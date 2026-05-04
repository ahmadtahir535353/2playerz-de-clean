<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\UserFcmToken;

class FcmController extends Controller
{
    public function saveToken(Request $req)
    {
        $req->validate(['token' => 'required|string']);

        UserFcmToken::updateOrCreate(
            ['token' => $req->token],
            [
                'user_id' => $req->user()->id,
                'device'  => $req->header('User-Agent'),
            ]
        );

        return response()->json(['ok' => true]);
    }

    // optional: logout / revoke token
    public function deleteToken(Request $req)
    {
        $req->validate(['token' => 'required|string']);
        UserFcmToken::where('token', $req->token)->delete();
        return response()->json(['ok' => true]);
    }
}
