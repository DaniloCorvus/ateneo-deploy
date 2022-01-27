<?php

namespace Tests\Feature;

use App\Models\Patient;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Tests\TestCase;

class PatientManagmentTest extends TestCase
{
    public function test_save_patients()
    {

        $this->withoutExceptionHandling();
        $response = $this->post('api/patients', [
            'Tipo_Identificacion' => '1',
            'Identificacion' => '1096216530',
            'Nombres' => 'Danilo',
            'Apellidos' => 'Grisalez',
            // 'Gender' => 'Femenino',
            'Date_of_Birth' => Carbon::now(),
            'EPS' => 'Medimas',
            'Contract_id' => '1',
            'Regimen' => '1',
            'Pais' => 'Colombia',
            'Departamento' => 'Bogota',
            'Ciudad' => 'Bogota',
            'Direccion' => 'Cl 10 ',
            'Correo' => 'angellphp@gmail.com',
            'Telefono' => '6100781',
            'Celular' => '3172603279',
            'Estado' => 'Activo',
            'Nivel' => '1',
            'ips_principal' => 'Una  eps',
            'IPS' => '1',
            'Regional' => '1',
            'Sede' => 'Una sede',
            'database' => 'medimas'
        ]);

        $response->assertOk();
        $Patient = Patient::firstWhere('Identificacion', 1096216530);
        $this->assertInstanceOf(Patient::class, $Patient);
        $this->assertEquals('1096216530', $Patient->Identificacion);
    }
    public function test_update_patients()
    {

        $this->withoutExceptionHandling();
        $response = $this->post('api/patients/62?_method=put', [
            'Tipo_Identificacion' => '1',
            'Identificacion' => '1096216530',
            'Nombres' => 'Danilox',
            'Apellidos' => 'Grisalez',
            // 'Gender' => 'Femenino',
            'Date_of_Birth' => Carbon::now(),
            'EPS' => 'Medimas',
            'Contract_id' => '1',
            'Regimen' => '1',
            'Pais' => 'Colombia',
            'Departamento' => 'Bogota',
            'Ciudad' => 'Bogota',
            'Direccion' => 'Cl 10 ',
            'Correo' => 'angellphp@gmail.com',
            'Telefono' => '6100781',
            'Celular' => '3172603279',
            'Estado' => 'Activo',
            'Nivel' => '1',
            'ips_principal' => 'Una  eps',
            'IPS' => '1',
            'Regional' => '1',
            'Sede' => 'Una sede',
            'database' => 'medimas'
        ]);

        $response->assertOk();
        $Patient = Patient::firstWhere('Identificacion', 1096216530);
        $this->assertInstanceOf(Patient::class, $Patient);
        $this->assertEquals('1096216530', $Patient->Identificacion);
    }

    public function test_get_patients()
    {
        $this->withoutExceptionHandling();
        $response = $this->get('api/patients');
        $this->assertCount(count(json_decode($response->getContent(), true)['data']), Patient::all());
    }
}
