<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Scholarship;
use Illuminate\Http\Request;

class ScholarshipController extends Controller
{
    public function index()
    {
        $scholarships = Scholarship::orderByDesc('created_at')->paginate(15);

        return view('admin.scholarships.index', compact('scholarships'));
    }

    public function create()
    {
        return view('admin.scholarships.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'provider' => 'required|string|max:255',
            'criteria' => 'nullable|array',
            'application_deadline' => 'required|date|after:today',
            'is_active' => 'boolean',
        ]);

        if (!empty($validated['criteria'])) {
            $validated['criteria'] = array_filter($validated['criteria']);
        }

        Scholarship::create($validated);

        return redirect()
            ->route('admin.scholarships.index')
            ->with('success', 'Scholarship created successfully.');
    }

    public function edit(Scholarship $scholarship)
    {
        return view('admin.scholarships.edit', compact('scholarship'));
    }

    public function update(Request $request, Scholarship $scholarship)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'provider' => 'required|string|max:255',
            'criteria' => 'nullable|array',
            'application_deadline' => 'required|date',
            'is_active' => 'boolean',
        ]);

        if (!empty($validated['criteria'])) {
            $validated['criteria'] = array_filter($validated['criteria']);
        }

        $scholarship->update($validated);

        return redirect()
            ->route('admin.scholarships.index')
            ->with('success', 'Scholarship updated successfully.');
    }

    public function destroy(Scholarship $scholarship)
    {
        $scholarship->delete();

        return redirect()
            ->route('admin.scholarships.index')
            ->with('success', 'Scholarship deleted successfully.');
    }
}


