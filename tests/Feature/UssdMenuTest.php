<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Voter;

class UssdMenuTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
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

    /** @test */
    public function test_it_navigates_to_the_main_menu()
    {
        $response = $this->post('/api/ussd', [
            'text' => '1',
            'phoneNumber' => '254712345678',
            'sessionId' => 'session_001'
        ]);

        $response->assertSeeText("1. Education");
        $response->assertSeeText("2. Health");
    }

    /** @test */
    public function test_it_returns_to_main_menu_when_back_is_pressed()
    {
        $response = $this->post('/api/ussd', [
            'text' => '1*1*0',
            'phoneNumber' => '254712345678',
            'sessionId' => 'session_001'
        ]);

        $response->assertSeeText("1. Education");
    }

    /** @test */
    public function test_it_registers_a_voter_and_prevents_duplicates()
    {
        $phoneNumber = '254700000000';

        // First registration
        $this->post('/api/ussd', [
            'text' => "1*2*3*Alphayo",
            'phoneNumber' => $phoneNumber,
            'sessionId' => 'session_1'
        ]);

        // Second registration with same phone but different name
        $this->post('/api/ussd', [
            'text' => "1*2*3*Alphayo Updated",
            'phoneNumber' => $phoneNumber,
            'sessionId' => 'session_2'
        ]);

        // Assert: Only one record exists for this phone number
        $this->assertEquals(1, Voter::where('phone', $phoneNumber)->count());
        $this->assertDatabaseHas('voters', [
            'phone' => $phoneNumber,
            'name' => 'Alphayo Updated'
        ]);
    }

    /** @test */
    public function test_it_handles_empty_name_input_gracefully()
    {
        $phoneNumber = '254799999999';

        $response = $this->post('/api/ussd', [
            'text' => "1*2*3*", // User sent empty name
            'phoneNumber' => $phoneNumber,
            'sessionId' => 'sess_empty'
        ]);

        // Assert database still caught the phone number
        $this->assertDatabaseHas('voters', ['phone' => $phoneNumber]);

        // Assert the user got a polite "supporter" response instead of a blank name
        $response->assertSeeText("Thank you valued supporter");
    }
}
