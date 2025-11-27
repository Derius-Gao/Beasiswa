<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Scholarship;
use App\Models\ScholarshipRecommendation;
use App\Services\AIService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ScholarshipController extends Controller
{
    protected $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    /**
     * Get scholarship recommendations for user
     */
    public function recommendations(Request $request)
    {
        $recommendations = $request->user()->scholarshipRecommendations()
            ->with('scholarship')
            ->orderBy('match_score', 'desc')
            ->get()
            ->map(function ($rec) {
                return [
                    'id' => $rec->id,
                    'scholarship' => $rec->scholarship,
                    'match_score' => $rec->match_score,
                    'reason' => $rec->reason,
                    'recommended_at' => $rec->recommended_at,
                    'is_notified' => $rec->is_notified,
                ];
            });

        return response()->json($recommendations);
    }

    /**
     * Apply for a scholarship
     */
    public function apply(Request $request, Scholarship $scholarship)
    {
        $validator = Validator::make($request->all(), [
            'motivation_letter' => 'required|string|max:2000',
            'documents' => 'nullable|array',
            'documents.*' => 'file|mimes:pdf,doc,docx,jpg,jpeg,png|max:5120', // 5MB max
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Check if user has a recommendation for this scholarship
        $recommendation = $request->user()->scholarshipRecommendations()
            ->where('scholarship_id', $scholarship->id)
            ->first();

        if (!$recommendation) {
            return response()->json(['error' => 'No recommendation found for this scholarship'], 404);
        }

        // Check if already applied
        if ($recommendation->applied_at) {
            return response()->json(['error' => 'Already applied for this scholarship'], 400);
        }

        // Handle document uploads
        $documentPaths = [];
        if ($request->hasFile('documents')) {
            foreach ($request->file('documents') as $document) {
                $path = $document->store('scholarship-applications', 'public');
                $documentPaths[] = $path;
            }
        }

        // Update recommendation with application details
        $recommendation->update([
            'applied_at' => now(),
            'application_data' => [
                'motivation_letter' => $request->motivation_letter,
                'documents' => $documentPaths,
                'applied_at' => now(),
            ],
        ]);

        return response()->json([
            'message' => 'Scholarship application submitted successfully',
            'recommendation' => $recommendation->load('scholarship')
        ]);
    }

    /**
     * Get all available scholarships (admin)
     */
    public function index(Request $request)
    {
        $scholarships = Scholarship::with('recommendations')
            ->when($request->has('active'), function ($query) use ($request) {
                return $query->where('is_active', $request->boolean('active'));
            })
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($scholarships);
    }

    /**
     * Create new scholarship (admin)
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'amount' => 'required|numeric|min:0',
            'provider' => 'required|string|max:255',
            'criteria' => 'required|json',
            'application_deadline' => 'required|date|after:today',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $scholarship = Scholarship::create($request->all());

        return response()->json([
            'message' => 'Scholarship created successfully',
            'scholarship' => $scholarship
        ], 201);
    }

    /**
     * Update scholarship (admin)
     */
    public function update(Request $request, Scholarship $scholarship)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'string|max:255',
            'description' => 'string',
            'amount' => 'numeric|min:0',
            'provider' => 'string|max:255',
            'criteria' => 'json',
            'application_deadline' => 'date|after:today',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $scholarship->update($request->all());

        return response()->json([
            'message' => 'Scholarship updated successfully',
            'scholarship' => $scholarship
        ]);
    }

    /**
     * Delete scholarship (admin)
     */
    public function destroy(Scholarship $scholarship)
    {
        $scholarship->delete();

        return response()->json(['message' => 'Scholarship deleted successfully']);
    }
}
