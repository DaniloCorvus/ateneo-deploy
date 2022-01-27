<?php

namespace Tests\Feature;

use App\Agendamiento;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class TipificacionManagmentTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_save_datail_call()
    {

        $this->withoutExceptionHandling();
        $response = $this->post('api/typings', [
            'type_agenda_id' => '1',
            'type_appointment_id' => '1',
            'ips_id' => '1',
            'eps_id' => '1',
            'speciality_id' => '1',
            'person_id' => '1',
            'date_start' => Carbon::parse('27-06-2021'),
            'date_end' => Carbon::parse('27-06-2021'),
            'long' => '5',
            'hour_start' => '5:00',
            'hour_end' => '15:00',
            'days' => array('1', '3', '5'),
            'pending' => 0,
        ]);

        $response->assertOk();
        $agendamiento = Agendamiento::first();
        $this->assertEquals('1', $agendamiento->person_id);
    }
}
