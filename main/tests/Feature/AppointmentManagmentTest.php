<?php

namespace Tests\Feature;

use App\Models\Appointment;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class AppointmentManagmentTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    // public function test_save_appointment()
    // {

    //     $this->withoutExceptionHandling();
    //     $response = $this->post('api/appointments', [
    //         'diagnostico' => 'diarrea cevera',
    //         'space_id' => '1',
    //         'call_id' => '1',
    //         'person_id' => '1',
    //         'ips_id' => '1',
    //         'speciality_id' => '1',
    //         'date' => Carbon::parse('30-06-2021'),
    //         'origin' => 'Origen del evento',
    //         'scope' => 'Ambito',
    //         'contract_id' => '1',
    //         'procedure_id' => '1',
    //         'price' => '50000',
    //         'observation' => 'una observacion muy cool'
    //     ]);

    //     $response->assertOk();
    //     $Appointment = Appointment::first();
    //     $this->assertEquals('1', $Appointment->person_id);
    // }

    // public function test_get_appointments()
    // {
    //     $this->withoutExceptionHandling();
    //     $response = $this->get('api/Appointments');
    //     $this->assertCount(count(json_decode($response->getContent(), true)['data']), Appointment::all());
    // }
}
