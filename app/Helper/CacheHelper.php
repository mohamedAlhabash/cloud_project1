<?php

declare(strict_types=1);

namespace App\Helper;

use InvalidArgumentException;


class CacheHelper implements CacheInterface
{
    public array $items = [];
    public int $size;
    public int $items_size = 0;
    public float $hitCount = 0;
    public float $missCount = 0;
    public int $requestCount = 0;
    public $replacment_policy;

    public function __construct(int $size, $replacment_policy)
    {
        if ($size < 0) {
            throw new InvalidArgumentException('Cache size must be greater than 0');
        }

        $this->size = $size;
        $this->replacment_policy = $replacment_policy;
    }

    public function add($key, $item, $item_size): void
    {
        $item_encode = $this->encodeImage($item);

        $this->items_size += $item_size;

        while ($this->size < $this->items_size) {
            $this->replacementPolicies();
        }

        // already on the list
        if (isset($this->items[$key])) {
            $old = $this->items[$key];
            $oldSize = strlen(base64_decode($old));
            $this->items[$key] = $item_encode;
            $this->moveToFront($key);
            $this->items_size -= $oldSize;

            return;
        }

        $this->items[$key] = $item_encode;
    }

    private function encodeImage($item)
    {
        $path = public_path('uploads/' . $item);
        $type = pathinfo($path, PATHINFO_EXTENSION);
        $data = file_get_contents($path);
        $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
        return $base64;
    }

    public function get($key)
    {
        if (false === isset($this->items[$key])) {
            return null;
        }

        $this->moveToFront($key);

        return $this->items[$key];
    }

    private function moveToFront(string $key): void
    {
        $cachedItem = $this->items[$key];

        unset($this->items[$key]);

        $this->items[$key] = $cachedItem;
    }

    public function replacementPolicies()
    {
        switch ($this->replacment_policy) {
            case 'least recently used':
                reset($this->items);

                $oldItem = $this->items[key($this->items)];
                $oldItemSize = strlen(base64_decode($oldItem));
                $this->items_size -= $oldItemSize;

                unset($this->items[key($this->items)]);
                break;

            case 'random replacement':
                $replacment_key = array_rand($this->items);

                $oldItem = $this->items[$replacment_key];
                $oldItemSize = strlen(base64_decode($oldItem));
                $this->items_size -= $oldItemSize;

                unset($this->items[$replacment_key]);
                break;

            default:
                # code...
                break;
        }
    }

    public function clearCache()
    {
        $this->items_size = 0; //1
        $this->replacment_policy = null; //2
        $this->size = 0;
        $this->hitCount = 0;
        $this->missCount = 0;
        $this->requestCount = 0;
        foreach ($this->items as $key => $value) {
            unset($this->items[$key]);
        }

        session()->forget('cache');
    }

    public function hitRate()
    {
        return $this->requestCount == 0 ? 0 : ($this->hitCount / $this->requestCount) * 100.0;
    }

    public function missRate()
    {
        return $this->requestCount == 0 ? 0 : ($this->missCount / $this->requestCount) * 100.0;
    }
}
