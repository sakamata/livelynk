<?php

namespace Tests\Feature\app\Http\Controllers;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class WillGoTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testExample()
    {
        $controller = app()->make('\App\Http\Controllers\WillGoController');
        echo $controller->update($request =1);
        $this->assertTrue(true);
    }
}
