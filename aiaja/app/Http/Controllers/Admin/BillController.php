<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bill;
use App\Models\User;
use Illuminate\Http\Request;

class BillController extends Controller
{
    public function index()
    {
        $bills = Bill::with('user')
            ->orderByDesc('created_at')
            ->paginate(15);

        return view('admin.bills.index', compact('bills'));
    }

    public function create()
    {
        $students = User::where('is_student', true)
            ->orderBy('name')
            ->get(['id', 'name', 'student_id']);

        return view('admin.bills.create', compact('students'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|string|max:100',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'required|date',
            'status' => 'required|in:unpaid,paid,overdue',
            'description' => 'nullable|string',
        ]);

        Bill::create($validated + ['is_auto_generated' => false]);

        return redirect()
            ->route('admin.bills.index')
            ->with('success', 'Bill created successfully.');
    }

    public function edit(Bill $bill)
    {
        $students = User::where('is_student', true)
            ->orderBy('name')
            ->get(['id', 'name', 'student_id']);

        return view('admin.bills.edit', compact('bill', 'students'));
    }

    public function update(Request $request, Bill $bill)
    {
        $validated = $request->validate([
            'user_id' => 'required|exists:users,id',
            'type' => 'required|string|max:100',
            'amount' => 'required|numeric|min:0',
            'due_date' => 'required|date',
            'status' => 'required|in:unpaid,paid,overdue',
            'description' => 'nullable|string',
        ]);

        $bill->update($validated);

        return redirect()
            ->route('admin.bills.index')
            ->with('success', 'Bill updated successfully.');
    }

    public function destroy(Bill $bill)
    {
        $bill->delete();

        return redirect()
            ->route('admin.bills.index')
            ->with('success', 'Bill deleted successfully.');
    }
}


