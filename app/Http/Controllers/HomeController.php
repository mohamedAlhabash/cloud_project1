<?php

namespace App\Http\Controllers;

use App\Helper\CacheHelper;
use App\Models\Attachment;
use App\Models\CacheConfig;
use App\Models\Policy;
use App\Models\Statistics;
use Illuminate\Http\Request;
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
        return view('backend.index');
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
            $cachedItem = session()->get('cache');
            $cachedItem->add($request->key, $uploaded['image_name'], $uploaded['size']);
            session()->put('cahce', $cachedItem);
        }
        return redirect(route('index'));
    }

    private function uploadImage(Request $request)
    {
        $image = $request->file('value');

        $image_name = $request->key . "." . $image->getClientOriginalExtension();
        $size = $image->getSize();
        $image->move(public_path('uploads/'), $image_name);
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

        if ($attachment) {
            $cachedItem->hitCount++;
            $source = 'Cache';
        } else {
            $cachedItem->missCount++;
            $attachment = Attachment::whereKey($request->key)->pluck('value')->first();
            $size = File::size(public_path('uploads/' . $attachment));
            $cachedItem->add($request->key, $attachment, $size);
            session()->put('cache', $cachedItem);
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
        return view('backend.cache-setting', compact('policies'));
    }

    public function storeCacheConfig(Request $request)
    {
        $request->validate([
            'policy_id' => 'required',
            'capacity' => 'required'
        ]);

        $newConfigration = CacheConfig::create([
            'policy_id' => $request->policy_id,
            'capacity' => $request->capacity * 1000000
        ]);

        $cachedItem = session()->get('cache');

        $cachedItem->size = $newConfigration->capacity;
        $cachedItem->replacment_policy = $this->replacment_policy_name;
        session()->put('cache', $cachedItem);

        return redirect()->route('cache-config');
    }


    public function cacheStatus()
    {
        $cachedItem = Statistics::latest('id')->where('check_time', true)->first();
        return view('backend.statistics', [
            'num_items' => $cachedItem ? $cachedItem->num_items : 0,
            'hit_rate'  => $cachedItem ? $cachedItem->hit_rate : 0,
            'miss_rate'  => $cachedItem ? $cachedItem->miss_rate : 0,
            'current_capacity' => $cachedItem ? $cachedItem->current_capacity : 0,
            'replacment_policy' => $this->replacment_policy_name,
        ]);
    }

    public function storeCacheStatus()
    {
        $cachedItem = session()->get('cache');
        $statiscts = Statistics::create([
            'num_items' => count($cachedItem->items),
            'current_capacity' => $cachedItem->items_size,
            'requests_number' => $cachedItem->requestCount,
            'miss_rate'  => $cachedItem->missRate(),
            'hit_rate'  => $cachedItem->hitRate(),
        ]);
        return $statiscts;
    }


    public function clearCache()
    {
        $cachedItem = session()->get('cache');
        $cachedItem->clearCache();
        session()->put('cache', $cachedItem);

        return ['message' => 'Cache is cleared'];
    }
}
