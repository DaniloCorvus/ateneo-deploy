<?php

namespace Tests\Feature;

use App\Models\Call;
use App\Models\Cie10;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class Cie10ManagmentTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_save_call()
    {
        // $this->withoutExceptionHandling();
        // $response = $this->post('api/calls', [
        //     'llamada_id' => '1',
        //     'funcionario_id' => '1',
        //     'fecha' => Carbon::now(),
        //     'paciente_id' => '1',
        //     'tramite_id' => '1',
        //     'ambito_id' => '1',
        //     'servicio_id' => '1',
        //     'tipificacion_id' => '1',
        // ]);
        // $response->assertOk();
        // $call = Call::first();
        // $this->assertEquals('1', $call->paciente_id);
    }

    public function test_get_cie10s()
    {
        // $this->withoutExceptionHandling();
        // $response = $this->get('api/cie10s');
        // $this->assertCount(count(json_decode($response->getContent(), true)['data']), Cie10::all());
    }
}
