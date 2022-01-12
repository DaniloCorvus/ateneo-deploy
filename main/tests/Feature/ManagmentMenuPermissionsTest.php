<?php

namespace Tests\Feature;

use App\Menu;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ManagmentMenuPermissionsTest extends TestCase
{
     /**
     * A basic test example.
     *
     * @return void
     */

    public function testSaveMenu()
    {
        $this->withoutExceptionHandling();
        $response = $this->post('api/menus', [
            'menu_ids' => array('1', '3', '5'),
            'usuario_id' => 1,
        ]);

        $response->assertOk();
        $menu = Menu::first();
        $this->assertEquals('1', $menu->person_id);
    }

    public function test_get_menus()
    {
        $this->withoutExceptionHandling();
    }
}
