<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Services\TokenService;

class JwtController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function token(TokenService $service)
    {
        $user = auth()->guard('web')->user();
        abort_if(!$user, 401, 'No web session');
        
        return response()->json([
            'token' => $service->generateToken($user->id, $user->nickname)
        ]);
    }
}
