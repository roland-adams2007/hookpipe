<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TokenController extends Controller
{
    public function show(Request $request)
    {
        $token = $request->user()->tokens()
            ->where('name', 'like', 'api-token%')
            ->first();
        
        if (!$token) {
            return response()->json([
                'success' => false,
                'message' => 'No external token found. Create one using POST /token',
                'has_token' => false
            ], 404);
        }

        return response()->json([
            'success' => true,
            'has_token' => true,
            'token' => [
                'id' => $token->id,
                'name' => $token->name,
                'last_used_at' => $token->last_used_at,
                'created_at' => $token->created_at,
            ]
        ]);
    }

    public function createOrReplace(Request $request)
    {
        $request->validate([
            'name' => 'nullable|string|max:255',
        ]);

        $user = $request->user();
        $tokenName = $request->input('name', 'api-token');
        
        $oldToken = $user->tokens()
            ->where('name', 'like', 'api-token%')
            ->first();
        
        $action = $oldToken ? 'replaced' : 'created';
        
        $user->tokens()
            ->where('name', 'like', 'api-token%')
            ->delete();
        
        $token = $user->createToken($tokenName);
        
        return response()->json([
            'success' => true,
            'message' => "External token {$action} successfully",
            'action' => $action,
            'token' => $token->plainTextToken,
            'token_name' => $tokenName,
            'warning' => 'Copy this token now - it will not be shown again'
        ], 201);
    }

    public function delete(Request $request)
    {
        $user = $request->user();
        
        $tokenCount = $user->tokens()
            ->where('name', 'like', 'api-token%')
            ->count();
        
        if ($tokenCount === 0) {
            return response()->json([
                'success' => false,
                'message' => 'No external token found to delete'
            ], 404);
        }
        
        $user->tokens()
            ->where('name', 'like', 'api-token%')
            ->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'External token deleted successfully'
        ]);
    }
}