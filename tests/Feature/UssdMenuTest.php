<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class UssdMenuTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_shows_the_welcome_screen_on_first_dial()
    {
        $response = $this->post('/api/ussd', [
            'text' => '',
            'phoneNumber' => '254712345678',
            'sessionId' => 'session_001'
        ]);

        $response->assertStatus(200);
        $response->assertSeeText("CON Welcome");
    }

    public function test_it_navigates_to_the_main_menu()
    {
        $response = $this->post('/api/ussd', [
            'text' => '1',
            'phoneNumber' => '254712345678',
            'sessionId' => 'session_001'
        ]);

        $response->assertSeeText("1. Education");
    }

    public function test_it_returns_to_main_menu_when_back_is_pressed()
    {
        $response = $this->post('/api/ussd', [
            'text' => '1*1*0',
            'phoneNumber' => '254712345678',
            'sessionId' => 'session_001'
        ]);

        $response->assertSeeText("1. Education");
    }

    public function test_it_registers_a_voter_in_the_database()
    {
        $phoneNumber = '254700000000';
        $name = 'Alphayo';

        $response = $this->post('/api/ussd', [
            'text' => "1*2*3*$name",
            'phoneNumber' => $phoneNumber,
            'sessionId' => 'session_reg_123'
        ]);

        $this->assertDatabaseHas('voters', [
            'phone' => $phoneNumber,
            'name' => $name
        ]);
    }
}
