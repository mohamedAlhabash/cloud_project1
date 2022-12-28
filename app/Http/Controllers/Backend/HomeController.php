<?php

namespace App\Http\Controllers\Backend;

use App\Models\Policy;
use App\Models\Attachment;
use App\Models\Statistics;
use App\Helper\CacheHelper;
use App\Helper\CloudWatchHelper;
use App\Helper\Ec2pool;
use App\Models\CacheConfig;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;

class HomeController extends Controller
{
    public $cache;
    public $capacity;
    public $replacment_policy_name;

    public function __construct()
    {
        $configration = CacheConfig::latest('id')->first();

        if ($configration) {
            $this->capacity = $configration->capacity;
            $this->replacment_policy_name = $configration->policy->policy_name;
            $this->cache = new CacheHelper((int) $this->capacity, $this->replacment_policy_name);
        } else {
            $this->capacity = 1000000;
            $this->replacment_policy_name = 'least recently used';
            $this->cache = new CacheHelper((int) $this->capacity, $this->replacment_policy_name);
        }

        session()->put('cache', $this->cache);
    }


    public function index()
    {
        // dd(session()->get('cache'));
        $msg = null;
        return view('backend.index', compact('msg'));
    }

    public function storeImage(Request $request)
    {
        $request->validate([
            'key' => 'required|string',
            'value' => 'required|mimes:jpeg,png|max:2048'
        ]);

        $attachment = Attachment::wherekey($request->key)->first();

        // attachment exists in DB
        if ($attachment) {
            $msg = null;
            $uploaded = null;
            if ($request->file('value')) {
                if ($attachment->value != null && File::exists('uploads/' . $attachment->value)) {
                    unlink('uploads/' . $attachment->value);
                }
                $uploaded = $this->uploadImage($request);
                $input['value'] = $uploaded['image_name'];
            }

            $attachment->update($input);

            //update cache
            $cachedItem = session()->get('cache');
            $cachedItem->add($request->key, $uploaded['image_name'], $uploaded['size']);
            session()->put('cahce', $cachedItem);
        } else {

            $input['key'] = $request->key;
            $uploaded = null;
            if ($request->file('value')) {
                $uploaded = $this->uploadImage($request);
                $input['value'] = $uploaded['image_name'];
            }

            Attachment::create($input);

            //add to cache
            $msg = null;
            $cachedItem = session()->get('cache');
            if ($cachedItem->size > $uploaded['size']) {
                $cachedItem->add($request->key, $uploaded['image_name'], $uploaded['size']);
                session()->put('cahce', $cachedItem);
            } else {
                $msg = 'size of image greater than cache capacity';
            }
        }
        return redirect()->route('index')->with([
            'msg' => $msg,
        ]);
    }

    private function uploadImage(Request $request)
    {
        $image = $request->file('value');

        $image_name = $request->key . "." . $image->getClientOriginalExtension();
        $size = $image->getSize();
        $image->move(public_path('uploads/'), $image_name);
        // Storage::disk('s3')->put($image_name, $image);
        return [
            'image_name' => $image_name,
            'size'       => $size,
        ];
    }


    public function image()
    {
        $attachment = null;
        $source = '';
        return view('backend.images', compact('attachment', 'source'));
    }

    public function getImage(Request $request)
    {
        $request->validate([
            'key' => 'required|exists:attachments,key'
        ]);

        $source = 'DB';

        $cachedItem = session()->get('cache');
        $attachment = $cachedItem->get($request->key);
        $cachedItem->requestCount++;

        $size_cache = strlen(base64_decode($attachment));
        $pos  = strpos($attachment, ';');
        $type = explode(':', substr($attachment, 0, $pos))[1];
        $type_cache = explode('/', $type);

        $path1 = Attachment::whereKey($request->key)->pluck('value')->first();
        $size = File::size(public_path('uploads/' . $path1));
        $type = pathinfo($path1, PATHINFO_EXTENSION);

        if ($attachment) {
            if ($size != $size_cache || $type_cache[1] != $type) {
                $cachedItem->add($request->key, $attachment, $size);
                session()->put('cache', $cachedItem);
            }

            $cachedItem->hitCount++;
            $source = 'Cache';
        } else {
            $cachedItem->missCount++;
            $attachment = Attachment::whereKey($request->key)->pluck('value')->first();
            $size = File::size(public_path('uploads/' . $attachment));
            $cachedItem = session()->get('cache');

            if ($cachedItem->size > $size) {
                $cachedItem->add($request->key, $attachment, $size);
                session()->put('cache', $cachedItem);
            }
        }
        return view('backend.images', compact('attachment', 'source'));
    }


    public function keys()
    {
        $attachments = Attachment::all();
        return view('backend.keys', compact('attachments'));
    }


    public function cacheConfig()
    {
        $policies = Policy::query()->get(['id', 'policy_name']);
        return view('backend.admin.cache-configiration', compact('policies'));
    }

    public function storeCacheConfig(Request $request)
    {
        $request->validate([
            'policy_id' => 'required',
            'capacity' => 'required|numeric'
        ]);

        $newConfigration = CacheConfig::create([
            'policy_id' => $request->policy_id,
            'capacity' => $request->capacity * 1000000
        ]);

        $cachedItem = session()->get('cache');

        $cachedItem->size = $newConfigration->capacity;
        while ($cachedItem->size < $cachedItem->items_size) {
            $cachedItem->replacementPolicies();
        }
        $cachedItem->replacment_policy = $this->replacment_policy_name;
        session()->put('cache', $cachedItem);

        return redirect()->route('cache-config');
    }


    public function clearCache()
    {
        $cachedItem = session()->get('cache');
        $cachedItem->clearCache();
        session()->put('cache', $cachedItem);

        return ['message' => 'Cache is cleared'];
    }

    public function storeCacheStatus(Request $request)
    {
        $cachedItem = session()->get('cache');

        $cloudWatch = new CloudWatchHelper();
        $cloudWatch->putTheMetricData('miss_rate', $cachedItem->missRate());
        $cloudWatch->putTheMetricData('hit_rate', $cachedItem->hitRate());
        $cloudWatch->putTheMetricData('request_served', $cachedItem->requestCount);
        $cloudWatch->putTheMetricData('num_items', count($cachedItem->items));
        $cloudWatch->putTheMetricData('current_capacity', $cachedItem->items_size);
        
        return 'data stored in metric successfully';
    }

    public function poolResizing()
    {
        $ec2 = new Ec2pool();
        $ec2Count = $ec2->get_number_of_instances();
        $statistics = Statistics::latest()->first();
        return view('backend.admin.pool-resizing', compact('ec2Count', 'statistics'));
    }

    public function increasePools(Request $request)
    {
        $ec2 = new Ec2pool();
        $ec2->create_instance_from_Image();
        return $ec2->get_number_of_instances();
    }

    public function decreasePools(Request $request)
    {
        $ec2 = new Ec2pool();
        $ec2->terminate_instance();
        return $ec2->get_number_of_instances();
    }



    public function statistics()
    {
        // $statistics = Statistics::latest()->take(120)->get();
        return view(
            'backend.admin.statistics1',
            [
                // 'statistics' => $statistics,
                'MissRate' => $this->getMissRateStatisics(),
                'HitRate' => $this->getHitRateStatisics(),
                'TotalItemSize' => $this->getTotalItemSizeStatisics(),
                'CountRequests' => $this->getCountRequestsStatisics(),
                'NumberOfItems' => $this->getNumberOfItemsStatisics(),
                'getTimeStamp' => $this->getTimeStamp(),
            ]
        );
    }

    public function scale()
    {
        $ec2 = new Ec2pool();
        $ec2Count = $ec2->get_number_of_instances();
        $statistics = Statistics::latest()->first();
        return view('backend.admin.pool-resizing', compact('ec2Count', 'statistics'));
    }

    public function autoScalling(Request $request)
    {
        $result = $this->getSpecifecStatistics('miss_rate');
        $ec2 = new Ec2pool();

        $cloud_miss_rate = end($result);

        $numberOfInstances = $ec2->get_number_of_instances();

        Statistics::create([
            'max_miss_rate' => $request->max_miss,
            'min_miss_rate' => $request->min_miss,
        ]);

        if($request->max_miss < $cloud_miss_rate) {
            for ($i = 0; $i < 2; $i++) {
                $this->increasePools($request);
            }
            return 'Increase to ' . $numberOfInstances + 2 . ' pools';

        } else if($request->min_miss > $cloud_miss_rate) {
            for ($i = 0; $i < $numberOfInstances / 2; $i++) {
                $this->decreasePools($request);
            }
            return 'Decrease to ' . $numberOfInstances / 2 . ' pools';

        } else {
            return 'nothin to do!';
        }
    }


    protected function getSpecifecStatistics($name)
    {
        $cloudWatch = new CloudWatchHelper();
        $res = $cloudWatch->getTheMetricStatistics($name, 30);

        usort($res, function ($a, $b) {
            return strcmp($a['Timestamp'], $b['Timestamp']);
        });
        $values = [];
        foreach ($res as $item) {
            array_push($values, $item['Average']);
        }
        return $values;
    }

    protected function getTimeStampStatistics($name)
    {
        $cloudWatch = new CloudWatchHelper();
        $res = $cloudWatch->getTheMetricStatistics($name, 30);
        usort($res, function ($a, $b) {
            return strcmp($a['Timestamp'], $b['Timestamp']);
        });
        $timeStamps = [];
        foreach ($res as $item) {
            array_push(
                $timeStamps,
                $item['Timestamp']
            );
        }
        return $timeStamps;
    }

    public function getMissRateStatisics()
    {
        return $this->getSpecifecStatistics('miss_rate');
    }

    public function getHitRateStatisics()
    {
        return $this->getSpecifecStatistics('hit_rate');
    }

    public function getTotalItemSizeStatisics()
    {
        return $this->getSpecifecStatistics('current_capacity');
    }

    public function getCountRequestsStatisics()
    {
        return $this->getSpecifecStatistics('request_served');
    }

    public function getNumberOfItemsStatisics()
    {
        return $this->getSpecifecStatistics('num_items');
    }

    public function getTimeStamp()
    {
        return $this->getTimeStampStatistics('miss_rate');
    }
}
