<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Voter;

class UssdMenuTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function test_it_handles_wasiliana_initial_dial_normalization()
    {
        $response = $this->post('/api/ussd', ['text' => '2222', 'phoneNumber' => '254712345678', 'sessionId' => 's1']);
        $response->assertSeeText("CON Welcome");
    }

    /** @test */
    public function test_it_navigates_to_the_main_menu()
    {
        $response = $this->post('/api/ussd', ['text' => '1', 'phoneNumber' => '254712345678', 'sessionId' => 's1']);
        $response->assertSeeText("1. Education");
        $response->assertSeeText("2. Health");
    }

    /** @test */
    public function test_education_pagination_flow_is_complete()
    {
        // Efficiency Path (1) through all 3 levels
        $this->post('/api/ussd', ['text' => '1*1*1'])->assertSeeText("Saving Time");
        $this->post('/api/ussd', ['text' => '1*1*1*1'])->assertSeeText("24/7 Accessibility");
        $this->post('/api/ussd', ['text' => '1*1*1*1*1'])->assertSeeText("Status Tracking");
    }

    /** @test */
    public function test_transparency_path_navigation()
    {
        // Transparency Path (2)
        $this->post('/api/ussd', ['text' => '1*1*2'])->assertSeeText("Eliminating Favoritism");
    }

    /** @test */
    public function test_request_support_flow()
    {
        // Show menu
        $this->post('/api/ussd', ['text' => '1*2*5'])->assertSeeText("Medical/Maternal");
        // Ask for ID
        $this->post('/api/ussd', ['text' => '1*2*5*1'])->assertSeeText("Please enter your ID number");
    }

    /** @test */
    public function test_it_registers_a_voter_and_updates_gracefully()
    {
        $phoneNumber = '254700000000';
        $this->post('/api/ussd', ['text' => "1*2*3*Alphayo", 'phoneNumber' => $phoneNumber]);
        $this->post('/api/ussd', ['text' => "1*2*3*Alphayo Updated", 'phoneNumber' => $phoneNumber]);

        $this->assertEquals(1, Voter::where('phone', $phoneNumber)->count());
        $this->assertDatabaseHas('voters', ['name' => 'Alphayo Updated']);
    }

    /** @test */
    public function test_it_handles_empty_name_input_gracefully()
    {
        $response = $this->post('/api/ussd', ['text' => "1*2*3*", 'phoneNumber' => '254799999999']);
        $response->assertSeeText("Thank you valued supporter");
    }
}
