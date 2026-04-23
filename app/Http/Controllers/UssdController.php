<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Voter;

class UssdController extends Controller
{
    public function handle(Request $request)
    {
        $text = $request->input('text');
        $phoneNumber = $request->input('phoneNumber');
        $details = explode('*', $text);
        $level = (empty($text)) ? 0 : count($details);

        // SCREEN 1: Welcome & Intro
        if ($level == 0) {
            $response = "CON Welcome, my name is John Ndung'u Kamau, a young aspiring MP for Roysambu constituency.\n\n";
            $response .= "Ni JONTEH FRESH na IDEAS FRESH za Roysambu\n\n";
            $response .= "1. Next";
        }

        // SCREEN 2: The Main Menu
        // We check if the last thing they pressed was '0' (Back) to return them here
        elseif ($level == 1 && $details[0] == "1" || end($details) === "0") {
            $response = "CON 1. Education — Online bursary application\n";
            $response .= "2. Health — RoysAfya care\n";
            $response .= "3. Reporting an issue";
        }

        // --- BRANCH 1: EDUCATION ---
        elseif ($level >= 2 && $details[1] == "1") {
            // If they just arrived at Education menu
            if ($level == 2) {
                $response = "CON 1. Efficiency for parents\n";
                $response .= "2. Transparency and Fairness\n";
                $response .= "0. Back";
            }
            // If they picked an Education sub-option
            elseif ($level == 3) {
                $eduChoice = $details[2];
                if ($eduChoice == "1") {
                    $response = "CON * Saving Time: Parents won't have to miss work to stand in long queues.\n";
                    $response .= "* 24/7 Accessibility: Apply from phones at any time.\n";
                    $response .= "\n0. Back";
                } elseif ($eduChoice == "2") {
                    $response = "CON * Eliminating Favoritism: Remove middlemen who give to friends.\n";
                    $response .= "* Accountability: Clear digital trail prevents 'sale' of slots.\n";
                    $response .= "\n0. Back";
                } else {
                    $response = "CON Invalid choice.\n0. Back to Education Menu";
                }
            }
        }

        // --- BRANCH 2: HEALTH ---
        elseif ($level >= 2 && $details[1] == "2") {
            if ($level == 2) {
                $response = "CON Welcome to RoysAfya Care\n";
                $response .= "1. Benefits Info\n2. Contribution (Ksh 1)\n3. Register/Join\n4. My Status\n5. Request Support\n0. Back";
            } elseif ($level == 3) {
                $healthChoice = $details[2];
                if ($healthChoice == "1") {
                    $response = "CON RoysAfya Benefits:\n* Medical & Maternal\n* Accident Coverage\n* Funeral Support\n\n0. Back";
                } elseif ($healthChoice == "3") {
                    $response = "CON Please enter your full name to Register/Join RoysAfya Care:";
                } else {
                    $response = "END This service will be available soon. Thank you!\n0. Back";
                }
            } elseif ($level == 4 && $details[2] == "3") {
                $name = end($details);
                Voter::updateOrCreate(
                    ['phone' => $phoneNumber],
                    [
                        'name' => $name,
                        'session_id' => $request->input('sessionId') // Grabbed from Wasiliana's request
                    ]
                );
                $response = "END Thank you $name! You have successfully registered.";
            }
        }

        // --- BRANCH 3: REPORTING AN ISSUE ---
        elseif ($level >= 2 && $details[1] == "3") {
            if ($level == 2) {
                $response = "CON Select an issue:\n";
                $response .= "1. Street Lights\n2. Insecurity\n3. Illegal Dumping\n4. Water Leakage\n5. Noise Pollution\n6. Road & Drainage\n7. Others\n0. Back";
            } elseif ($level == 3) {
                $issueType = $details[2];
                $issueLabels = ["1" => "Street Lights", "2" => "Insecurity", "3" => "Dumping", "4" => "Water", "5" => "Noise", "6" => "Roads"];

                if ($issueType == "7") {
                    $response = "CON Please describe the issue:";
                } elseif (isset($issueLabels[$issueType])) {
                    $label = $issueLabels[$issueType];
                    $response = "END Thank you for reporting $label. Ideas Fresh!";
                } else {
                    $response = "CON Invalid choice.\n0. Back";
                }
            } elseif ($level == 4 && $details[2] == "7") {
                $customIssue = end($details);
                $response = "END Thank you. Your report on '$customIssue' has been submitted.";
            }
        }

        // --- SMART CATCH-ALL (Cost-Saving) ---
        else {
            // Instead of END, we show the Main Menu with an error
            $response = "CON Invalid selection. Please try again:\n";
            $response .= "1. Education\n2. Health\n3. Reporting an issue";
        }

        return response($response)->header('Content-Type', 'text/plain');
    }
}
