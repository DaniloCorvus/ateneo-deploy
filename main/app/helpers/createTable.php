<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\TypeAppointment;

// date_default_timezone_set('America/Bogota');
// $timestamp = (int)(preg_replace('/([^0-9]+)/', "", 1268524800000));
// dd([date('j-M-Y', $timestamp / 1000), date('c', $timestamp / 1000)]);

if (!function_exists('createTable')) {

    function createTable($customTable, $data)
    {

        if (!Schema::hasTable($customTable)) {

            Schema::create($customTable, function (Blueprint $table) use ($data, $customTable) {

                foreach ($data as $index =>  $item) {

                    if (gettype($item) != 'array'); {
                        $index = customSnakeCase($index);
                    }

                    switch (gettype($item)) {

                        case 'string':
                            if (strlen($item) > 200) {
                                $table->text($index);
                            } else {
                                $table->string($index);
                            }
                            break;
                        case 'boolean':
                            $table->boolean($index);
                            break;
                        case 'integer':

                            if ($index == 'Id') {
                                $table->unsignedBigInteger('$index')->primary();
                            } else {
                                $table->unsignedBigInteger($index);
                            }
                            break;

                        default:

                            if (gettype($item) == 'array') {

                                $table->unsignedBigInteger($index . '_id');
                                createTable($index, $item);
                            } else {

                                if (strlen($item) > 200) {
                                    $table->text($index);
                                } else {

                                    $table->string($index);
                                }
                            }
                            break;
                    }
                }
                $table->timestamps();
            });
        }
    }
}
