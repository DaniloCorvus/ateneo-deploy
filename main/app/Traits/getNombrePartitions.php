<?php

namespace App\Traits;

use Illuminate\Http\Response;

trait getNombrePartitions
{
    /**
     * Build a success response
     * @param  string|array $data
     * @param  int $code
     * @return Illuminate\Http\Response
     */

    public  $firstname = '';
    public  $middlename = '';
    public  $secondsurname = '';
    public  $surname = '';

    public function init($full_name)
    {
        $this->full_name = $full_name;

        $this->firstname = '';
        $this->middlename = '';
        $this->secondsurname = '';
        $this->surname = '';


        /* separar el nombre completo en espacios */
        $tokens = explode(' ', trim($this->full_name));
        /* arreglo donde se guardan las "palabras" del nombre */

        $names = array();

        /* palabras de apellidos (y nombres) compuetos */
        $special_tokens = array('da', 'de', 'del', 'la', 'las', 'los', 'mac', 'mc', 'van', 'von', 'y', 'i', 'san', 'santa');

        $prev = "";

        foreach ($tokens as $token) {
            $_token = strtolower($token);
            if (in_array($_token, $special_tokens)) {
                $prev .= "$token ";
            } else {
                $names[] = $prev . $token;
                $prev = "";
            }
        }

        $num_nombres = count($names);

        switch ($num_nombres) {
            case 0:
                break;
            case 1:
                $this->firstname = $names[0];
                break;
            case 2:
                $this->firstname   = explode(' ', $names[0])[0];
                $this->surname  = $names[1];
                break;
            case 3:
                $this->firstname = $names[0];
                $this->middlename = $names[1];
                $this->surname = $names[2];
                break;
            case 4:
                $this->firstname = $names[0];
                $this->middlename = $names[1];
                $this->surname = $names[2];
                $this->secondsurname = $names[3];
                break;
            default:
                $this->firstname = $names[0];
                $this->middlename =  $names[1];
                $this->surname = $names[2];
                unset($names[0]);
                unset($names[1]);
                unset($names[2]);
                $this->secondsurname = implode(' ', $names);
                break;
        }


        $this->firstname = mb_convert_case($this->firstname, MB_CASE_TITLE, 'UTF-8');
        $this->middlename = mb_convert_case($this->middlename, MB_CASE_TITLE, 'UTF-8');
        $this->surname = mb_convert_case($this->surname, MB_CASE_TITLE, 'UTF-8');
        $this->secondsurname = mb_convert_case($this->secondsurname, MB_CASE_TITLE, 'UTF-8');

        return [$this->firstname, $this->middlename,  $this->surname, $this->secondsurname];
    }
}
