<?php

namespace Tests\Feature;

use App\Menu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testBasicTest()
    {

        $this->withoutExceptionHandling();
        $response = $this->post('api/menus', [
            'menu_ids' => array(1 => array(1, 2, 3), 2 => array(1, 2, 3), 4 => array(1, 2, 3), 5 => array(1, 2, 3)),
            'usuario_id' => 1,
        ]);

        $response->assertOk();
        $response->assertStatus(200);
    }
}
