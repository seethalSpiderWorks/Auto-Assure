<?php

namespace App\Http\Controllers\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use DB;
use Carbon\Carbon;

class LoginController extends Controller
{
   
	protected $maxAttempts = 5; //Default 5
    protected $decayMinutes = 5; //Default 5
    
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
		$this->middleware('guest')->except('logout');
       	$this->username = $this->findUsername();
    }
	
    public function findUsername()
    {
        $login = request()->input('username');
 
        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'user_email' : (is_numeric($login)?'mobile':'username');
 
        request()->merge([$fieldType => $login]);
 
        return $fieldType;
    }
	
    public function username()
    {
        return $this->username;
    }
	
    public function maxAttempt()
    {
       return $this->maxAttempts;
    }

	public function setcookie(Request $request)
	{
		//dd(1);
        $user_name = $request->username;
        $password = $request->password;

		$cookie_user_name =$request->username;
		$cookie_password= $request->password;
		$minutes =43200; 
		$response = new Response('Cookie Name'); 
		$response->withCookie(cookie('cookie_user_name', $cookie_user_name, $minutes)); 
		$response->withCookie(cookie('cookie_password', $cookie_password, $minutes)); 
		return $response; 
    }
	
    public function getCookie(Request $request)
    {
		//dd(2);
		\Cookie::queue(\Cookie::forget('cookie_user_name'));
		\Cookie::queue(\Cookie::forget('cookie_password'));
		return ['200' =>'Success'];
	} 
}
