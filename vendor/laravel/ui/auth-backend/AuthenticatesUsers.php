<?php

namespace Illuminate\Foundation\Auth;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Core;
use DB;
trait AuthenticatesUsers
{
    use RedirectsUsers, ThrottlesLogins;

    /**
     * Show the application's login form.
     *
     * @return \Illuminate\View\View
     */
    public function showLoginForm()
    {
        return view('layouts.login');
    }

    /**
     * Handle a login request to the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function login(Request $request)
    {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if (method_exists($this, 'hasTooManyLoginAttempts') &&
            $this->hasTooManyLoginAttempts($request)) {
			
            $this->fireLockoutEvent($request);
			
			$this->block_ip($request);
			
			
            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {
            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);
		$this->block_ip($request);
        return $this->sendFailedLoginResponse($request);
    }

    /**
     * Validate the user login request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function validateLogin(Request $request)
    {
        $request->validate([
            $this->username() => 'required|string',
            'password' => 'required|string',
        ]);
    }

    /**
     * Attempt to log the user into the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return bool
     */
    protected function attemptLogin(Request $request)
    { //dd($request->filled('remember_me'));
        return $this->guard()->attempt(
            $this->credentials($request), $request->filled('remember_me')
        );
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
         $credentials=$request->only($this->username(), 'password');
		 return $credentials;
    }

    /**
     * Send the response after the user was authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    protected function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();

        $this->clearLoginAttempts($request);

        if ($response = $this->authenticated($request, $this->guard()->user())) {
            return $response;
        }

        return $request->wantsJson()
                    ? new JsonResponse([], 204)
                    : redirect()->intended($this->redirectPath());
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
   protected function authenticated(Request $request, $user)
    {
        if ($user->status == 2) 
		{
            $message = 'Account Has been Blocked.. Please contact your Administrator';
            // Log the user out.
            $this->logout($request);

            // Return them to the log in form.
            return redirect()->back()
                ->withInput($request->only($this->username(), 'remember'))
                ->withErrors([
                    // This is where we are passing the message back.
                    $this->username() => $message,
                ]);
        }
	   
	    if ($user->status == 1)   // check user deleted
		{
			$message = 'Your Account Has been Deleted.. Please contact your Administrator';
            // Log the user out.
            $this->logout($request);

            // Return them to the log in form.
            return redirect()->back()
                ->withInput($request->only($this->username(), 'remember'))
                ->withErrors([
                    // This is where we are passing the message back.
            $this->username() => $message,
                ]);
		}
		
			$ip = $request->ip();
			$action = '';
			$user_id = Auth::user()->id;
			$user_name = Auth::user()->name;
			$category = "Login";
			$activity = '#'.Auth::user()->name.'('.Auth::user()->username.') Has been Logged In at '.date('d-m-Y H:i:s');
                $log_array = ['activity_ip'=>$ip, 'activity_action'=>$action, 'activity_user'=>$user_name, 'activity_user_id'=>$user_id, 'activity_desc'=>$activity,'activity_category'=>$category];
			Core::userActivityAction($log_array);
		
    }

    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    protected function sendFailedLoginResponse(Request $request)
    {
	   /*throw ValidationException::withMessages([
            $this->username() => [trans('auth.failed')],
        ]);*/
		if($this->username()=='username')
		{
			$msg="Incorrect Username or Password";
		}
	    else if($this->username()=='mobile')
		{
			$msg="Incorrect Mobile no or Password";
		}
		else
		{
			$msg="Incorrect Email ID or Password";
		}
        throw ValidationException::withMessages([
            $this->username() => [$msg],
        ]);
    }

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function username()
    {
        return 'email';
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        if ($response = $this->loggedOut($request)) {
            return $response;
        }
		
		

        return $request->wantsJson()
            ? new JsonResponse([], 204)
            : redirect('/');
    }

    /**
     * The user has logged out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return mixed
     */
    protected function loggedOut(Request $request)
    {
        //
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard();
    }
	
	public function block_ip($request)
	{
	 
		$ip = $request->ip(); // GET LOGIN IP ADDRESS
		$ip_data = DB::table('tbl_user_login_ip')->where('user_login_ip',$ip)->first();
		if(!($ip_data))
		{
			$values = array('user_login_ip'=>$ip,'attempts'=>$this->limiter()->attempts($this->throttleKey($request)),'user_login_username'=>$request->username,'user_login_password'=>$request->password);
			DB::table('tbl_user_login_ip')->insert($values);
		}
		else
		{
			$where = array('user_login_ip'=>$ip);
			$values = array('attempts'=>$this->limiter()->attempts($this->throttleKey($request)),'user_login_username'=>$request->username,'user_login_password'=>$request->password);
			DB::table('tbl_user_login_ip')->where($where)->update($values);
		}

		#LOGIN FILED HISTORY DETAILS
		$F_values = array('login_failed_ip'=>$ip,'username'=>$request->username,'password'=>$request->password);
		DB::table('tbl_user_login_failed_history')->insert($F_values);
		#LOGIN FILED HISTORY DETAILS
		$this->block_User($request); // USER Blocking
		
			$ip = $request->ip();
			$action = '';
			$activity = 'Un-Authorized Login Attempt at '.date('d-m-Y H:i:s').' - Tried Username :'.$request->username;
                $log_array = ['activity_ip'=>$ip, 'activity_action'=>$action, 'activity_desc'=>$activity];
			Core::userActivityAction($log_array);
	}
	
	public function block_User($request)
	{
	    $username=$request->username;
		$password=$request->password;
		$ip=$request->ip();
		$attempts=$this->limiter()->attempts($this->throttleKey($request));
		$user_data = DB::table('users')->where('status',0)->where('username',$username)->first();
		if($user_data)
		{
			$attempt = DB::table('tbl_user_login_attempt_user')->where('username',$username)->first();
			if(!$attempt)
			{
				$values = array('user_login_attempt_user_ip'=>$ip,'username'=>$username,'password'=>$password,'attempt'=>$attempts);
				DB::table('tbl_user_login_attempt_user')->insert($values);
			}
			else
			{
				$where = array('username'=>$username);
					$values = array('user_login_attempt_user_ip'=>$ip,'username'=>$username,'password'=>$password,'attempt'=>$attempts);
					DB::table('tbl_user_login_attempt_user')->where($where)->update($values);
					if($attempts==$this->maxAttempts)
					{
					$F_where = array('username'=>$username);
					$F_values = array('status'=>'2','user_blocked_time'=>date('Y-m-d h:i:s'));
					DB::table('users')->where($F_where)->update($F_values);
					}
			}
			
			
		}
	
	}
}
