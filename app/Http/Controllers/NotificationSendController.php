<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use Modules\Users\Entities\UsersModel;

class NotificationSendController extends Controller
{
    
    public function updateDeviceToken(Request $request)
    {
        Auth::user()->device_token =  $request->token;
        Auth::user()->save();

        return response()->json(['Token successfully stored.']);
    }
    public function sendNotification(Request $request)
    {
        $url = 'https://fcm.googleapis.com/fcm/send';

        $FcmToken = UsersModel::whereNotNull('device_token')->where('device_token','!=','')->pluck('device_token')->all();
            
		
        $serverKey = 'AAAAS4SJSb4:APA91bETROL_sH3M0JOVHlzaxX7v-9OfQ3jucYmP1qSqiQtHMAd2z1a8gxwTOsisp6DQcQ84o3bm-gpUyAiPpyXhvwv4qKBZhjx_8ARkv8xMT0v6u6ORt5e9n0-ETJyrU3NQKQKEmqdO'; // ADD SERVER KEY HERE PROVIDED BY FCM
    
        $data = [
            "registration_ids" =>$FcmToken,
            "notification" => [
                "title" => $request->title,
                "body" => $request->body,  
            ]
        ];
        $encodedData = json_encode($data);
		$headers = array(
			'Authorization:key='.$serverKey,
			'Content-Type:application/json'
        ); 
    
     $curl = curl_init();

		curl_setopt_array($curl, array(
		  CURLOPT_URL => 'https://fcm.googleapis.com/fcm/send',
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 10,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'POST',
		  CURLOPT_POSTFIELDS =>$encodedData,
		  CURLOPT_HTTPHEADER => $headers,
		));

		$response = curl_exec($curl);

		curl_close($curl);
		
		return ;
    }

}

