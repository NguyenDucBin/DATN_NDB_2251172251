<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Tour;
use App\Models\Booking;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    public function dashboard()
    {
        $totalUsers = User::count();
        $totalTours = Tour::count();
        $totalBookings = Booking::count();
        $totalRevenue = Booking::where('payment_status', 'paid')
            ->whereIn('status', ['confirmed', 'completed'])
            ->sum('total_price');

        // Lấy dữ liệu doanh thu theo tháng cho biểu đồ
        $monthExpression = DB::getDriverName() === 'sqlite'
            ? "CAST(strftime('%m', created_at) AS INTEGER)"
            : 'MONTH(created_at)';

        $revenueByMonth = Booking::selectRaw("{$monthExpression} as month, SUM(total_price) as revenue")
            ->where('payment_status', 'paid')
            ->whereIn('status', ['confirmed', 'completed'])
            ->whereYear('created_at', date('Y'))
            ->groupBy('month')
            ->orderBy('month')
            ->get()
            ->pluck('revenue', 'month')
            ->toArray();
            
        // Chuẩn bị mảng 12 tháng
        $chartData = [];
        for ($i = 1; $i <= 12; $i++) {
            $chartData[] = $revenueByMonth[$i] ?? 0;
        }

        $pendingToursCount = Tour::where('status', 'pending')->count();

        return view('admin.dashboard', compact('totalUsers', 'totalTours', 'totalBookings', 'totalRevenue', 'chartData', 'pendingToursCount'));
    }
}
