<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class RefreshTokenController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request)
    {
        $token = auth()->guard("api")->refresh();
        Log::info(auth()->guard("api")->user());
        return response()->json([
            'token' => $token,
            'success' => true,
        ],200);
    }
}
