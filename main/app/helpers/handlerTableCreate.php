<?php

if (!function_exists('handlerTableCreate')) {

    function handlerTableCreate($data)
    {
        $count = 0;

        foreach ($data as $index => $item) {
            $count++;
            if (gettype($item) != 'array') {
                dd('necesitas un array');
            } else {

                foreach ($item as $index => $value) {

                    if ($index != 'EPSs' && $index != 'Interface' && $index != 'Parent' && $index != 'Regional' && $index != 'Institution' && $index != 'Roles' && $index != 'Specialities') {
                        if (gettype($value) == 'array') {
                            createTable(customSnakeCase($index), $value);
                        } else {
                            createTable('default_table', $item);
                        }
                    }
                }
                
            }
        }
    }
}
