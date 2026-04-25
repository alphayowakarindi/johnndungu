<?php

namespace App\Http\Controllers;

use App\Models\Voter;
use Inertia\Inertia;

class DashboardController extends Controller
{


    public function index()
    {
        return Inertia::render('dashboard', [
            'voters' => Voter::latest()->get()->map(function ($voter) {
                return [
                    'id' => $voter->id,
                    'name' => $voter->name ?? 'N/A',
                    'phone' => $voter->phone,
                    'registered_at' => $voter->created_at->format('D, M jS'), // e.g. Sun, Apr 26th
                    'full_date' => $voter->created_at->format('Y'),          // e.g. 2026
                    'time' => $voter->created_at->format('g:i A'),           // e.g. 1:45 AM
                ];
            }),
        ]);
    }
}
