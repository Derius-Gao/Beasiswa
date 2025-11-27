<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Transaction;
use App\Models\Scholarship;
use App\Models\Bill;

class AdminController extends Controller
{
    public function dashboard()
    {
        return view('admin.dashboard');
    }

    public function scholarships()
    {
        return view('admin.scholarships');
    }

    public function reports()
    {
        return view('admin.reports');
    }

    public function transactions()
    {
        return view('admin.transactions');
    }
}
