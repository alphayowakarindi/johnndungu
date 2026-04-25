<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Voter;

class UssdController extends Controller
{
    public function handle(Request $request)
    {
        // 1. Clean Input
        $text = trim($request->input('text', ''));
        $phoneNumber = $request->input('phoneNumber');
        $sessionId = $request->input('sessionId');

        // Normalize Wasiliana initial dial (2222)
        if ($text === "2222") {
            $text = "";
        }

        $details = !empty($text) ? explode('*', $text) : [];
        $level = count($details);

        $response = "";

        // --- THE LOGIC ROUTER ---

        // Level 0: Welcome Screen
        if ($level == 0) {
            $response = "CON Welcome, my name is John Ndung'u Kamau, a young aspiring MP for Roysambu. Let's go digital!\n\n";
            $response .= "Ni JONTEH FRESH na IDEAS FRESH za Roysambu Fresh.\n\n";
            $response .= "1. Next";
        }

        // Level 1: Main Menu (Reached via '1' from Welcome or '0' from submenus)
        elseif (($level == 1 && $details[0] == "1") || (end($details) === "0" && $level > 0)) {
            $response = "CON 1. Education - Online bursary application\n";
            $response .= "2. Health - RoysAfya care\n";
        }

        // --- BRANCH 1: EDUCATION ---
        elseif ($level >= 2 && $details[1] == "1") {

            // LEVEL 2: Education Sub-Menu
            if ($level == 2) {
                $response = "CON 1. Efficiency for parents\n";
                $response .= "2. Transparency and Fairness\n";
                $response .= "0. Back";
            }

            // LEVEL 3: PAGE 1 (First Bullet Point)
            elseif ($level == 3) {
                $eduChoice = $details[2];

                if ($eduChoice == "1") {
                    // Your exact text: Saving Time
                    $response = "CON Saving Time: Parents won't have to miss work or close their businesses to stand in long queues at a physical office.\n";
                    $response .= "1. More\n0. Back";
                } elseif ($eduChoice == "2") {
                    // Your exact text: Eliminating Favoritism
                    $response = "CON Eliminating Favoritism: By using a digital system, you remove the middlemen who often give bursaries to friends or relatives.\n";
                    $response .= "1. More\n0. Back";
                } else {
                    $response = "CON Invalid choice.\n0. Back";
                }
            }

            // LEVEL 4: PAGE 2 (Second Bullet Point)
            elseif ($level == 4) {
                $eduChoice = $details[2];
                $more1 = end($details);

                if ($more1 == "1") {
                    if ($eduChoice == "1") {
                        // Your exact text: 24/7 Accessibility
                        $response = "CON 24/7 Accessibility: They can apply from their phones at any time, even late at night after work.\n";
                        $response .= "1. More\n0. Back";
                    } elseif ($eduChoice == "2") {
                        // Your exact text: Accountability
                        $response = "CON Accountability: The system creates a clear digital trail of who applied, who qualified, and why, preventing the sale of bursary slots.\n";
                        $response .= "1. More\n0. Back";
                    }
                } else {
                    $response = "CON Invalid choice.\n0. Back";
                }
            }

            // LEVEL 5: PAGE 3 (Third Bullet Point)
            elseif ($level == 5) {
                $eduChoice = $details[2];
                $more2 = end($details);

                if ($more2 == "1") {
                    if ($eduChoice == "1") {
                        // Your exact text: Status Tracking
                        $response = "CON Status Tracking: Instead of wondering if their application was lost, parents can see the progress of their application in real-time.\n";
                        $response .= "0. Back";
                    } elseif ($eduChoice == "2") {
                        // Your exact text: Reaching the Needy
                        $response = "CON Reaching the Needy: It ensures that the funds actually reach those who need them most, rather than those with the best political connections.\n";
                        $response .= "0. Back";
                    }
                } else {
                    $response = "CON Invalid choice.\n0. Back";
                }
            }
        }

        // Branch 2: Health
        elseif ($level >= 2 && $details[1] == "2") {
            if ($level == 2) {
                $response = "CON Welcome to RoysAfya Care\n";
                $response .= "One Man, One Shilling, One Healthy Roysambu\n\n";
                $response .= "1. Benefits Info\n";
                $response .= "2. Contribution (Ksh 1)\n";
                $response .= "3. Register\n";
                $response .= "4. My Status\n";
                $response .= "5. Request Support\n";
                $response .= "0. Back";
            } elseif ($level == 3) {
                $healthChoice = $details[2];

                if ($healthChoice == "1") {
                    $response = "CON RoysAfya Benefits\n";
                    $response .= "* Medical & Maternal\n";
                    $response .= "* Accident Coverage\n";
                    $response .= "* Funeral Support\n\n";
                    $response .= "1. More\n0. Back";
                } elseif ($healthChoice == "3") {
                    $response = "CON Please enter your full name:";
                }
                // --- ADDED THIS SECTION HERE ---
                elseif ($healthChoice == "5") {
                    $response = "CON 1. Medical/Maternal\n";
                    $response .= "2. Accident/Emergency\n";
                    $response .= "3. Family Shopping (Breadwinner Ill)\n";
                    $response .= "4. Funeral Support\n";
                    $response .= "5. Back";
                } else {
                    $response = "CON Coming soon.\n0. Back";
                }
            } elseif ($level == 4) {
                $healthChoice = $details[2];
                $input = end($details);

                if ($healthChoice == "1" && $input == "1") {
                    $response = "CON Hospital Shopping: 1 month food supply if breadwinner is admitted\n\n0. Back";
                }
                // --- ADDED THIS SECTION TO HANDLE THE SUPPORT CHOICE ---
                elseif ($healthChoice == "5") {
                    // User picked a support category (1, 2, 3, or 4)
                    if ($input == "5") {
                        // Logic for "5. Back" - sends them back to the Health Menu
                        $response = "CON Welcome to RoysAfya Care\n";
                        $response .= "1. Benefits Info\n2. Contribution (Ksh 1)\n3. Register\n4. My Status\n5. Request Support\n0. Back";
                    } else {
                        $response = "CON Please enter your ID number or location for follow-up:";
                    }
                } elseif ($healthChoice == "3") {
                    $name = $input;
                    $displayName = empty($name) ? "valued supporter" : $name;

                    Voter::updateOrCreate(
                        ['phone' => $phoneNumber],
                        ['name' => $name, 'session_id' => $sessionId]
                    );

                    $response = "END Thank you $displayName! Registered successfully.";
                }
            }
        }

        // Catch-all
        else {
            $response = "CON Invalid selection.\n1. Education\n2. Health";
        }

        return response($response)->header('Content-Type', 'text/plain');
    }
}
