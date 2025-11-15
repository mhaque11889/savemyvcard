<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\BusinessLogicController;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Models\Admin;
use App\Models\User;
use App\Models\UserVcard;
use App\Models\Vcard;
use App\Models\LeadNotification;
use App\Models\DataSent;
use App\Models\DaraReceived;
use App\Models\CardExtractionDetails;


class AdminController extends Controller
{
    //
    public function index()
    {
        return view('admin.login',['error' => '']);
    }

    public function loginAdmin(Request $request)
    {
        $username = $request['username'];
        $password = md5($request['password']);

        $admin = Admin::select('username','password', 'usertype')->where('username','=',trim($username))->first();
        
        if(is_null($admin))
        {
            return back()->with('message', 'The username you entered is not found.');
        }
        else if(strcmp($password, $admin['password']) == 0)
        {
            $request->session()->put('username', $admin['username']);
            $request->session()->put('userlevel', $admin['usertype']);
            $request->session()->put('usertype', 'admin');
            return redirect('/adminDashboard');
        }
        else{
            return back()->with('message', 'The Username or password incorrect. Plz check if you are using your correct admin credentials');
        }
    }

    public function adminDashboard()
    {
        $session = session()->get('usertype');
        $error = '';
        if(!$session)
        {
            return view('admin.login',['error' => $error]);
        }
        else if($session !== 'admin')
        {
            $error = 'You are not authorised to view this page';
            return view('admin.login',['error' => $error]);
        }

        $data = LeadNotification::join('vcard', 'vcard.id', '=', 'lead_notification.vcard_id')
            ->join('dataSent', 'lead_notification.message_id', '=', 'dataSent.messageId')
            ->select(
                'vcard.salutation_text',
                'vcard.firstName',
                'vcard.middleName',
                'vcard.lastName',
                'vcard.uniqueCode',
                DB::raw('COUNT(vcard.uniqueCode) as download_number'),
                DB::raw('MAX(lead_notification.created_at) as last_download_at')
            )
            ->groupBy('vcard.salutation_text', 'vcard.firstName', 'vcard.middleName', 'vcard.lastName','vcard.uniqueCode')
            ->orderByDesc(DB::raw('COUNT(vcard.uniqueCode)'))
            ->limit(10)
            ->get();

        return view('admin.adminDashboard', ['data' => $data]);
    }

    public function loginImpersonate($id)
    {
        $session = session()->get('usertype');
        $error = '';
        if(!$session)
        {
            return view('admin.login',['error' => $error]);
        }
        else if($session !== 'admin')
        {
            $error = 'You are not authorised to view this page';
            return view('admin.login',['error' => $error]);
        }

        $user = User::select('email','mobileno','password')->where('id','=',trim($id))->first();

        if(is_null($user))
        {
            return back()->with('message', 'The Username or password incorrect. Plz check if you are using your full email address');
        }else
        {
            //548496 - manzar2004@gmail.com
            $subscription = User::where('email', $user->email)
            ->with('userSubscription.subscription')
            ->first();
            session()->flush();
            session()->put('subscription',$subscription->userSubscription->subscription->subscriptionName);
            session()->put('mobile', $user['mobileno']);
            session()->put('username', $user['email']);
            session()->put('usertype', 'member');
            return redirect('/memberDashboard');
        }
    }

    public function registeredUsers()
    {
        $session = session()->get('usertype');
        $error = '';
        if(!$session)
        {
            return view('admin.login',['error' => $error]);
        }
        else if($session !== 'admin')
        {
            $error = 'You are not authorised to view this page';
            return view('admin.login',['error' => $error]);
        }

        $users = DB::table('users as u')
                ->select('u.id','u.mobileno', 'u.email', 'u.isemailverified', 'u.isMobileVerified', 'u.created_at', DB::raw('COUNT(uvc.vcardid) as vcardcount'))
                ->leftJoin('user_vcard as uvc', 'u.id', '=', 'uvc.userid')
                ->groupBy('u.id','u.mobileno', 'u.email', 'u.isemailverified', 'u.isMobileVerified', 'u.created_at')
                ->orderBy('u.created_at','DESC')
                ->get();

        return view('admin.registeredUsers')->with(compact('users'));
    }

    public function nonRegisteredUsers()
    {
        $session = session()->get('usertype');
        $error = '';
        if(!$session)
        {
            return view('admin.login',['error' => $error]);
        }
        else if($session !== 'admin')
        {
            $error = 'You are not authorised to view this page';
            return view('admin.login',['error' => $error]);
        }

        $users = DataSent::select(
                    'dataSent.sentTo',
                    'dataReceived.senderProfileName',
                    DB::raw('MAX(dataSent.created_at) as lastUsed'),
                    DB::raw('COUNT(dataSent.created_at) as number_of_time')
                )
                ->join('dataReceived', 'dataReceived.senderPhoneNo', '=', 'dataSent.sentTo')
                ->where('dataSent.messagetype', 'ContactCard')->where('dataSent.deliveredAt','>','0')
                ->whereNotIn('dataSent.sentTo', function ($query) {
                    $query->select('mobileNo1')->from('vcard');
                })
                ->groupBy('dataSent.sentTo', 'dataReceived.senderProfileName')
                ->orderByRaw('MAX(dataSent.created_at) DESC')
                ->get();

        return view('admin.nonRegisteredUsers')->with(compact('users'));
    }



    public function registeredUserDetails($userid)
    {
        $session = session()->get('usertype');
        $error = '';
        if(!$session)
        {
            return view('admin.login',['error' => $error]);
        }
        else if($session !== 'admin')
        {
            $error = 'You are not authorised to view this page';
            return view('admin.login',['error' => $error]);
        }

        $users = DB::table('users as u')
                ->select('u.mobileno', 'u.email', 'u.isemailverified', 'u.isMobileVerified', 'u.created_at', DB::raw('COUNT(uvc.vcardid) as vcardcount'))
                ->leftJoin('user_vcard as uvc', 'u.id', '=', 'uvc.userid')
                ->groupBy('u.mobileno', 'u.email', 'u.isemailverified', 'u.isMobileVerified', 'u.created_at')
                ->orderBy('u.created_at','DESC')
                ->get();

        return view('admin.registeredUsers')->with(compact('users'));
    }

    public function userProfile($id)
    {
        $session = session()->get('usertype');
        $error = '';
        if(!$session)
        {
            return view('admin.login',['error' => $error]);
        }
        else if($session !== 'admin')
        {
            $error = 'You are not authorised to view this page';
            return view('admin.login',['error' => $error]);
        }
        //User Details
        $user = User::select('id', 'mobileno', 'email', 'isemailverified', 'isActive', 'country', 'forgotpassword', 'isMobileVerified', 'created_at', 'updated_at', 'last_login')->where('id','=',$id)->first();
        //Vcard Created By User and its status
        $vcards = Vcard::join('user_vcard','user_vcard.vcardid','vcard.id')
                    ->select('vcard.id','vcard.salutation_text','vcard.firstName','vcard.middleName','vcard.lastName','vcard.uniqueCode','vcard.status','vcard.premiumCode')
                    ->where('user_vcard.userid','=',$id)->get();
        //Download Information of Vcards
        $downloads = LeadNotification::join('vcard','vcard.id','lead_notification.vcard_id')->join('dataSent','lead_notification.message_id','dataSent.messageId')
                    ->select('vcard.salutation_text','vcard.firstName','vcard.middleName','vcard.lastName','vcard.uniqueCode','lead_notification.downloader_name','lead_notification.downloader_no','lead_notification.created_at','dataSent.deliveredAt','dataSent.isError')
                    ->where('lead_notification.user_id','=',$id)->get();
        
        return view('admin.userProfile', ['user' => $user, 'vcards' => $vcards, 'downloads' => $downloads]);
    }

    public function verifyEmailByAdmin($userid)
    {
        $session = session()->get('usertype');
        $error = '';
        if(!$session)
        {
            return view('admin.login',['error' => $error]);
        }
        else if($session !== 'admin')
        {
            $error = 'You are not authorised to view this page';
            return view('admin.login',['error' => $error]);
        }

        User::where('id','=', $userid)->update(['isemailverified' => 1]);
        return redirect()->back();
    }

    public function verifyMobileByAdmin($userid)
    {
        $session = session()->get('usertype');
        $error = '';
        if(!$session)
        {
            return view('admin.login',['error' => $error]);
        }
        else if($session !== 'admin')
        {
            $error = 'You are not authorised to view this page';
            return view('admin.login',['error' => $error]);
        }
        User::where('id','=', $userid)->update(['isMobileVerified' => 1]);
        return redirect()->back();
    }

    public function verifyVcardByAdmin($vcardid)
    {
        $session = session()->get('usertype');
        $error = '';
        if(!$session)
        {
            return view('admin.login',['error' => $error]);
        }
        else if($session !== 'admin')
        {
            $error = 'You are not authorised to view this page';
            return view('admin.login',['error' => $error]);
        }
        Vcard::where('id','=', $vcardid)->update(['status' => 1]);
        return redirect()->back();
    }

    public function failedMessages()
    {
        $session = session()->get('usertype');
        $error = '';
        if(!$session)
        {
            return view('admin.login',['error' => $error]);
        }
        else if($session !== 'admin')
        {
            $error = 'You are not authorised to view this page';
            return view('admin.login',['error' => $error]);
        }
        $failedMessages = DB::table('dataSent as ds')
                        ->leftJoin('users as u', 'ds.sentTo', '=', 'u.mobileno') // Join users on mobileno
                        ->leftJoin('lead_notification', 'lead_notification.downloader_no', '=', 'ds.sentTo') // Join lead_notification on downloader_no
                        ->whereNotNull('ds.isError') // Filter rows where isError is not null
                        ->select(
                            'u.mobileno',
                            'u.email',
                            'ds.sentTo',
                            'lead_notification.downloader_name',
                            'ds.messageType',
                            'ds.contents',
                            'ds.errorMessage',
                            'ds.created_at'
                        )
                        ->orderBy('ds.created_at','DESC')
                        ->get();

        return view('admin.failedMessages',['failedMessages' => $failedMessages]);
    }

    public function viewProcessedCards()
    {
        $session = session()->get('usertype');
        $error = '';
        if(!$session)
        {
            return view('admin.login',['error' => $error]);
        }
        else if($session !== 'admin')
        {
            $error = 'You are not authorised to view this page';
            return view('admin.login',['error' => $error]);
        }
        $cards = CardExtractionDetails::join('dataReceived', 'card_extraction_details.media_id','=','dataReceived.mediaURL')
                ->select('card_extraction_details.media_id','card_extraction_details.aiResponse','dataReceived.senderPhoneNo','dataReceived.senderProfileName', 'card_extraction_details.created_at', 'card_extraction_details.updated_at')
                ->where('card_extraction_details.isProcessedByAi','=',2)->where('dataReceived.senderPhoneNo','<>','919718793639')->orderBy('created_at','DESC')->get();
        
        foreach($cards as $card){
            $card->processed_at = DataSent::select('sentTo','messageType','isError','created_at')->where('sentTo','=',$card->senderPhoneNo)->where('created_at', '>', $card->created_at)->where('messageType','ContactCard')->first();
        }

        return view('admin.viewProcessedCards', ["cards" => $cards]);
    }

    public function viewAllVcards()
    {
        $session = session()->get('usertype');
        $error = '';
        if(!$session)
        {
            return view('admin.login',['error' => $error]);
        }
        else if($session !== 'admin')
        {
            $error = 'You are not authorised to view this page';
            return view('admin.login',['error' => $error]);
        }
        $cards = VCard::select('user_vcard.userid','vcard.salutation_text',
            'vcard.firstName',
            'vcard.middleName',
            'vcard.lastName',
            'vcard.uniqueCode',
            'vcard.mobileNo1',
            'vcard.jobTitle',
            'vcard.companyName',
            'vcard.status',
            'vcard.created_at')->leftJoin('user_vcard','user_vcard.vcardid','vcard.id')
            ->orderBy('vcard.created_at', 'desc')->get();
        
        return view('admin.viewVCards', ["cards" => $cards]);
    }

    public function addAlternateQRCode(Request $request)
    {
       
        $vcardid = $request->vcardid;
        $type = $request->type;
        $alternateQR = $request->value;
        // Check if the alternate QR code already exists
        if(strcmp($type, 'alternateCode') == 0){
            $exists = Vcard::where('uniqueCode', $alternateQR)
            ->orWhere('premiumCode', $alternateQR)
            ->exists();
            if (empty($exists)) {
                Vcard::where('id', $vcardid)->update(['premiumCode' => $alternateQR]);
                return response()->json(['status' => 'success']);
            }
        }
        else if(strcmp($type, 'mobileStickerCode') ==0 )
        {
            $exists = Vcard::where('mobileStickerCode', $alternateQR)
            ->exists();
            if (empty($exists)) {
                Vcard::where('id', $vcardid)->update(['mobileStickerCode' => $alternateQR]);
                return response()->json(['status' => 'success']);
            }
        }
        else if(strcmp($type, 'tentCardCode') ==0 )
        {
            $exists = Vcard::where('tentCardCode', $alternateQR)
            ->exists();
            if (empty($exists)) {
                Vcard::where('id', $vcardid)->update(['tentCardCode' => $alternateQR]);
                return response()->json(['status' => 'success']);
            }
        }

        return response()->json(['status' => 'failed']);
    }

    public function viewBusinessCardData()
    {
        $session = session()->get('usertype');
        $error = '';
        if(!$session)
        {
            return view('admin.login',['error' => $error]);
        }
        else if($session !== 'admin')
        {
            $error = 'You are not authorised to view this page';
            return view('admin.login',['error' => $error]);
        }

        $bl = new BusinessLogicController();
        $bl->updateScannedCard();

        return view('admin.businessCardDataFilterPage');
    }

    public function filterBusinessCardData(Request $request)
    {
        $request->validate([
            'startDate' => ['required', 'date'],
            'endDate' => ['required', 'date', 'after:startDate'],
        ], [
            'startDate.required' => 'Start date is required.',
            'startDate.date' => 'Start date must be a valid date.',
            'endDate.required' => 'End date is required.',
            'endDate.date' => 'End date must be a valid date.',
            'endDate.after' => 'End date must be after the start date.'
        ]);

        $extractedCards = CardExtractionDetails::where('created_at','>=',$request->startDate)->where('created_at','<=', $request->endDate)->get();
        return view('admin.filteredBusinessCardData', [ 'extractedCards' => $extractedCards]);
    }
}
