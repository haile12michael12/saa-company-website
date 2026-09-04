<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\Security\SecurityLogService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function __construct(protected SecurityLogService $securityLogService) {}

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
            'device_name' => 'nullable|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            $this->securityLogService->logLoginAttempt($request->email, false, 'Invalid credentials');
            throw ValidationException::withMessages([
                'email' => ['The provided credentials do not match our records.'],
            ]);
        }

        $deviceName = $request->device_name ?? 'API Token';
        $token = $user->createToken($deviceName)->plainTextToken;

        $this->securityLogService->logLoginAttempt($request->email, true);

        return response()->json([
            'token' => $token,
            'token_type' => 'Bearer',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'company_id' => $user->company_id,
            ],
        ]);
    }

    public function me(Request $request)
    {
        return response()->json($request->user()->load('company'));
    }

    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Token revoked successfully.']);
    }
}