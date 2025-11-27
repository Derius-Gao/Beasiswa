<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\AIService;

class AIController extends Controller
{
	protected $aiService;

	public function __construct(AIService $aiService)
	{
		$this->aiService = $aiService;
	}

	public function health()
	{
		return response()->json($this->aiService->checkHealth());
	}
}


