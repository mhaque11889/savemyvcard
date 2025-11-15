<?php

namespace App\Http\Controllers;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use App\Http\Controllers\OpenAIController;
use App\Http\Controllers\WhatsAppController;
use Illuminate\Http\Request;
use App\Mail\EmailVerification;
use App\Models\WebhookData;
use App\Models\DataReceived;
use App\Models\DataSent;
use App\Models\User;
use App\Models\Subscription;
use App\Models\Members;
use App\Models\Countries;
use App\Models\CountryStates;
use App\Models\CountryStateCities;
use App\Models\Vcard;
use App\Models\UserVcard;
use App\Models\User_Subscription;
use App\Models\LeadNotification;
use App\Models\CardExtractionDetails;

class BusinessLogicController extends Controller
{

    public function sendOTPEmail(Request $request)
    {
        try{
            $r = $request->message;
            $userid = $r['userId'];            
            $mailData['OTP'] = $r['otp'];
            
            if(strlen($userid)>0) 
            {
                Mail::to($userid)->send(new EmailVerification($mailData));
            }
            echo "success";
        }
        catch(\Throwable $th)
        {
            Log::error('Error in Send OTP Email: '.$th->getMessage());
        }
    }

    public function emailVerified(Request $request)
    {
        try{
            $r = $request->message;
            $userid = $r['userId'];            
            User::where('email','=',$userid)->update(['isemailverified' => 1]);
            echo "success";
        }
        catch(\Throwable $th)
        {
            Log::error('Error in Email Verify Function: '.$th->getMessage());
        }
    }

    public function getStates(Request $request)
    {
        try {
            $r = $request->message;
            $countryId = $r['countryId'];
            $states = CountryStates::select('id', 'name')->where('country_id','=',$countryId)->orderBy('name')->get();
            return response()->json($states);
        } catch (\Throwable $th) {
            Log::error('Error in get states: '.$th->getMessage());
        }
    }

    public function getAllAlternateCodes(Request $request)
    {
        try {
            $vcardid = $request->vcardid;
            $vcard = Vcard::select('id','salutation_text','firstName','middleName','lastName','uniqueCode','premiumCode','mobileStickerCode','tentCardCode')->where('id','=',$vcardid)->first();
            $alternateCodes = [
                'status'    => 'success',
                'vcardid' => $vcard->id,
                'name' => $vcard->salutation_text.' '.$vcard->firstName.' '.$vcard->middleName.' '.$vcard->lastName,
                'uniqueCode' => $vcard->uniqueCode,
                'premiumCode' => $vcard->premiumCode,
                'mobileStickerCode' => $vcard->mobileStickerCode,
                'tentCardCode' => $vcard->tentCardCode
            ];
            return response()->json($alternateCodes);
        } catch (\Throwable $th) {
            //throw $th;
            Log::error("message".$th->getMessage());
            return response()->json(['status' => 'failed', 'message' => 'Error in fetching alternate codes']);
        }
    }

    public function getCities(Request $request)
    {
        try {
            $r = $request->message;
            $stateid = $r['stateid'];
            $cities = CountryStateCities::select('id', 'name')->where('state_id','=',$stateid)->orderBy('name')->get();
            return response()->json($cities);
        } catch (\Throwable $th) {
            Log::error('Error in get cities: '.$th->getMessage());
        }
    }

    public function saveVCard(Request $req)
    {
        try {
            echo 'Salutation : '.$req->salutation_text;
            $country = ''; $state = ''; $city = '';
            //exit;
            $randomCode = $this->generateRandomCode();
            if(isset($req->country)){
                $country = Countries::select('name')->where('id','=',$req->country)->first();
                $country = $country->name;
            }

            if(isset($req->state)){
                $state = CountryStates::select('name')->where('id','=',$req->state)->first();
                $state = $state->name;
            }
            
            if(isset($req->city))
            {
                $city = CountryStateCities::select('name')->where('id','=',$req->city)->first();
                $city = $city->name;
            }


            $userid = User::select('id')->where('email','=', $req->useremail)->first();

            $vcard = new Vcard();
            $vcard->salutation_text = trim($req->salutation_text);
            $vcard->firstName = trim($req->firstName);
            $vcard->middleName = $req->middleName;
            $vcard->lastName = trim($req->lastName);
            $vcard->jobTitle = $req->jobTitle;
            $vcard->companyName = $req->companyName;
            $vcard->industry = $req->industry;
            $vcard->subIndustry = $req->subIndustry;
            $vcard->emailid1 = $req->emailid1;
            $vcard->emailid2 = $req->emailid2;
            $vcard->mobileNo1 = $req->cc1.$req->mobileNo1;
            $vcard->countryCode1 = $req->cc1;
            $vcard->mobileNo2 = $req->cc2.$req->mobileNo2;
            $vcard->countryCode2 = $req->cc2;
            $vcard->addressLine1 = $req->addressLine1;
            $vcard->addressLine2 = $req->addressLine2;
            $vcard->country = $country;
            $vcard->state = $state;
            $vcard->city = $city;
            $vcard->zipcode = $req->zipcode;
            $vcard->linkdnURL = $req->linkdnURL;
            $vcard->twitterURL = $req->twitterURL;
            $vcard->instaURL = $req->instaURL;
            $vcard->facebookURL = $req->facebookURL;
            $vcard->indiamartLink = $req->youtubeChannel;
            $vcard->indiamartLink = $req->indiamartLink;
            $vcard->bniURL = $req->bniURL;
            $vcard->tradeIndia = $req->tradeIndia;
            $vcard->bniPublicProfile = $req->bniPublicProfile;
            $vcard->website1 = $req->website1;
            $vcard->uniqueCode = $randomCode.'SMVC';
            $vcard->save();

            $userVcard = new UserVcard();
            $userVcard->userid = $userid->id;
            $userVcard->vcardid = $vcard->id;
            $userVcard->save();

            return redirect('/vcards');
        } catch (\Throwable $th) {
            //throw $th;
            Log::error('Error in save vcard: '.$th->getMessage());
        }
    }

    public function updateVCard(Request $req)
    {
        try {
            $country = ''; $state = ''; $city = '';
            //exit;
            if(isset($req->country) && ctype_digit($req->country)){
                $country = Countries::select('name')->where('id','=',$req->country)->first();
                $country = $country->name;
            } else { $country = $req->country; }

            if(isset($req->state) && ctype_digit($req->state)){
                $state = CountryStates::select('name')->where('id','=',$req->state)->first();
                $state = $state->name;
            } else { $state = $req->state; }
            
            if(isset($req->city) && ctype_digit($req->city))
            {
                $city = CountryStateCities::select('name')->where('id','=',$req->city)->first();
                $city = $city->name;
            } else { $city = $req->city; }


            $userid = User::select('id')->where('email','=', $req->useremail)->first();

            $vcard = Vcard::find($req->vcardid);
            $vcard->salutation_text = trim($req->salutation_text);
            $vcard->firstName = trim($req->firstName);
            $vcard->middleName = $req->middleName;
            $vcard->lastName = trim($req->lastName);
            $vcard->jobTitle = $req->jobTitle;
            $vcard->companyName = $req->companyName;
            $vcard->industry = $req->industry;
            $vcard->subIndustry = $req->subIndustry;
            $vcard->emailid1 = $req->emailid1;
            $vcard->emailid2 = $req->emailid2;
            if($vcard->mobileNo1 !== $req->cc1.$req->mobileNo1)
            {
                $vcard->status = '0';
            }
            $vcard->mobileNo1 = $req->cc1.$req->mobileNo1;
            $vcard->countryCode1 = $req->cc1;
            $vcard->mobileNo2 = $req->cc2.$req->mobileNo2;
            $vcard->countryCode2 = $req->cc2;
            $vcard->addressLine1 = $req->addressLine1;
            $vcard->addressLine2 = $req->addressLine2;
            $vcard->country = $country;
            $vcard->state = $state;
            $vcard->city = $city;
            $vcard->zipcode = $req->zipcode;
            $vcard->linkdnURL = $req->linkdnURL;
            $vcard->twitterURL = $req->twitterURL;
            $vcard->instaURL = $req->instaURL;
            $vcard->facebookURL = $req->facebookURL;
            $vcard->indiamartLink = $req->youtubeChannel;
            $vcard->indiamartLink = $req->indiamartLink;
            $vcard->bniURL = $req->bniURL;
            $vcard->tradeIndia = $req->tradeIndia;
            $vcard->bniPublicProfile = $req->bniPublicProfile;
            $vcard->website1 = $req->website1;
            
            $vcard->save();


            return redirect('/vcards');
        } catch (\Throwable $th) {
            //throw $th;
            Log::error('Error in save vcard: '.$th->getMessage());
        }
    }

    private function generateRandomCode($length = 8) {
        // Define the allowed characters: uppercase letters A-Z and numbers 1-9 (exclude 0 for clarity)
        $characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ123456789';
        
        // Shuffle the string or select random characters
        $randomCode = '';
        
        for ($i = 0; $i < $length; $i++) {
            // Select a random character from the allowed set
            $randomCode .= $characters[random_int(0, strlen($characters) - 1)];
        }
        
        return $randomCode;
    }

    public function smvcContactCard()
    {
        $data = [
            "name" => [
                "formatted_name" => ".Save My V Card",
                "first_name" => ".Save My V Card",
                "prefix" => ""
            ],
            "phones" => [
                [
                    "phone" => "+919007900976",
                    "wa_id" => "919007900976",
                    "type" => "WORK"
                ],
                [
                    "phone" => "+919999389493",
                    "wa_id" => "919999389493",
                    "type" => "HOME"
                ]
            ],
            "emails" => [
                [
                    "email" => "info@savemyvcard.com",
                    "type" => "WORK"
                ]
            ],
            "urls" => [
                [
                    "url" => "https://www.savemyvcard.com",
                    "type" => "WORK"
                ]
            ]
        ];
    
        return $data;
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

    public function getLeads(Request $request)
    {
        try {
            $token = $request->token;
            $newToken = $this->rot13($token);
            $tokenArr = explode('#$',$newToken);
            $userid = $tokenArr[0];
            $password = $tokenArr[1];

            $user = User::select('mobileno')->where('id','=',$userid)->where('password','=',$password)->first();
            
            if(is_null($user))
            {
                return response()->json(['status' => 'failed', 'message' => 'Invalid token']);
            }

            $vcardList = DB::table('lead_notification as ln')
                    ->join('users as u', 'ln.user_id', '=', 'u.id')
                    ->join('vcard as vc', 'vc.id', '=', 'ln.vcard_id')
                    ->join('dataSent as ds', 'ln.message_id', '=', 'ds.messageId')
                    ->select(
                        'vc.firstName as cardFirstName',
                        'vc.mobileNo1 as cardMobileNumber',
                        'ln.downloader_name',
                        'ln.downloader_no',
                        'ln.message_received',
                        DB::raw('UNIX_TIMESTAMP(ln.created_at) as timestamp')  // Convert created_at to timestamp
                    )
                    ->where('u.id', $userid)
                    ->get();
            return response()->json(['status' => 'success' , 'data' => $vcardList]);
        } catch (\Throwable $th) {
            Log::error("There is some issue with the getAuth".$th->getMessage());
        }
    }

    public function changePassword(Request $request)
    {
        $useremail = $request->userid;
        $oldpassword = $request->oldPassword;
        $newPassword = $request->newPassword;
        $confirmNewPassword = $request->confirmNewPassword;
        if($newPassword !== $confirmNewPassword)
        {
            return redirect()->back()->withErrors(['error' => "New Password and Confirm New Password doesn't match"])->withInput();
        }
        $user = User::select('id','password')->where('email','=', $useremail)->first();
        if($user->password !== md5($oldpassword))
        {
            return redirect()->back()->withErrors(['error' => "The current password is not correct. Please try again."])->withInput();
        }
        User::where('id','=',$user->id)->update(['password' => md5($newPassword)]);
        return redirect()->back()->withErrors(['message' => "Password Changed Successfully"])->withInput();
    }

    public function processCard($cardcode)
    {
        $vcard = Vcard::where('uniqueCode','=', $cardcode)->orWhere('premiumCode','=',$cardcode)->first();
        if(is_null($vcard))
        {
            if(substr($cardcode,-4) == 'SMMS')
            {
                $vcard = Vcard::where('mobileStickerCode','=',$cardcode)->first();
            }
            else if(substr($cardcode,-4) == 'SMTC')
            {
                $vcard = Vcard::where('tentCardCode','=',$cardcode)->first();
            }
            else
            {
                Log::info("Card code not found:".$cardcode);
                echo "<h2>Card code not found:".$cardcode."</h2><br/><h5>Click on the button below to send the request to activate the card.</h5>";
                $message ="ActivateCard Code:".$cardcode."";
                echo "<a href='https://wa.me/919007900976?text=".urlencode($message)."'><button>Activate Card</button></a>";
                return;
            }

            if(is_null($vcard))
            {
                Log::info("Card code not found:".$cardcode);
                echo "<h2>Card code not found:".$cardcode."</h2><br/><h5>Click on the button below to send the request to activate the card.</h5>";
                $message ="ActivateCard Code:".$cardcode."";
                echo "<a href='https://wa.me/919007900976?text=".urlencode($message)."'><button>Activate Card</button></a>";
                return;
            }
        }
        $connectTypeAndMessage = UserVcard::select('connectType','firstMessage')->where('vcardid','=',$vcard->id)->first();
        if($connectTypeAndMessage->connectType == 1)
        {
            $message = $connectTypeAndMessage->firstMessage;
            if(is_null($message))
            {
                $message = "Please share contact card of $vcard->salutation_text $vcard->firstName $vcard->middleName $vcard->lastName $cardcode";
            }
            else{
                $message = $message." CardCode: ".$cardcode;
            }
            $url = "https://wa.me/919007900976?text=".urlencode($message);
            return redirect()->away($url);
        }
        else if($connectTypeAndMessage->connectType == 2)
        {
            $message = $connectTypeAndMessage->firstMessage;
            if(is_null($message))
            {
                $message = "Hi $vcard->salutation_text $vcard->firstName $vcard->middleName $vcard->lastName. Got your number via Savemyvcard.com";
            }
            $url = "https://wa.me/$vcard->mobileNo1?text=".urlencode($message);
            return redirect()->away($url);
        }
        else if($connectTypeAndMessage->connectType == 3)
        {
            return redirect()->route('vcfcard',['cardcode' => $cardcode]);
        }
    }

    public function updateVCardInteractionType(Request $request)
    {
        // Define the base validation rules
        $rules = [
            'interactionType' => 'required|in:1,2,3', // Ensure interactionType is valid
        ];
        
        $request->merge([
            'firstMessage' => trim($request->firstMessage),
        ]);
        // Apply conditional validation for 'firstMessage'
        $validator = Validator::make($request->all(), $rules);

        $validator->sometimes('firstMessage', 'required|max:250|string', function ($input) {
            // Apply the rule if interactionType is 1 or 2
            return in_array($input->interactionType, [1, 2]);
        });

        // Handle validation errors
        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Proceed with updating the vCard details
        $vcard = UserVcard::where('vcardid','=',$request->vcardid)->update([
            'connectType' => $request->interactionType,
            'firstMessage' => $request->firstMessage
        ]);

        return redirect()->back()->with('success', 'VCard Interaction Type Updated Successfully!');
    }

    public function processBusinessCard()
    {
        // $openAIService = new OpenAIController();

        // $unprocessedCard = CardExtractionDetails::select('id','media_id')->where('isProcessedByAi','=','2')->limit(2)->get();

        // foreach($unprocessedCard as $card)
        // {
        //     $media_url = "businesscards/{$card->media_id}.jpeg";
        //     $content = Storage::disk('s3')->get($media_url);
        //     $imageData = base64_encode($content);

        //     // Call OpenAI API to structure the data
        //     $structuredData = $openAIService->processBusinessCard($imageData, 'base64');
        //     CardExtractionDetails::where('id', $card->id)->update([
        //         'aiResponseMini' => json_encode($structuredData)
        //     ]);
        // }
        
    }

    public function processBusinessCardUsingAI()
    {
        $openAIService = new OpenAIController();
        $wa = new WhatsAppController();
        $unprocessedCard = CardExtractionDetails::select('id','media_id')->where('isProcessedByAi','=','1')->where('created_at','>',Carbon::now()->subDay())->limit(2)->get();

        foreach($unprocessedCard as $card)
        {
            $media_url = "businesscards/{$card->media_id}.jpeg";
            //$content = Storage::disk('s3')->get($media_url);
            //$imageData = base64_encode($content);
            // Call OpenAI API to structure the data
            $imageUrl = Storage::disk('s3')->url($media_url);
            $structuredData = $openAIService->extractBusinessCardDetails($imageUrl, 'url');
            
            if ($structuredData) {
                $received = DataReceived::select('senderPhoneNo','senderProfileName')
                    ->where('messagetype','=','image')
                    ->where('mediaURL','=',$card->media_id)
                    ->first();
                
                // Check if sender's number matches any contact in the card
                $senderNumber = preg_replace('/\D/', '', $received->senderPhoneNo);
                $isOwner = false;
                
                if (isset($structuredData['Contact']) && is_array($structuredData['Contact'])) {
                    foreach ($structuredData['Contact'] as $contact) {
                        $cardNumber = preg_replace('/\D/', '', $contact);
                        if ($cardNumber && $senderNumber && strpos($senderNumber, $cardNumber) !== false) {
                            $isOwner = true;
                            break;
                        }
                    }
                }

                if ($isOwner) {
                    // Update the card details since sender is the owner
                    CardExtractionDetails::where('id', $card->id)->update([
                        'isProcessedByAi' => 2,
                        'aiResponse' => json_encode($structuredData),
                        'isEditedByUser' => 0  // This will allow updateScannedCard to process it
                    ]);
                    
                    $wa->sendTextMessage($received->senderPhoneNo, 'Thank you for sharing your business card. We will create your account and share the credentials soon.');

                    $this->createUserAccount($structuredData, $received->senderPhoneNo, $received->senderProfileName);
                } else {
                    // Continue with existing logic for non-owner cards
                    $vcard = $wa->createVCardFromData($structuredData);
                    if (!empty($vcard)) {  
                        $wa->sendTextMessage($received->senderPhoneNo, 'This image contains a business card. It will be processed and a contact card will be sent shortly.');
                        $wa->sendContactCardFromCard($received->senderPhoneNo, $vcard);
                        CardExtractionDetails::where('id', $card->id)->update([
                            'isProcessedByAi' => 2,
                            'aiResponse' => json_encode($structuredData),
                        ]);
                    }
                }
            } else {
                Log::error("Failed to process AI response: " . json_encode($structuredData));
            }
        }
    }

    public function processBusinessCardUsingForManzar($media_id)
    {
        $openAIService = new OpenAIController();
        $wa = new WhatsAppController();
        $unprocessedCard = CardExtractionDetails::select('id','media_id','aiResponse')->where('media_id','=',$media_id)->get();

        foreach($unprocessedCard as $card)
        {
            $structuredData = json_decode($card->aiResponse, true);
            
            if ($structuredData) {
                $received = DataReceived::select('senderPhoneNo','senderProfileName')
                    ->where('messagetype','=','image')
                    ->where('mediaURL','=',$card->media_id)
                    ->first();
                
                // Check if sender's number matches any contact in the card
                $senderNumber = preg_replace('/\D/', '', $received->senderPhoneNo);
                $isOwner = false;
                
                if (isset($structuredData['Contact']) && is_array($structuredData['Contact'])) {
                    foreach ($structuredData['Contact'] as $contact) {
                        $cardNumber = preg_replace('/\D/', '', $contact);
                        if ($cardNumber && $senderNumber && strpos($senderNumber, $cardNumber) !== false) {
                            $isOwner = true;
                            break;
                        }
                    }
                }

                if ($isOwner) {
                    // Update the card details since sender is the owner
                    CardExtractionDetails::where('id', $card->id)->update([
                        'isProcessedByAi' => 2,
                        'aiResponse' => json_encode($structuredData),
                        'isEditedByUser' => 0  // This will allow updateScannedCard to process it
                    ]);
                    
                    $wa->sendTextMessage($received->senderPhoneNo, 'Thank you for sharing your business card. We will create your account and share the credentials soon.');

                    $this->createUserAccount($structuredData, $received->senderPhoneNo, $received->senderProfileName);
                } 
            } else {
                Log::error("Failed to process AI response: " . json_encode($structuredData));
            }
        }
    }

    public function createUserAccount($structuredData, $senderNumber, $senderName)
    {
        $data = $structuredData;
        $email = is_array($data['Email_ID']) ? ($data['Email_ID'][0] ?? null ) : $data['Email_ID'];
        $ccode = '91';
        if(strlen($senderNumber) == 12){
            $ccode = substr($senderNumber, 0, 2);
        }
        $lc = new LoginController();
        $response = $lc->autoRegisterMember($email, $senderNumber, $ccode);

        $wa = new WhatsAppController();
        if($response[0] == 99){
            $wa->sendTextMessage($senderNumber, "Hi $senderName, An account already exists with this email or mobile number. Please use a different email or mobile number to create your account.");
            return;
        }

        $wa->sendTextMessage($senderNumber, "Hi $senderName, Your account has been created successfully. Your username is $email and password is $response[0].\n\nLogin: https://savemyvcard.com/signin");
        $jsonData = json_encode($structuredData);
        $aiController = new OpenAIController();
        $jsonData = $aiController->extractCardCreationData($jsonData);
        if(!is_null($jsonData)){
            $vcardid = $this->createAutoVCard($jsonData, $response[1], $senderNumber);
            if($vcardid > 0)
            {
                $cardUrl = "https://savemyvcard.com/getDigitalCard/".$vcardid;
                $wa->sendTextMessage($senderNumber, "Hi $senderName, Your digital business card has been created successfully. Please click on the link to download your card.\n\n$cardUrl");
            }
        }
        //$this->createAutoVCard($jsonData, $response[1]);
    }

    public function createAutoVCard($structuredData, $userid, $senderNumber)
    {
        try {

            $randomCode = $this->generateRandomCode();

            $structuredData = json_decode($structuredData, true);

            $vcard = new Vcard();
            $vcard->firstName = $structuredData['firstname'] ?? null;
            $vcard->lastName = $structuredData['lastname'] ?? null;
            $vcard->jobTitle = $structuredData['jobtitle']?? null;
            $vcard->companyName = $structuredData['companyname']?? null;
            $vcard->emailid1 = $structuredData['emailid']?? null;
            $vcard->mobileNo1 = $senderNumber;

            $vcard->countryCode1 = substr($senderNumber, 0, 2);
            $vcard->addressLine1 = $structuredData['address']?? null;
            $vcard->website1 = $structuredData['website']?? null;
            $vcard->uniqueCode = $randomCode.'SMVC';
            $vcard->save();

            $userVcard = new UserVcard();
            $userVcard->userid = $userid;
            $userVcard->vcardid = $vcard->id;
            $userVcard->save();

            return $vcard->id;
        } catch (\Throwable $th) {
            //throw $th;
            Log::error('Error in create auto vcard: '.$th->getMessage());
            return 0;
        }
    }

    public function editScannedBusinessCards(Request $request)
    {
        $mediaCode = $request->media_id;
        try {
            $cardDetails = CardExtractionDetails::where('media_id','=',$mediaCode)->first();
            return redirect()->back();
            //return view('member.editScannedBusinessCards', ['carddetails' => $cardDetails]);
        } catch (\Throwable $th) {
            Log::error("Error in scanned business cards:".$th->getMessage());
        }
    }

    public function updateScannedCard()
    {
            $cards = CardExtractionDetails::where('isProcessedByAi','=','2')->where('isEditedByUser','=','0')->limit(200)->get();

            foreach($cards as $card)
            {
                try
                {
                    $data = json_decode($card->aiResponse, true); // Decode JSON into array
                    $cardDetails = CardExtractionDetails::find($card->id);
                    
                    $extractNumbers = function ($value) {
                        return isset($value) ? preg_replace('/\D/', '', $value) : null;
                    };

                    $cardDetails->update([
                        'isEditedByUser' => 1,
                        'cardName' => is_array($data['Name'] ?? '') ? implode(', ', $data['Name']) : ($data['Name'] ?? ''),
                        'jobTitle' => is_array($data['Job_Title'] ?? '') ? implode(', ', $data['Job_Title']) : ($data['Job_Title'] ?? ''),
                        'companyName' => is_array($data['Company_Name'] ?? '') ? implode(', ', $data['Company_Name']) : ($data['Company_Name'] ?? ''),
                        'contactNo1' => $extractNumbers($data['Contact'][0] ?? null),
                        'contactNo2' => $extractNumbers($data['Contact'][1] ?? null),
                        'contactNo3' => $extractNumbers($data['Contact'][2] ?? null),
                        'contactNo4' => $extractNumbers($data['Contact'][3] ?? null),
                        'contactNo5' => $extractNumbers($data['Contact'][4] ?? null),
                        'email1' => is_array($data['Email_ID']) ? ($data['Email_ID'][0] ?? null ) : $data['Email_ID'],
                        'email2' => is_array($data['Email_ID']) ? ($data['Email_ID'][1] ?? null ) : null,
                        'website1' => is_array($data['Website']) ? ($data['Website'][0] ?? null ) : $data['Website'],
                        'website2' => is_array($data['Website']) ? ($data['Website'][1] ?? null ) : null,
                        'address' => is_array($data['Address'] ?? '') ? implode(', ', $data['Address']) : ($data['Address'] ?? '')
                    ]);
                }
                catch (\Throwable $th) {
                    Log::error("There was some error in scannedcardentries::".$th->getMessage());
                    echo "<br>Error::".$th->getMessage();
                }
            }
    }

}
