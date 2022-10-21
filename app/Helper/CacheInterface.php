<?php

declare(strict_types=1);

namespace App\Helper;

interface CacheInterface
{
    public function add($key, $value, $item_ize);
    public function get($key);
}
