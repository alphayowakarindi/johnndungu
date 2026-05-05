<?php

namespace App\Http\Controllers;

use App\Models\Voter;
use Illuminate\Http\Request;

class UssdController extends Controller
{
    public function handle(Request $request)
    {
        // 1. Clean Input
        $text        = trim($request->input('text', ''));
        $phoneNumber = $request->input('phoneNumber');
        $sessionId   = $request->input('sessionId');

        // Normalize Wasiliana initial dial (2222)
        if ($text === "2222") {
            $text = "";
        }

        // -------------------------------------------------------
        // Normalize navigation presses:
        //
        //   "00"  → Home: jump straight back to main menu
        //           We do this by resetting $text to "1" so the
        //           router lands on Level 1 (Education & Health).
        //
        //   "0"   → Back: pop one level off the stack
        //
        //   "98"  → More: treated as a normal forward navigation
        //           (already handled by level/choice matching below)
        // -------------------------------------------------------
        if (!empty($text)) {
            $parts   = explode('*', $text);
            $trimmed = [];

            foreach ($parts as $part) {
                if ($part === '00') {
                    // "00. Home" — jump to main menu regardless of depth
                    $trimmed = ['1'];
                } elseif ($part === '0') {
                    // "0. Back" — erase the last step
                    array_pop($trimmed);
                } else {
                    $trimmed[] = $part;
                }
            }

            $text = implode('*', $trimmed);
        }

        // Build details array and determine depth
        $details  = !empty($text) ? explode('*', $text) : [];
        $level    = count($details);
        $response = "";

        // =======================================================
        // LEVEL 0 — Welcome Screen
        // (No Home/Back here — this is the very first screen)
        // =======================================================
        if ($level == 0) {
            $response  = "CON Welcome, my name is John Ndung'u, a young aspiring MP for Roysambu. Let's go digital!\n\n";
            $response .= "John Ndung'u ni kijana FRESH na IDEAS FRESH za Roysambu Fresh.\n\n";
            $response .= "1. Next";
        }

        // =======================================================
        // LEVEL 1 — Main Menu (Education & Health)
        // This is what "00. Home" always returns to.
        // No Home/Back shown here — this IS the home screen.
        // =======================================================
        elseif ($level == 1 && $details[0] == "1") {
            $response  = "CON 1. Education - Online bursary application\n";
            $response .= "2. Health - RoysAfya care";
        }

        // =======================================================
        // BRANCH 1: EDUCATION ($details[1] == "1")
        //
        // Flow:
        //   Level 2           → Bursary landing (Start / Check Status / Help)
        //   Level 3, pick 1   → Prompt: Enter National ID
        //   Level 3, pick 2   → Coming Soon
        //   Level 3, pick 3   → Coming Soon
        //   Level 4           → Select Category (ID captured at level 3)
        //   Level 5           → Select Ward (Category captured at level 4)
        //   Level 6           → Save to DB + Thank You screen (CON, not END)
        // =======================================================
        elseif ($level >= 2 && $details[1] == "1") {

            // --- Level 2: Bursary Landing Page ---
            if ($level == 2) {
                $response  = "CON Online Bursary Application\n";
                $response .= "TOTAL FUNDS ALLOCATED: 50M\n\n";
                $response .= "1. Start Application\n";
                $response .= "2. Check Status\n";
                $response .= "3. Help\n";
                $response .= "0. Back";
            }

            // --- Level 3: User picked from the landing page ---
            elseif ($level == 3) {
                $eduChoice = $details[2];

                if ($eduChoice == "1") {
                    $response  = "CON Enter Applicant's National ID Number:\n\n";
                    $response .= "0. Back\n";
                    $response .= "00. Home";
                } elseif ($eduChoice == "2") {
                    $response  = "CON Check Status\n\n";
                    $response .= "This feature is coming soon.\n\n";
                    $response .= "0. Back\n";
                    $response .= "00. Home";
                } elseif ($eduChoice == "3") {
                    $response  = "CON Help\n\n";
                    $response .= "This feature is coming soon.\n\n";
                    $response .= "0. Back\n";
                    $response .= "00. Home";
                } else {
                    $response  = "CON Invalid choice.\n\n";
                    $response .= "0. Back\n";
                    $response .= "00. Home";
                }
            }

            // --- Level 4: National ID captured, now pick Category ---
            // $details[3] == National ID the user typed
            elseif ($level == 4 && $details[2] == "1") {
                $response  = "CON Select Category:\n";
                $response .= "1. Secondary School\n";
                $response .= "2. University/College\n";
                $response .= "3. Vocational/TVET\n";
                $response .= "4. PWD (Persons With Disabilities)\n";
                $response .= "0. Back\n";
                $response .= "00. Home";
            }

            // --- Level 5: Category captured, now pick Ward ---
            // $details[4] == the category choice (1-4)
            elseif ($level == 5 && $details[2] == "1") {
                $categoryChoice  = $details[4];
                $validCategories = ["1", "2", "3", "4"];

                if (in_array($categoryChoice, $validCategories)) {
                    $response  = "CON Select Your Ward:\n";
                    $response .= "1. Githurai\n";
                    $response .= "2. Zimmerman\n";
                    $response .= "3. Kahawa West\n";
                    $response .= "4. Kahawa\n";
                    $response .= "5. Roysambu\n";
                    $response .= "0. Back\n";
                    $response .= "00. Home";
                } else {
                    $response  = "CON Invalid category selected.\n\n";
                    $response .= "0. Back\n";
                    $response .= "00. Home";
                }
            }

            // --- Level 6: Ward captured — save and show Thank You screen ---
            // $details[3] == National ID
            // $details[4] == Category choice
            // $details[5] == Ward choice
            elseif ($level == 6 && $details[2] == "1") {
                $nationalId     = $details[3];
                $categoryChoice = $details[4];
                $wardChoice     = $details[5];

                $validWards = ["1", "2", "3", "4", "5"];

                if (in_array($wardChoice, $validWards)) {

                    $categoryMap = [
                        "1" => "Secondary School",
                        "2" => "University/College",
                        "3" => "Vocational/TVET",
                        "4" => "PWD (Persons With Disabilities)",
                    ];
                    $wardMap = [
                        "1" => "Githurai",
                        "2" => "Zimmerman",
                        "3" => "Kahawa West",
                        "4" => "Kahawa",
                        "5" => "Roysambu",
                    ];

                    $categoryName = $categoryMap[$categoryChoice] ?? "Unknown";
                    $wardName     = $wardMap[$wardChoice]         ?? "Unknown";

                    // Save or update the applicant record
                    // Voter::updateOrCreate(
                    //     ['phone' => $phoneNumber],
                    //     [
                    //         'national_id' => $nationalId,
                    //         'category'    => $categoryName,
                    //         'ward'        => $wardName,
                    //         'session_id'  => $sessionId,
                    //     ]
                    // );

                    // CON instead of END — session stays alive
                    $response  = "CON Thank you. Your basic details are saved.\n\n";
                    $response .= "You will receive an SMS with a link to upload the Fee Structure, Birth Certificate and ID copy.\n\n";
                    $response .= "00. Home";
                } else {
                    $response  = "CON Invalid ward selected.\n\n";
                    $response .= "0. Back\n";
                    $response .= "00. Home";
                }
            }

            // --- Catch-all for unexpected education levels ---
            else {
                $response  = "CON Invalid selection.\n\n";
                $response .= "0. Back\n";
                $response .= "00. Home";
            }
        }

        // =======================================================
        // BRANCH 2: HEALTH ($details[1] == "2")
        // =======================================================
        elseif ($level >= 2 && $details[1] == "2") {

            // --- Level 2: Health Sub-Menu ---
            if ($level == 2) {
                $response  = "CON Welcome to RoysAfya Care\n";
                $response .= "One Man, One Shilling, One Healthy Roysambu\n\n";
                $response .= "1. Benefits Info\n";
                $response .= "2. Contribution (Ksh 1)\n";
                $response .= "3. Register\n";
                $response .= "4. My Profile\n";
                $response .= "5. Request Support\n";
                $response .= "00. Home";
            }

            // --- Level 3: Health choices ---
            elseif ($level == 3) {
                $healthChoice = $details[2];

                if ($healthChoice == "1") {
                    // Benefits Info — has a 98. More option
                    $response  = "CON RoysAfya Benefits\n";
                    $response .= "* Medical & Maternal\n";
                    $response .= "* Accident Coverage\n";
                    $response .= "* Funeral Support\n\n";
                    $response .= "98. More\n";
                    $response .= "0. Back\n";
                    $response .= "00. Home";
                } elseif ($healthChoice == "3") {
                    $response  = "CON Please enter your full name:\n\n";
                    $response .= "0. Back\n";
                    $response .= "00. Home";
                } elseif ($healthChoice == "5") {
                    $response  = "CON Select support type:\n";
                    $response .= "1. Medical/Maternal\n";
                    $response .= "2. Accident/Emergency\n";
                    $response .= "3. Family Shopping (Breadwinner Ill)\n";
                    $response .= "4. Funeral Support\n";
                    $response .= "0. Back\n";
                    $response .= "00. Home";
                } else {
                    $response  = "CON Coming soon.\n\n";
                    $response .= "0. Back\n";
                    $response .= "00. Home";
                }
            }

            // --- Level 4: Health detail actions ---
            elseif ($level == 4) {
                $healthChoice = $details[2];
                $input        = end($details);

                if ($healthChoice == "1") {
                    // Came from Benefits Info — user pressed 98 (More)
                    if ($input == "98") {
                        $response  = "CON Hospital Shopping:\n";
                        $response .= "1 month food supply if breadwinner is admitted.\n\n";
                        $response .= "00. Home";
                    } else {
                        $response  = "CON Invalid choice.\n\n";
                        $response .= "0. Back\n";
                        $response .= "00. Home";
                    }
                } elseif ($healthChoice == "3") {
                    // Came from Register — $input is the name they typed
                    $name        = $input;
                    $displayName = empty($name) ? "valued supporter" : $name;

                    Voter::updateOrCreate(
                        ['phone' => $phoneNumber],
                        [
                            'name' => $name,
                            'session_id' => $sessionId
                        ]
                    );

                    // CON instead of END — session stays alive
                    $response  = "CON Thank you $displayName!\n\n";
                    $response .= "You have been registered successfully.\n\n";
                    $response .= "00. Home";
                } elseif ($healthChoice == "5") {
                    // Came from Request Support — user picked a category
                    $response  = "CON Please enter your ID number or location for follow-up:\n\n";
                    $response .= "0. Back\n";
                    $response .= "00. Home";
                } else {
                    $response  = "CON Invalid choice.\n\n";
                    $response .= "0. Back\n";
                    $response .= "00. Home";
                }
            }

            // --- Level 5: Support follow-up submission ---
            elseif ($level == 5) {
                $healthChoice = $details[2];

                if ($healthChoice == "5") {
                    // User submitted their ID/location — confirm and keep session alive
                    $response  = "CON Thank you!\n\n";
                    $response .= "Your support request has been received.\n";
                    $response .= "We will follow up with you shortly.\n\n";
                    $response .= "00. Home";
                } else {
                    $response  = "CON Invalid choice.\n\n";
                    $response .= "0. Back\n";
                    $response .= "00. Home";
                }
            }

            // --- Catch-all for unexpected health levels ---
            else {
                $response  = "CON Invalid selection.\n\n";
                $response .= "0. Back\n";
                $response .= "00. Home";
            }
        }

        // =======================================================
        // GLOBAL CATCH-ALL
        // =======================================================
        else {
            $response  = "CON Invalid selection.\n\n";
            $response .= "1. Education\n";
            $response .= "2. Health";
        }

        return response($response)->header('Content-Type', 'text/plain');
    }
}
