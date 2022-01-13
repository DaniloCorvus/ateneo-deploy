<?php

namespace App\Clases;

class stdObject
{
    public function __construct(array $arguments = array())
    {
        if (!empty($arguments)) {
            foreach ($arguments as $property => $argument) {
                $this->{$property} = $argument;
            }
        }
    }
}
