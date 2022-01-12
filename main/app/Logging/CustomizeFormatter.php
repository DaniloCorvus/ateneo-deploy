<?php

namespace App\Logging;

use App\Models\Person;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\SlackWebhookHandler;

class CustomizeFormatter
{
    /**
     * Customize the given logger instance.
     *
     * @param  \Illuminate\Log\Logger  $logger
     * @return void
     */
    public function __invoke($logger)
    {
        $dateFormat = "Y-m-d H:i:s";
        $checkLocal = env('APP_ENV');

        foreach ($logger->getHandlers() as $handler) {


            if ($handler instanceof SlackWebhookHandler) {

                // $output = "[$checkLocal]: %datetime% > %level_name% - %message%` :poop: \n";
                // $formatter = new LineFormatter($output, $dateFormat);
                // $handler->setFormatter($formatter);

                // $handler->pushProcessor(function ($record) {
                //     $person =  Person::firstWhere('identifier', auth()->user()->person_id);
                //     $record['extra']['Usuario'] = $person->first_name . ' ' .   $person->first_surname  . ' CC : ' . $person->identifier;
                //     return $record;
                // });
            }
        }
    }
}
