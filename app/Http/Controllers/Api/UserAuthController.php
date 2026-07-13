<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class UserAuthController extends Controller
{
    /**
     * Privilege id for "Technician User" (privilege_code = TECH).
     * Only users with this privilege may use the API.
     */
    protected const TECHNICIAN_PRIVILEGE = 49;

    /**
     * Authenticate a user and issue a Sanctum API token.
     *
     * Accepts a single "username" field that may be an email address,
     * a mobile number, or a username — matching the web login behaviour.
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status' => 0,
                'msg'    => $validator->errors()->first(),
            ], 422);
        }

        $login = $request->input('username');

        // Decide which column to match on, same as the web LoginController.
        $field = filter_var($login, FILTER_VALIDATE_EMAIL)
            ? 'user_email'
            : (is_numeric($login) ? 'mobile' : 'username');

        $user = User::where($field, $login)->first();

        if (! $user || ! Hash::check($request->input('password'), $user->password)) {
            return response()->json([
                'status' => 0,
                'msg'    => 'Invalid credentials.',
            ], 401);
        }

        // status: 0 = active, 1 = blocked/deleted.
        if ((int) $user->status === 1) {
            return response()->json([
                'status' => 0,
                'msg'    => 'Your account is inactive. Please contact the administrator.',
            ], 403);
        }

        // Restrict API access to technicians only.
        if ((int) $user->previlage !== self::TECHNICIAN_PRIVILEGE) {
            return response()->json([
                'status' => 0,
                'msg'    => 'Access denied. This API is available to technicians only.',
            ], 403);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'status' => 1,
            'msg'    => 'Login successful.',
            'token'  => $token,
            'user'   => $this->userPayload($user),
        ]);
    }

    /**
     * Return the currently authenticated user.
     */
    public function getUser(Request $request)
    {
        return response()->json([
            'status' => 1,
            'user'   => $this->userPayload($request->user()),
        ]);
    }

    /**
     * Revoke the token used for the current request.
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 1,
            'msg'    => 'Logged out successfully.',
        ]);
    }

    /**
     * Shape the user object returned by the API.
     */
    protected function userPayload(User $user): array
    {
        return [
            'id'         => $user->id,
            'user_id'    => $user->user_id,
            'username'   => $user->username,
            'name'       => trim($user->name . ' ' . $user->lname),
            'user_email' => $user->user_email,
            'mobile'     => $user->mobile,
            'previlage'  => $user->previlage,
        ];
    }
}
