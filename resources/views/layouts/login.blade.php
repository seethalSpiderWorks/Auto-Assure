<style>
.loginerrormsg{color: #FF0000; text-align: left; padding-top: 10px; margin-bottom: 0;}
</style>
 
<?php 
    $cookie_username = Cookie::get('cookie_user_name');
	$cookie_pasword = Cookie::get('cookie_password');
?>
 
<!doctype html>
<html lang="en">

    <head>
        <meta name="robots" content="noindex" />
        <meta charset="utf-8" />
        <title> Login | Auto Assure </title>
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta content="Auto Assure" name="description" />
        <meta content="Themesbrand" name="author" />
        <!-- App favicon -->
        <link rel="shortcut icon" href="{{asset('assets/images/favicon.svg')}}">

        <!-- Bootstrap Css -->
        <link href="{{asset('assets/css/bootstrap.min.css')}}" id="bootstrap-style" rel="stylesheet" type="text/css" />
        <!-- Icons Css -->
        <link href="{{asset('assets/css/icons.min.css')}}" rel="stylesheet" type="text/css" />
        <!-- App Css-->
        <link href="{{asset('assets/css/app.min.css?ver=1.2')}}" id="app-style" rel="stylesheet" type="text/css" />

    </head>

    <body class="authentication-bg" style="">
	 <div class="account-pages-container">
        <div class="account-pages my-5 pt-sm-5">
            <div class="container">
               
                <div class="row align-items-center justify-content-center">
                    <div class="col-md-8 col-lg-6 col-xl-5">
                        <div class="card">
                           
                            <div class="card-body p-4"> 
								<div class="row">
									<div class="col-lg-12">
										<a href="#" class="mb-5 d-block auth-logo" style="margin-bottom: 1rem!important">
											<img src="{{asset('assets/images/logo.svg')}}" style="height:100;width:290" alt=""  class="logo logo-dark">
											<!--<img src="{{asset('assets/images/logo-light.png')}}" alt="" height="22" class="logo logo-light">-->
										</a>
									</div>
								</div>
								
                                <div class="text-center mt-2">
                                    <h5 class="text-primary">Welcome Back !</h5>
                                    <p class="text-muted">Sign in to continue to Auto Assure.</p>
                                </div>
                                <div class="p-2 mt-4">
									<div class="clearfix"></div>

									@if (session('status'))
										<div class="alert alert-danger">
											{{ session('status') }}
										</div>
									@endif
									
                                    <form  method="POST" autocomplete="off" class="myform" action="{{ route('login') }}">
									{{ csrf_field() }}   
        
                                        <div class="mb-3">
											<!--div class="text-danger form-group"-->
												<label class="form-label" for="username">Username</label>
												<input type="text" class="form-control @error('username') is-invalid @enderror  @error('user_email') is-invalid @enderror  @error('mobile') is-invalid @enderror" name="username" value="@if($cookie_username !=''){{$cookie_username }}@endif" id="username" name="username" placeholder="Enter username"  required>  <!-- onClick="test();" -->
											
												@error('username')
													<p class="loginerrormsg" role="alert">
														{{ $message }}
													</p>
												@enderror
												@error('user_email')
													<p class="loginerrormsg" role="alert">
														{{ $message }}
													</p>
												@enderror
												@error('mobile')
													<p class="loginerrormsg" role="alert">
														{{ $message }}
													</p>
												@enderror
											<!--/div-->
										</div>
                
                                        <div class="mb-3">
                                            <!--div class="float-end">
                                                <a href="auth-recoverpw.html" class="text-muted">Forgot password?</a>
                                            </div-->
                                            <label class="form-label" for="userpassword">Password</label>
                                            <input type="password" class="form-control @error('password') is-invalid @enderror" value="@if($cookie_pasword !=''){{$cookie_pasword }}@endif" id="userpassword" name="password" placeholder="Enter password" onClick="test();"  required>
											@error('password')
												<p class="loginerrormsg" role="alert">
													{{ $message }}
												</p>
											@enderror
										</div>
                
				
									<div class="mt-3 row">
										<div class="col-lg-6">
											<div style="display: flex;">
												<div class="form-check">
													<input type="checkbox" class="form-check-input" id="remember_me" name="remember_me" value="true" @if($cookie_pasword !=''){{"Checked"}}@endif style="">
												</div>
													<label class="form-check-label" for="auth-remember-check" style="">Remember me</label>
											</div>	
										</div>
                                        
                                        <div class="right-button col-sm-12 col-md-12 col-lg-6">
											<div>
												<button class="btn btn-primary w-sm waves-effect waves-light" type="submit" style="min-width: 110px;position: absolute;right: 8px;">Log In</button>
											</div>
										</div>
									</div>
                                   
                                    </form>
                                </div>
							
                            </div><!-- card-body -->
                        </div>

                        <div class="mt-3 text-center">                           
							<p style="color:white">© <script>document.write(new Date().getFullYear())</script> <a href="https://www.auto-assure.com/" style="color:white" target="_blank"> Auto Assure</a><a href="https://srvinfotech.com" target="_blank" style="color:white;">&nbsp;Powered by SRV InfoTech</a></p>  <!-- <style="color: #74788d;"> -->
						</div>

                    </div>
                </div>
                <!-- end row -->
            </div>
            <!-- end container -->
        </div>
</div>
        <!-- JAVASCRIPT -->
        <script src="{{asset('assets/libs/jquery/jquery.min.js')}}"></script>
        <script src="{{asset('assets/libs/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
        <script src="{{asset('assets/libs/metismenu/metisMenu.min.js')}}"></script>
        <script src="{{asset('assets/libs/simplebar/simplebar.min.js')}}"></script>
        <script src="{{asset('assets/libs/node-waves/waves.min.js')}}"></script>
        <script src="{{asset('assets/libs/waypoints/lib/jquery.waypoints.min.js')}}"></script>
        <script src="{{asset('assets/libs/jquery.counterup/jquery.counterup.min.js')}}"></script>

        <!-- App js -->
        <script src="{{asset('assets/js/app.js')}}"></script>
        <script src="{{asset('module.js/main.js')}}"></script>
	
    </body>
</html>


<script type="text/javascript">
$("#remember_me").click(function()
{
	var CSRF_TOKEN = "{{ csrf_token() }}";
	
    if($(this).is(':checked'))
	{
		$.ajax({
			type: 'POST',
			dataType:'json',
			data: { '_token': CSRF_TOKEN, 'username': $('#username').val(),'password':$('#userpassword').val() },
			url: "password/setcookie",
			success: function(result) {}
    	});
	}
     
	if(!$(this).is(':checked'))
	{
		$.ajax({
			type: 'POST',
			dataType:'json',
			data: { '_token': CSRF_TOKEN},
			url: "password/getCookie",
			success: function(result) {
			   }
		});
	}

});
</script>
<script src="https://www.gstatic.com/firebasejs/8.2.0/firebase-app.js"></script>
<script src="https://www.gstatic.com/firebasejs/8.2.0/firebase-messaging.js"></script>

<script>
$( document ).ready(function() {
  //startFCM();
});
</script>

<script>
    var firebaseConfig = {
        apiKey: "AIzaSyCJNj8-kgb00Oc_rWPSgNixqQFmqCoTN6c",
        authDomain: "srvinfotech-31f88.firebaseapp.com",
        projectId: "srvinfotech-31f88",
        storageBucket: "srvinfotech-31f88.appspot.com",
        messagingSenderId: "324346137022",
        appId: "1:324346137022:web:45bb2791d44dc22a0111ef",
        measurementId: "G-TNRFBZ5XSN"
    };
    firebase.initializeApp(firebaseConfig);
    const messaging = firebase.messaging();

    function startFCM() {
        
        messaging
            .requestPermission()
            .then(function () {
                return messaging.getToken()
            })
            .then(function (response) {
			
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: '{{ route("store.token") }}',
                    type: 'POST',
                    data: {
                        token: response
                    },
                    dataType: 'JSON',
                    success: function (response) {
                        //alert('Token stored.');
                    },
                    error: function (error) {
						
                       // alert(error);
                    },
                });
            }).catch(function (error) {
				
                //alert(error);
            });
    }
    messaging.onMessage(function (payload) {
        const title = payload.notification.title;
        const options = {
            body: payload.notification.body,
            icon: payload.notification.icon,
        };
        new Notification(title, options);
    });
</script>