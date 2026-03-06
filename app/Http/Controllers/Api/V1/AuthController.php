<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\V1\Concerns\ApiResponse;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use ApiResponse;

    /**
     * POST /api/v1/auth/login
     * Returns a Sanctum API token.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return $this->error('Invalid credentials.', 401);
        }

        if ($user->status !== 'Active') {
            return $this->error('Your account is inactive.', 403);
        }

        // Revoke existing tokens (single-session)
        $user->tokens()->delete();

        $token = $user->createToken('mobile-app', ['*'])->plainTextToken;

        return $this->success([
            'token'      => $token,
            'token_type' => 'Bearer',
            'user'       => [
                'id'     => $user->id,
                'name'   => $user->name,
                'email'  => $user->email,
                'role'   => $user->role?->name,
                'branch' => $user->branch?->name,
            ],
        ]);
    }

    /**
     * POST /api/v1/auth/logout
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();
        return $this->success(null, 'Logged out successfully.');
    }

    /**
     * GET /api/v1/auth/me
     */
    public function me(Request $request)
    {
        $user = $request->user()->load('role', 'branch');
        return $this->success([
            'id'     => $user->id,
            'name'   => $user->name,
            'email'  => $user->email,
            'role'   => $user->role?->name,
            'branch' => $user->branch?->name,
        ]);
    }
}
