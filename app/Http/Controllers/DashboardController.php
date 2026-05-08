<?php

namespace App\Http\Controllers;

use App\Models\HouseCreation;
use App\Models\Member;
use App\Models\MemberReport;

class DashboardController extends Controller
{
    public function adminIndex()
    {
        return view('backend.pages.dashboard.index', $this->dashboardData());
    }

    public function superAdminIndex()
    {
        return view('backend.pages.dashboard.index', $this->dashboardData());
    }

    private function dashboardData(): array
    {
        return [
            'totalHouses' => HouseCreation::count(),
            'activeHouses' => HouseCreation::where('active', 1)->count(),
            'totalMembers' => Member::count(),
            'activeMembers' => Member::where('active', 1)->count(),
            'subscribedMembers' => Member::where('subscription', 1)->count(),
            'totalReports' => MemberReport::count(),
            'pendingReports' => MemberReport::where('status', 'pending')->count(),
            'totalDebit' => MemberReport::sum('debit'),
            'totalCredit' => MemberReport::sum('credit'),
        ];
    }
}
