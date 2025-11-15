<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\WebhookData;
use App\Models\DataReceived;
use App\Models\DataSent;
use App\Models\User;
use App\Models\Subscription;
use App\Models\User_Subscription;
use App\Models\Members;
use App\Models\Vcard;
use App\Models\UserVcard;
use App\Models\LeadNotification;


class LoginController extends Controller
{
    //
    public function loginMember(Request $request)
    {
        $username = $request['username'];
        $password = md5($request['password']);

        $user = User::select('email','mobileno','password')->where('email','=',trim($username))->orWhere('mobileno', '=', trim($username))->first();
        
        if(is_null($user))
        {
            return back()->with('message', 'The username you entered is not found.');
        }
        else if(strcmp($password, $user['password']) == 0)
        {
            //548496 - manzar2004@gmail.com
            $subscription = User::where('email', $user->email)
            ->with('userSubscription.subscription')
            ->first();

            User::where('email', $user['email'])->update(['last_login' => now()]);
            $request->session()->put('subscription',$subscription->userSubscription->subscription->subscriptionName);
            $request->session()->put('mobile', $user['mobileno']);
            $request->session()->put('username', $user['email']);
            $request->session()->put('usertype', 'member');
            return redirect('/memberDashboard');
        }
        else{
            return back()->with('message', 'The Username or password incorrect. Plz check if you are using your full email address');
        }
    }

    public function registerMember(Request $request)
    {
        try {
            session_start();

            if($request->password !== $_SESSION['captcha'])
            {
                echo "Captcha Doesnot match<br/>";
                return back()->with('message', 'Captcha Code does not match. Please try again');
            }

            $request->validate([
                'username' => 'required|email',
                'mobileNumber' => 'required|min:10',
                'parent1contactcc' => 'required|min:2',
            ], [
                'username.required' => 'The email is required.',
                'username.email' => 'Please enter a valid email address.',
                'mobileNumber.required' => 'The mobile number is required.',
                'mobileNumber.min' => 'The mobile number must be at least 10 digits long.',
                'parent1contactcc.required' => 'The country code is required.',
                'parent1contactcc.min' => 'The country code must be at least 2 characters long.',
            ]);

            $username = $request->username;
            $ccode = $request->parent1contactcc;
            $mobileNumber = $ccode.''.$request->mobileNumber;

            $users = User::where('email','=',$username)->orWhere('mobileno', '=', $mobileNumber)->get();
            if($users->isNotEmpty())
            {
                return back()->with('message', 'The email / mobilenumber already registered. Please use forgot password link to reset password');
            }
            $randomNumber = random_int(100000, 999999);

            $user = new User();
            $user->mobileno = $mobileNumber;
            $user->email = $username;
            $user->password = md5($randomNumber);
            $user->isemailverified = '0';
            $user->isActive = '0';
            $user->country = $ccode;
            $user->forgotpassword = '0';
            $user->isMobileVerified = '0';
            $user->save();

            $insertedId = $user->id;

            $subscription = Subscription::find(1);

            $user_sub = new User_Subscription();
            $user_sub->userid = $insertedId;
            $user_sub->subscriptionid = '1';
            $user_sub->start_date = now();
            $user_sub->end_date = now()->addDays($subscription->daysAllowed);
            $user_sub->vcard_count = $subscription->vcardAllowed;
            $user_sub->scan_count = $subscription->vcardAllowed;
            $user_sub->is_active = true;
            $user_sub->save();
            
            $subs = Subscription::select('subscriptionName')->where('id','=','1')->first();

            $request->session()->put('mobile', $mobileNumber);
            $request->session()->put('username', $username);
            $request->session()->put('password', $randomNumber);
            $request->session()->put('usertype', 'member');
            $request->session()->put('subscription', $subs->subscriptionName);
            
            return redirect('/memberDashboard');
        } catch (\Exception $e) {
            \Log::error('Registration error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return back()->with('message', 'An error occurred during registration. Please try again.');
        }
    }

    public function autoRegisterMember($email, $mobileNumber, $country)
    {
        $username = $email;
        $ccode = $country;
        $mobileNumber = $mobileNumber;

        $user = User::where('email','=',$username)->orWhere('mobileno', '=', $mobileNumber)->first();

        if(!is_null($user))
        {
            return [99];
        }

        $randomNumber = random_int(100000, 999999);

        $user = new User();
        $user->mobileno = $mobileNumber;
        $user->email = $username;
        $user->password = md5($randomNumber);
        $user->isemailverified = '0';
        $user->isActive = '1';
        $user->country = $ccode;
        $user->forgotpassword = '0';
        $user->isMobileVerified = '1';
        $user->save();

        $insertedId = $user->id;

        $user_sub = new User_Subscription();
        $user_sub->userid = $insertedId;
        $user_sub->subscriptionid = '1';
        $user_sub->save();
        
        return [$randomNumber, $insertedId];
    }

    public function logout(){
        session()->flush();
        return redirect('/signin');
    }
}
