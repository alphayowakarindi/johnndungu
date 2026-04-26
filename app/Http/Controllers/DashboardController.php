<?php

namespace App\Http\Controllers;

use App\Models\Voter;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        // Fetch paginated voters
        $voters = Voter::latest()->paginate(15)->through(function ($voter) {
            return [
                'id' => $voter->id,
                'name' => $voter->name ?? 'N/A',
                'phone' => $voter->phone,
                'registered_at' => $voter->created_at->format('D, M jS'),
                'time' => $voter->created_at->format('g:i A'),
            ];
        });

        return Inertia::render('dashboard', [
            'voters' => $voters,
        ]);
    }
}
