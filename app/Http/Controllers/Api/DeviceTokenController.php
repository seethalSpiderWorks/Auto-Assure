<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\DeviceToken;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class DeviceTokenController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
            'device_type' => 'required|in:android,ios',
            'device_name' => 'nullable|string|max:255',
        ]);

        $deviceToken = DeviceToken::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'token' => $request->token,
            ],
            [
                'device_type' => $request->device_type,
                'device_name' => $request->device_name,
            ]
        );

        Log::channel('fcm')->info("DEVICE TOKEN REGISTERED | User: " . Auth::id() . " | Type: {$request->device_type} | Name: {$request->device_name} | Token: " . substr($request->token, 0, 20) . "...");

        return response()->json(['message' => 'Device token registered successfully.']);
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'token' => 'required|string',
        ]);

        DeviceToken::where('user_id', Auth::id())
            ->where('token', $request->token)
            ->delete();

        Log::channel('fcm')->info("DEVICE TOKEN REMOVED | User: " . Auth::id() . " | Token: " . substr($request->token, 0, 20) . "...");

        return response()->json(['message' => 'Device token removed successfully.']);
    }
}
