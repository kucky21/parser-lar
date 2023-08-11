<?php

namespace App\Http\Controllers;

use App\Services\FoxEntryParserService;

class FoxEntryController extends Controller
{
    public function index(FoxEntryParserService $parserService)
    {
        try {
            $parserService->parseAndIndex();
            return response()->json(['message' => 'Data indexed successfully']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'An error occurred while indexing data.'], 500);
        }
    }
}