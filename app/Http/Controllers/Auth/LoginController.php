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

	/**
	 * Where to send the user once they are logged in.
	 *
	 * Laravel's default is redirect()->intended(), which replays whatever URL was
	 * requested when the session was unauthenticated. That URL is often not a page
	 * you can GET: the auth middleware stores it for AJAX autosaves, DataTable
	 * feeds and other POST/PUT/DELETE endpoints too (126 of them here), and it also
	 * survives for records that have since been deleted. Replaying one of those
	 * after login lands the user on a 404 — the intermittent bug this guards.
	 *
	 * So the stored URL is only honoured when it really is a GET page in this app;
	 * otherwise the user goes to the dashboard.
	 */
	protected function authenticated(Request $request, $user)
	{
		$intended = $request->session()->pull('url.intended');

		return redirect()->to($this->safeIntendedUrl($intended) ?? $this->redirectPath());
	}

	/**
	 * The intended URL if it is safe to send a freshly logged-in user there,
	 * otherwise null.
	 */
	protected function safeIntendedUrl(?string $intended): ?string
	{
		if (! $intended) {
			return null;
		}

		$parts = parse_url($intended);

		// Off-site targets are never followed (open-redirect protection).
		if (! empty($parts['host']) && $parts['host'] !== request()->getHost()) {
			return null;
		}

		$path = '/'.ltrim($parts['path'] ?? '/', '/');

		// Bouncing back to the auth pages would loop.
		if (preg_match('#^/(login|logout)$#i', $path)) {
			return null;
		}

		// Must resolve to a real GET route. This rejects the POST/PUT/DELETE
		// endpoints and any /resource/{id} whose record no longer exists.
		try {
			$probe = \Illuminate\Http\Request::create($intended, 'GET');
			app('router')->getRoutes()->match($probe);
		} catch (\Throwable $e) {
			return null;
		}

		return $intended;
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
