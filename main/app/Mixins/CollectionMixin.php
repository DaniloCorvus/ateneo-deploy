<?php

namespace App\Mixins;

use Illuminate\Support\Facades\Response;
use Illuminate\Http\Response as Status;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Arr;

class CollectionMixin
{
    /**
     * @return \Closure
     */

    // Collection::macro('toUpper', function () {
    //     return $this->map(function ($value) {
    //         return Str::upper($value);
    //     });
    // });

    public function toUpper()
    {
        return function () {
            return  $this->map(function ($value) {
                return Str::upper($value);
            });
        };
    }
}
