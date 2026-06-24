<?php

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Tour;
use App\Models\Booking;

class HostController extends Controller
{
    public function dashboard()
    {
        $hostId = auth()->id();
        
        $totalTours = Tour::where('host_id', $hostId)->count();
        $totalBookings = Booking::whereHas('tour', function($q) use ($hostId) {
            $q->where('host_id', $hostId);
        })->count();
        
        $totalRevenue = Booking::whereHas('tour', function($q) use ($hostId) {
            $q->where('host_id', $hostId);
        })
            ->where('payment_status', 'paid')
            ->whereIn('status', ['confirmed', 'completed'])
            ->sum('total_price');

        $recentBookings = Booking::whereHas('tour', function($q) use ($hostId) {
            $q->where('host_id', $hostId);
        })->with(['user', 'tour', 'refunds'])->latest()->take(5)->get();

        return view('host.dashboard', compact('totalTours', 'totalBookings', 'totalRevenue', 'recentBookings'));
    }
}
