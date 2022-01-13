<?php

namespace App\Traits;

trait Sorted
{

    public function scopeSortedByName($query, $order = 'Asc')
    {
        return $query->orderBy('name', $order);
    }
}
