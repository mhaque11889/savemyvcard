<?php

namespace App\Http\Controllers;

use App\Http\Controllers\WhatsAppController;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Models\WebhookData;
use App\Models\DataReceived;
use App\Models\DataSent;
use App\Models\User;
use App\Models\Subscription;
use App\Models\User_Subscription;
use App\Models\Members;
use App\Models\Countries;
use App\Models\CountryStates;
use App\Models\CountryStateCities;
use App\Models\Vcard;
use App\Models\UserVcard;
use App\Models\LeadNotification;
use App\Models\CardExtractionDetails;

class RoutingController extends Controller
{
    //
    public function index()
    {
        return view('welcome');
    }

    public function memberSignin()
    {
        return view('member.login');
    }

    public function memberRegister()
    {
        return view('member.register');
    }

    public function memberForgotPassword()
    {
        return view('member.fogotPassword');
    }

    public function memberDashboard()
    {
        $session = session()->get('username');
        if(!$session)
        {
            return view('member.login');
        }
        $user = User::select('isMobileVerified','isemailverified')->where('email','=',$session)->first();
        $isMobileVerified = $user->isMobileVerified;
        $isEmailVerified = $user->isemailverified;
        return view('member.memberDashboard')->with(compact('isMobileVerified', 'isEmailVerified'));
    }

    public function viewVCards()
    {
        $session = session()->get('username');
        if(!$session)
        {
            return view('member.login');
        }
        $user = User::select('isMobileVerified','isemailverified')->where('email','=',$session)->first();
        if($user->isMobileVerified == 0 || $user->isemailverified == 0)
        {
            return redirect('/memberDashboard');
        }

        $userss = User::where('email','=', $session)
            ->with(['vCards' => function($query) {
                $query->select('vcard.id', 'vcard.mobileNo1', 'vcard.firstName', 'vcard.lastName','vcard.middleName', 'vcard.salutation_text', 'vcard.uniqueCode', 'vcard.status');
            }])
            ->first();

        if ($userss && $userss->vCards->isNotEmpty()) {
            // Access the related vCards
            $vcards = $userss->vCards;
            return view('member.vcardList')->with(compact('vcards'));
        }

        return view('member.vcardList');
    }

    public function addNewVCards()
    {
        $session = session()->get('username');
        if(!$session)
        {
            return view('member.login');
        }
        $user = User::select('isMobileVerified','isemailverified')->where('email','=',$session)->first();
        if($user->isMobileVerified == 0 || $user->isemailverified == 0)
        {
            return redirect('/memberDashboard');
        }
        
        $countries = Countries::select('id','name')->get();
        
        $userCardCount = User::where('email', $session)
            ->withCount('vCards') // Count the related vCards
            ->first();
        
        $vCardCount = $userCardCount->v_cards_count; // This will give the count of vCards
        
        $subscription = User::where('email', $session)
            ->with('userSubscription.subscription')
            ->first();

        if($vCardCount >= $subscription->userSubscription->subscription->vcardAllowed)
        {
            $message = "Your plan doesn't allow you to create more than $vCardCount cards. Please upgrade to create more cards";
            return redirect()->back()->withErrors(['error' => $message])->withInput();
        }
        
        return view('member.vcardAddNew')->with(compact('countries'));
    }

    public function editDigitalCard($id)
    {
        $session = session()->get('username');
        if(!$session)
        {
            return view('member.login');
        }

        $vcard = DB::table('vcard')
        ->join('user_vcard as uvc', 'uvc.vcardid', '=', 'vcard.id')
        ->join('users', 'uvc.userid', '=', 'users.id')
        ->where('users.email', $session)
        ->where('vcard.id', $id)
        ->select('vcard.*')
        ->first();

        if(is_null($vcard))
        {
            return redirect()->back()->withErrors(['error' => 'You do not have permission to edit this card.'])->withInput();
        }
        $countries = Countries::select('id','name', 'phonecode')->orderBy('name')->get();

        return view('member.vcardEdit')->with(compact('vcard', 'countries'));
    }

    public function changeInteractionType($id) 
    {
        $session = session()->get('username');
        if(!$session)
        {
            return view('member.login');
        }

        $vcard = DB::table('vcard')
        ->join('user_vcard as uvc', 'uvc.vcardid', '=', 'vcard.id')
        ->join('users', 'uvc.userid', '=', 'users.id')
        ->where('users.email', $session)
        ->where('uvc.vcardid', $id)
        ->select('uvc.*', 'vcard.salutation_text', 'vcard.firstName', 'vcard.middleName', 'vcard.lastName')
        ->first();

        if(is_null($vcard))
        {
            return redirect()->back()->withErrors(['error' => 'You do not have permission to edit this card.'])->withInput();
        }
        
        return view('member.vcardInteractionType', ['vcard' => $vcard]);
    }

    public function leadNotification()
    {
        $session = session()->get('username');
        if(!$session)
        {
            return view('member.login');
        }
        $user = User::select('isMobileVerified','isemailverified')->where('email','=',$session)->first();
        if($user->isMobileVerified == 0 || $user->isemailverified == 0)
        {
            return redirect('/memberDashboard');
        }
        $vcardList = DB::table('lead_notification as ln')
        ->join('users as u', 'ln.user_id', '=', 'u.id')
        ->join('vcard as vc', 'vc.id', '=', 'ln.vcard_id')
        ->join('dataSent as ds', 'ln.message_id', '=', 'ds.messageId')
        ->select(
            'vc.firstName',
            'ln.downloader_name',
            'ln.downloader_no',
            'ln.message_received',
            'ln.created_at',
            'ds.deliveredAt',
            'ds.isError'
        )
        ->where('u.email', $session)
        ->orderBy('ln.created_at', 'DESC')
        ->get();

       return view('member.leadNotification')->with(compact('vcardList'));
    }

    public function digitalCards()
    {
        $session = session()->get('username');
        if(!$session)
        {
            return view('member.login');
        }
        $user = User::select('isMobileVerified','isemailverified')->where('email','=',$session)->first();
        if($user->isMobileVerified == 0 || $user->isemailverified == 0)
        {
            return redirect('/memberDashboard');
        }
        $vcards = DB::table('vcard')
        ->join('user_vcard as uvc', 'uvc.vcardid', '=', 'vcard.id')
        ->join('users', 'uvc.userid', '=', 'users.id')
        ->where('users.email', $session)
        ->select('vcard.uniqueCode','vcard.salutation_text','vcard.firstName','vcard.middleName','vcard.lastName')
        ->get();

        return view('member.digitalCards')->with(compact('vcards'));
        
    }

    public function viewProcessedBusinessCard()
    {
        $session = session()->get('username');
        if(!$session)
        {
            return view('member.login');
        }
        $user = User::with('vCards')->select('id','isMobileVerified','isemailverified','mobileno')->where('email','=',$session)->first();
        $vcards = $user->vCards;
        $mobileNumbers = $vcards->pluck('mobileNo1')->toArray();

        if($user->isMobileVerified == 0 || $user->isemailverified == 0)
        {
            return redirect('/memberDashboard');
        }

        $cards = CardExtractionDetails::join('dataReceived', 'card_extraction_details.media_id','=','dataReceived.mediaURL')
                ->select('card_extraction_details.media_id','card_extraction_details.aiResponse','dataReceived.senderPhoneNo','dataReceived.senderProfileName', 'card_extraction_details.created_at', 'card_extraction_details.updated_at')
                ->where('card_extraction_details.isProcessedByAi','=',2)->whereIn('dataReceived.senderPhoneNo', $mobileNumbers)
                ->orderBy('created_at', 'DESC')->get();
        
        return view('member.viewProcessedCards', ["cards" => $cards]);
    }

    public function rot13($input) {
        $output = '';
        $length = strlen($input);
    
        for ($i = 0; $i < $length; $i++) {
            $charCode = ord($input[$i]);
    
            if ($charCode >= 65 && $charCode <= 90) {  // Uppercase letters (A-Z)
                $output .= chr((($charCode - 65 + 13) % 26) + 65);
            } elseif ($charCode >= 97 && $charCode <= 122) {  // Lowercase letters (a-z)
                $output .= chr((($charCode - 97 + 13) % 26) + 97);
            } else {
                $output .= $input[$i];  // Keep non-alphabetic characters unchanged
            }
        }
    
        return $output;
    }

    public function digitalCardsNew($uniqueCode)
    {
        $session = session()->get('username');
        if(!$session)
        {
            return view('member.login');
        }
        $user = User::select('isMobileVerified','isemailverified')->where('email','=',$session)->first();
        if($user->isMobileVerified == 0 || $user->isemailverified == 0)
        {
            return redirect('/memberDashboard');
        }
        $vcard = Vcard::where('uniqueCode','=', $uniqueCode)->first();
        return view('member.digitalCardsDownload')->with(compact('vcard'));
    }

    public function digitalCardsDownloadAsImage($vcardId)
    {
        $vcard = Vcard::where('id','=', $vcardId)->first();
        return view('member.digitalCardsDownloadAsImage')->with(compact('vcard'));
    }

    public function apiIntegrations()
    {
        $session = session()->get('username');
        if(!$session)
        {
            return view('member.login');
        }
        $user = User::select('isMobileVerified','isemailverified','id','password')->where('email','=',$session)->first();

        $token = $user->id."#$".$user->password;
        $newToken = $this->rot13($token);

        if($user->isMobileVerified == 0 || $user->isemailverified == 0)
        {
            return redirect('/memberDashboard');
        }
        return view('member.apiIntegration')->with(compact('newToken'));
    }

    public function changePassword()
    {
        $session = session()->get('username');
        if(!$session)
        {
            return view('member.login');
        }
        
        return view('member.changePassword');
    }

    public function sendTestMessage()
    {
        $whatsapp = new WhatsAppController();
        $whatsapp->sendTextMessage('919718793639', 'This is a test message');
    }

    public function contactCard()
    {
        $whatsapp = new WhatsAppController();
        $contactArr = $whatsapp->createVCard('1326');

        $whatsapp->sendContactCard('919718793639', $contactArr);
    }

    public function qrCode()
    {
        return view('member.createQRCode');
    }

    public function createVcfCards($cardcode)
    {
        $vcardss = Vcard::where('uniqueCode','=',$cardcode)->first();
        // Define the VCard content
        $vcard = "BEGIN:VCARD\r\n";
        $vcard .= "VERSION:3.0\r\n";

        $vcard .= "N:$vcardss->lastName;$vcardss->firstName;$vcardss->middleName;$vcardss->salutation_text;\r\n";
        $vcard .= "FN:".$vcardss->salutation_text."".$vcardss->firstName."".$vcardss->middleName."".$vcardss->lastName."\r\n"; // Full Name
        
        $fileName = $vcardss->firstName."".$vcardss->middleName."".$vcardss->lastName.".vcf";

        if(strlen($vcardss->companyName) > 0)
        {
        $vcard .= "ORG:$vcardss->companyName\r\n"; // Organization
        }
        if(strlen($vcardss->jobTitle) > 0)
        {
        $vcard .= "TITLE:$vcardss->jobTitle\r\n"; // Job tiTLe
        }

        if(strlen($vcardss->mobileNo1) > 5)
        {
        $vcard .= "TEL;TYPE=WORK,VOICE:+$vcardss->mobileNo1\r\n"; // Work Phone
        }
        if(strlen($vcardss->mobileNo2) > 5)
        {
        $vcard .= "TEL;TYPE=WORK,VOICE:+$vcardss->mobileNo2\r\n"; // Work Phone
        }

        if(strlen($vcardss->emailid1) > 5)
        {
        $vcard .= "EMAIL;TYPE=WORK,INTERNET:$vcardss->emailid1\r\n"; // Work Email
        }
        if(strlen($vcardss->emailid2) > 5)
        {
        $vcard .= "EMAIL;TYPE=WORK,INTERNET:$vcardss->emailid2\r\n"; // Work Email
        }

        $addressArr = [];
        $address = [];
        $address["type"] = 'Work';
        if(strlen($vcardss->addressLine1) > 0)
        {
        $address['street'] = $vcardss->addressLine1;
        }
        if(strlen($vcardss->addressLine1) > 0 && strlen($vcardss->addressLine2) > 0)
        {
        $address['street'] = $vcardss->addressLine1.", ".$vcardss->addressLine2;
        }
        if(strlen($vcardss->city) > 0)
        {
        $address['city'] = $vcardss->city;
        }
        if(strlen($vcardss->state) > 0)
        {
        $address['state'] = $vcardss->state;
        }
        if(strlen($vcardss->zipcode) > 0)
        {
        $address['zip'] = $vcardss->zipcode;
        }
        if(strlen($vcardss->country) > 0)
        {
        $address['country'] = $vcardss->country;
        }

        $urlArr = [];

        if(strlen($vcardss->facebookURL) > 5)
        {
        $url1 = array(
        "url" => $vcardss->facebookURL,
        "type" => "Facebook Page"
        );
        $vcard .= "URL;TYPE=WORK:$vcardss->facebookURL\r\n"; // Work Website
        }

        if(strlen($vcardss->instaURL) > 5)
        {
        $vcard .= "URL;TYPE=WORK:$vcardss->instaURL\r\n"; // Work Website
        }

        if(strlen($vcardss->twitterURL) > 5)
        {
        $vcard .= "URL;TYPE=WORK:$vcardss->twitterURL\r\n"; // Work Website
        }

        if(strlen($vcardss->linkdnURL) > 5)
        {
        $vcard .= "URL;TYPE=WORK:$vcardss->linkdnURL\r\n"; // Work Website
        }

        if(strlen($vcardss->bniURL) > 5)
        {
        $vcard .= "URL;TYPE=WORK:$vcardss->bniURL\r\n"; // Work Website
        }

        if(strlen($vcardss->linkdinCompany) > 5)
        {
        $vcard .= "URL;TYPE=WORK:$vcardss->linkdinCompany\r\n"; // Work Website
        }

        if(strlen($vcardss->youtubeChannel) > 5)
        {
        $vcard .= "URL;TYPE=WORK:$vcardss->youtubeChannel\r\n"; // Work Website
        }

        if(strlen($vcardss->indiamartLink) > 5)
        {
        $vcard .= "URL;TYPE=WORK:$vcardss->indiamartLink\r\n"; // Work Website
        }

        if(strlen($vcardss->tradeIndia) > 5)
        {
        $vcard .= "URL;TYPE=WORK:$vcardss->tradeIndia\r\n"; // Work Website
        }

        if(strlen($vcardss->bniPublicProfile) > 5)
        {
        $vcard .= "URL;TYPE=WORK:$vcardss->bniPublicProfile\r\n"; // Work Website
        }

        if(strlen($vcardss->website1) > 5)
        {
        $vcard .= "URL;TYPE=WORK:$vcardss->website1\r\n"; // Work Website
        }



        if(sizeof($address) > 3)
        {
        $vcard .= "ADR;TYPE=Work:;;".$address['street'].";".$address['city'].";".$address['state'].";".$address['zip'].";".$address['country']."\r\n"; // Home Address
        $vcard .= "LABEL;TYPE=Work:".$address['street']."\n".$address['city']."\n".$address['state']."\n".$address['zip']."\n".$address['country']."\r\n"; // Home Address
        }

        $vcard .= "END:VCARD\r\n";
        
        $fileDownloadString = 'attachment; filename="'.$fileName.'"';
        return response($vcard)
            ->header('Content-Type', 'text/vcard')
            ->header('Content-Disposition', $fileDownloadString);
    }

    public function viewProcessedBusinessCardData(Request $request)
    {
        $session = session()->get('username');
        if(!$session)
        {
            return view('member.login');
        }
        
        // Build the query with date filtering
        $query = CardExtractionDetails::join('dataReceived', 'card_extraction_details.media_id','=','dataReceived.mediaURL')
                ->join('users', 'dataReceived.senderPhoneNo','=','users.mobileno')
                ->where('card_extraction_details.isProcessedByAi','=',2)
                ->where('users.email', $session);
        
        // Apply date filters if provided
        if ($request->has('start_date') && $request->start_date) {
            $query->whereDate('card_extraction_details.created_at', '>=', $request->start_date);
        }
        
        if ($request->has('end_date') && $request->end_date) {
            $query->whereDate('card_extraction_details.created_at', '<=', $request->end_date);
        }
        
        $cards = $query->orderBy('card_extraction_details.created_at', 'DESC')->get();
        
        return view('member.viewProcessedBusinessCardData', ['cards' => $cards]);
    }
}
