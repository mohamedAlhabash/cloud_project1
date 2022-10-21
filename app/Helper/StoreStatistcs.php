<?php
namespace App\Helper;

class StoreStatistics{

    public function set($data)
    {
        return [
            'num_items' => $data->num_items,
            'current_capacity' => $data->current_capacity,
            'requests_number' =>$data->requests_number,
            'miss_rate'=>$data->miss_rate,
            'hit_rate'=>$data->hit_rate,
        ];
    }
}
