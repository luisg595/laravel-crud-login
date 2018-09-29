<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

class LoginApiController extends Controller {

	public function logout(Request $request)
	{
	    $request->user()->token()->revoke();
	    return response()->json([
	        'message' => 'Successfully logged out'
	    ]);
	}
	
}