<?php

namespace App\Http\Controllers;
use App\Http\Controllers\BusinessLogicController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;
use App\Models\WebhookData;
use App\Models\DataReceived;
use App\Models\DataSent;
use App\Models\User;
use App\Models\Subscription;
use App\Models\User_Subscription;
use App\Models\Members;
use App\Models\Vcard;
use App\Models\UserVcard;
use App\Models\Countries;
use App\Models\LeadNotification;
use App\Models\CardExtractionDetails;
use Illuminate\Support\Facades\Storage;

class WhatsAppController extends Controller
{
    protected $version;
    protected $meta_phone_id;
    protected $waba_id;
    protected $system_user_token;

    public function __construct()
    {
        $this->version = env('WHATSAPP_API_VERSION', 'v20.0');
        $this->meta_phone_id = env('WHATSAPP_META_PHONE_ID');
        $this->waba_id = env('WHATSAPP_WABA_ID');
        $this->system_user_token = env('WHATSAPP_SYSTEM_USER_TOKEN');
    }
    
    public function getWebhook(Request $request)
    {
        $my_token = env('WHATSAPP_WEBHOOK_VERIFY_TOKEN');

        $mode = $request['hub_mode'];
        $challenge = $request['hub_challenge'];
        $verify_token =$request['hub_verify_token'];

        if($mode && $verify_token)
        {
            if($mode ==='subscribe' && $verify_token === $my_token)
            {
                http_response_code(200);
                echo $challenge;
            }
            else{
                echo $my_token;
                http_response_code(200);
            }
        }
    }

    public function postWebhook(Request $request)
    {
        try{
            $input = $request->getContent();
            $this->handleResponse($input);
            http_response_code(200);
        }
        catch(\Throwable $e)
        {
            Log::error($e->getMessage());
        }
    }

    public function handleResponse($jsonString)
    {
        try {
            $jsonData = json_decode($jsonString, true);
            $changes = $jsonData['entry'][0]['changes'];
            foreach($changes as $change)
            {
                $field = $change['field'];
                $value = $change['value'];
                $metadata = $value['metadata'];  // Associative array
                // Accessing elements inside the 'metadata' array
                $received = new WebhookData();
                $received->webhook_data = $jsonString;
                $received->save();

                $displayPhoneNumber = $metadata['display_phone_number'];  // String

                if($displayPhoneNumber == '919654876000')
                {
                    $this->sendDataToAutoPilot($jsonString);
                }
                else if($displayPhoneNumber == '919007900976' && $field === 'messages' )
                {
                    //saving webhook data for later analysis
                    
                    //Data Received
                    if(isset($value['messages']))
                    {
                        $this->handleDataReceived($value);
                    }
                    //Data Sent
                    if(isset($value['statuses']))
                    {
                        $this->handleStatusUpdate($value);
                    }
                    $this->sendDataToAutoPilot($jsonString);
                }
                else{
                    //Log::info("Need to look into this::".$jsonString);
                }

                /**
                 * 
                 * if($displayPhoneNumber == '919007900976' && $field === 'message_template_status_update')
                 * {
                  *  //$this->updateMessageTemplateStatus($value);
                 *} 
                */
            }
        } catch (\Throwable $th) {
            //throw $th;
            Log::error($th->getMessage());
        }
    }

    private function sendDataToAutoPilot($data)
    {
        try
        {
            $curl = curl_init();
            curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://api.wautopilot.com/webhooks/meta/whatsapp',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 1,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => $data,
            CURLOPT_TIMEOUT => 5,	
            CURLOPT_CONNECTTIMEOUT => 5, 
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json'
                ),
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_SSL_VERIFYHOST => false
            ));

            $response = curl_exec($curl);
            curl_close($curl);
        }
        catch (\Exception $e)
        {
            Log::error("API Exception : " . $e->getMessage());
        }
    }

    private function handleStatusUpdate($values)
    {
        $statuses = $values['statuses'];
        foreach ($statuses as $status) {
            $id = $status['id'];
            $stat = $status['status'];
            $timestamp = $status['timestamp'];
            $sentTo = $status['recipient_id'];

            if(strcmp($stat,"sent") == 0)
            {
                $result = DataSent::where('messageId','=',$id)->first();
                if(is_null($result))
                {
                    $message_sent = new DataSent();
                    $message_sent->messageId = $id;
                    $message_sent->sentTo = $sentTo;
                    $message_sent->messageType = 'NA';
                    $message_sent->contents = 'NA';
                    $message_sent->sentAt = $timestamp;
                    $message_sent->save();
                }
                else{
                    DataSent::where('messageId',"=",$id)->update(['sentAt' => $timestamp]);
                }
            }
            else if(strcmp($stat, "delivered") == 0)
            {
                DataSent::where('messageId',"=",$id)->update(['deliveredAt' => $timestamp]);
            }
            else if(strcmp($stat, "read") == 0)
            {
                DataSent::where('messageId',"=",$id)->update(['readAt' => $timestamp]);
            }
            else if(strcmp($stat, "failed") == 0)
            {
                try{
                    $errorMessage = $status['errors'][0]['message'];
                    DataSent::where('messageId',"=",$id)->update(['isError' => '1', 'errorMessage' =>$errorMessage]);
                }
                catch(\Exception $e)
                {
                    DataSent::where('messageId',"=",$id)->update(['error' => '1']);
                    Log::error("Error in handle data sent::".$e->getMessage());
                }
            }

        }
    }

    private function handleDataReceived($values)
    {
        $messages = $values['messages'];
        $profileName = $values['contacts'][0]['profile']['name'];  // String
        $profileName = trim(preg_replace("/[^a-zA-Z ]/", "", $profileName)) ?: "--";
        foreach($messages as $message)
        {
            $from = $message['from'];
            $id = $message['id'];
            $timestamp = $message['timestamp'];
            $type = $message['type'];
            $body = "--";
            $add = '';
            $add_name = '';
            $lat = '';
            $lon = '';
            $add_url = '';
            $contextId = '';
            
            if(strcmp($type,"text") == 0)
            {
                $body = $message['text']['body'];
            }
            else if(strcmp($type,'button') == 0)
            {
                $body = $messages[0]['button']['text'];  // String
                $contextId = $messages[0]['context']['id'];	
            }
            else if(strcmp($type,'location') == 0)
            {
                $lat = $messages[0]['location']['latitude'];  // String
                $lon = $messages[0]['location']['longitude'];  // String
                if(isset($messages[0]['location']['address'])){
                    $add = $messages[0]['location']['address'];  // String
                }
                if(isset($messages[0]['location']['name'])){
                    $add_name = $messages[0]['location']['name'];  // String
                }
                if(isset($messages[0]['location']['url'])){
                    $add_url = $messages[0]['location']['url'];  // String
                }
            }
            else if(strcmp($type, 'image') == 0)
            {
                $add_name = 'notprocessed'; //flag to denote that this row will be processed in the next function
                $body = isset($messages[0]['image']['caption']) ? $messages[0]['image']['caption'] : null;
                $contextId = $messages[0]['image']['id'];
                $add_url = $this->getMediaUrl($contextId, $body);
            }
            $newData = new DataReceived();
            $newData->senderPhoneNo = $from;
            $newData->senderProfileName = $profileName;
            $newData->messageId = $id;
            $newData->messagetype = $type;
            $newData->messageText = $body;
            $newData->latitude = $lat;
            $newData->longitude = $lon;
            $newData->addressName = $add_name; //mime_type in case of image
            $newData->URL = $add_url; //media id in case of image
            $newData->mediaURL = !empty($contextId) ? $contextId : null; // Set NULL if empty
            $newData->save();

            $this->processReceivedMessage($from, $type, $body, $profileName, $contextId);
        }
    }

    public function getMediaUrl($mediaid, $caption)
    {
        $url = 'https://graph.facebook.com/'.($this->version).'/'.($mediaid).'/?phone_number='.($this->waba_id);
        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->system_user_token,
                'Content-Type' => 'application/json',
            ])->get($url);
            $jsonData = json_decode($response, true);
            if ($jsonData) {
                // Accessing the URL and ID from the decoded JSON data
                $url = $jsonData['url'];
                $type = $jsonData['mime_type'];
                $id = $jsonData['id'];
                $response2 = $this->saveInS3($url,$type,$id);
                
                return $response2;
            } 
            else 
            {
                Log::error('Failed to decode JSON');
                return 'error';
            }
        } catch (\Throwable $th) {
            //throw $th;
            Log::error('Error in get Media::'.$th->getMessage());
            return 'exception';
        }
    }

    public function saveMediaInFolder($url, $type, $id)
    {
        // Fetch the image from the given URL
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->system_user_token
        ])->get($url);

        // Check if the response is successful
        if ($response->successful()) {
            // Get the image content
            $imageContent = $response->body();
            
            $filename = "{$id}.jpeg"; 
            $filePath = "downloads/{$filename}";

            Storage::disk('public')->put($filePath, $imageContent);
            
            return $filename;
        }

        return response()->json(['message' => 'Failed to download image'], 400);
    }

    public function saveInS3($url, $type, $id)
    {
         // Fetch the image from the given URL
         $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->system_user_token
        ])->get($url);

        // Check if the response is successful
        if ($response->successful()) {
            // Get the image content
            $imageContent = $response->body();
            
            $filename = "{$id}.jpeg"; 
            $filePath = "businesscards/{$filename}";

            $path = Storage::disk('s3')->put($filePath, $imageContent,'public');
            Log::info("S3 File Path {$path}");
            return $filename;
        }

        return response()->json(['message' => 'Failed to download image'], 400);
    }

    public function viewTemplates($url = null)
    {
        if($url == null)
        {
            $url = 'https://graph.facebook.com/'.($this->version).'/'.($this->waba_id).'/message_templates';
        }
        $response = $this->getTemplate($url);
        $jsonData = json_decode($response, true);
        
        foreach($jsonData['data'] as $row)
        {
            echo "<br/>".$row['name'];
            if($row['name'] !== 'hello_world'){
                $this->deleteTemplate($url,$row['name']);
            }
            sleep(1);
        }

        if(isset($jsonData['paging']['next']))
        {
            $this->viewTemplates($jsonData['paging']['next']);
        }
    }

    public function getTemplate($url) : string
    {
        $curl = curl_init();
		curl_setopt_array($curl, array(
		  CURLOPT_URL => $url,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 1,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'GET',
		  CURLOPT_SSL_VERIFYPEER => false,
		  CURLOPT_SSL_VERIFYHOST => false,
		  CURLOPT_HTTPHEADER => array(
		    'Content-Type: application/json',
		    'Authorization: Bearer '.$this->system_user_token
		  ),
		));
		$response = curl_exec($curl);
		curl_close($curl);
        return $response;
    }

    public function deleteTemplate($url,$name)
    {
        $curl = curl_init();
		curl_setopt_array($curl, array(
		  CURLOPT_URL => $url.'?name='.$name,
		  CURLOPT_RETURNTRANSFER => true,
		  CURLOPT_ENCODING => '',
		  CURLOPT_MAXREDIRS => 1,
		  CURLOPT_TIMEOUT => 0,
		  CURLOPT_FOLLOWLOCATION => true,
		  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
		  CURLOPT_CUSTOMREQUEST => 'DELETE',
		  CURLOPT_SSL_VERIFYPEER => false,
		  CURLOPT_SSL_VERIFYHOST => false,
		  CURLOPT_HTTPHEADER => array(
		    'Content-Type: application/json',
		    'Authorization: Bearer '.$this->system_user_token
		  ),
		));
		$response = curl_exec($curl);

        if (curl_errno($curl)) {
            echo 'Error:' . curl_error($curl);
        } else {
            // Output the response
            echo 'Response:' . $response;
        }
		curl_close($curl);
    }

    public function createTemplate(Request $request)
    {
        $r = $request->input('message');
        $template_name = $r['template_name'];
        $body_text = $r['template_text'];
        $url = 'https://graph.facebook.com/'.($this->version).'/'.($this->meta_phone_id).'/message_templates';

        $header = [
            "type" => "HEADER",
            "format" => "TEXT",
            "text" => "SAVE MY V CARD"
        ];

        $body = [
            "type" => "BODY",
            "text" => $body_text
        ];

        $footer = [
            "type" => "FOOTER",
            "text" => "www.savemyvcard.com"
        ];

        $data = [
            "name" => $template_name,
            "category" => "MARKETING",
            "allow_category_change" => true,
            "language" => "en_US",
            "components" => [$header, $body, $footer]
        ];

        // Make HTTP request using Laravel's HTTP client
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->system_user_token,
            'Content-Type' => 'application/json',
        ])->post($url, $data);

        // Log the response
        Log::info("Response from create Template: " . $response->body());

        // Return the response for further processing or display
        return $response->body();
    }

    public function sendTemplateMessages($mobile, $template_name, $variables = [])
    {
        try 
        {
            $url = 'https://graph.facebook.com/'.($this->version).'/'.($this->meta_phone_id).'/messages';
            $data = array(
                    "messaging_product" => "whatsapp",
                    "to" => $mobile,
                    "type" => "template",
                    "template" => array(
                        "name" => $template_name,
                        "language" => array(
                            "code" => "en_US"
                        )
                    )
                );

            // Check if variables are provided and construct "components"
            if (!empty($variables)) {
                $parameters = [];

                foreach ($variables as $variable) {
                    $parameters[] = [
                        "type" => "text",
                        "text" => $variable
                    ];
                }

                $data['template']['components'] = [
                    [
                        "type" => "body",
                        "parameters" => $parameters
                    ]
                ];
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->system_user_token,
                'Content-Type' => 'application/json',
            ])->post($url, $data);

            $response_data = json_decode($response, true);
            $message_id = $response_data['messages'][0]['id'];
            
            if(strlen($message_id) > 0)
            {
                $sent = new DataSent();
                $sent->messageId = $message_id;
                $sent->sentTo = $mobile;
                $sent->messageType = 'Template';
                $sent->contents = $template_name;
                $sent->save();
                return $message_id;
            }
            else{
                Log::error("Issue in sending Template Message. mobile:".$mobile." Template:".$template_name."  Response:".$response);
            }
        }
        catch(\Exception $e)
        {
            Log::error("sendTemplateMessage Error: ".$e->getMessage());
            return "error";
        }
    }

    public function sendTemplateMessagesWithImage($mobile, $template_name, $imageUrl, $variables = [])
    {
        try 
        {
            $url = 'https://graph.facebook.com/'.($this->version).'/'.($this->meta_phone_id).'/messages';
            $data = array(
                    "messaging_product" => "whatsapp",
                    "to" => $mobile,
                    "type" => "template",
                    "template" => array(
                        "name" => $template_name,
                        "language" => array(
                            "code" => "en_US"
                        ),
                        "components" => [
                            [
                                "type" => "header",
                                "parameters" => [
                                    [
                                        "type" => "image",
                                        "image" => [
                                            "link" => $imageUrl
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    )
                );

            // Check if variables are provided and construct "components"
            if (!empty($variables)) {
                $parameters = [];

                foreach ($variables as $variable) {
                    $parameters[] = [
                        "type" => "text",
                        "text" => $variable
                    ];
                }
                array_push($data['template']['components'] , [
                    "type" => "body",
                    "parameters" => $parameters
                ]);
            }

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->system_user_token,
                'Content-Type' => 'application/json',
            ])->post($url, $data);

            $response_data = json_decode($response, true);
            $message_id = $response_data['messages'][0]['id'];
            
            if(strlen($message_id) > 0)
            {
                $sent = new DataSent();
                $sent->messageId = $message_id;
                $sent->sentTo = $mobile;
                $sent->messageType = 'Template';
                $sent->contents = $template_name;
                $sent->save();
                return $message_id;
            }
            else{
                Log::error("Issue in sending Template Message. mobile:".$mobile." Template:".$template_name."  Response:".$response);
            }
        }
        catch(\Exception $e)
        {
            Log::error("sendTemplateMessage Error: ".$e->getMessage());
            return "error";
        }
    }

    public function sendTextMessage($mobile, $message)
    {
        try{
            $data = array(
                    "messaging_product" => "whatsapp",
                    "recipient_type" => "individual",
                    "to" => $mobile,
                    "type" => "text",
                    "text" => array(
                        "preview_url" => true,
                        "body" => $message
                    )
                );
    
            $url = 'https://graph.facebook.com/'.($this->version).'/'.($this->meta_phone_id).'/messages';
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->system_user_token,
                'Content-Type' => 'application/json',
            ])->post($url, $data);
 
            $response_data = json_decode($response, true);
            $message_id = $response_data['messages'][0]['id'];
            
            if(strlen($message_id) > 0)
            {
                $sent = new DataSent();
                $sent->messageId = $message_id;
                $sent->sentTo = $mobile;
                $sent->messageType = 'text';
                $sent->contents = $message;
                $sent->save();
            }
            else{
                Log::error("Issue in sending Text Message. mobile:".$mobile." Message:".$message."  Response:".$response);
            }
        }
        catch(\Exception $e)
        {
            Log::error("Issue in sending Text Message. mobile:".$mobile." Message:".$message);
        }
    }

    public function createVCard($vcardid)
    {
        $vcard = Vcard::find($vcardid);

        $nameArr = array(
			"formatted_name" => $vcard->firstName,
			"first_name" => $vcard->firstName
		);

		if(strlen($vcard->middleName)>0)
		{
			$nameArr["middle_name"] = $vcard->middleName;
			$nameArr['formatted_name'] = $vcard->firstName." ".$vcard->middleName;
		}

        if(strlen($vcard->lastName)>0)
		{
			$nameArr["last_name"] = $vcard->lastName;
			$nameArr['formatted_name'] = $vcard->firstName." ".$vcard->middleName." ".$vcard->lastName;
		}

		if(strlen($vcard->salutation_text)>0)
		{
			$nameArr["prefix"] = $vcard->salutation_text;
			$nameArr['formatted_name'] = $vcard->salutation_text." ".$vcard->firstName." ".$vcard->middleName." ".$vcard->lastName;
		}

		$orgArray = [];

        if(strlen($vcard->companyName) > 0)
		{
			$orgArray['company'] = $vcard->companyName;
		}
		if(strlen($vcard->jobTitle) > 0)
		{
			$orgArray['title'] = $vcard->jobTitle;
		}
		

		$phoneArr = [];

		if(strlen($vcard->mobileNo1) > 5)
		{
			$phone1 = array(
		   			"phone" => '+'.$vcard->mobileNo1,
	                "wa_id" =>  $vcard->mobileNo1,
	                "type" => "WORK"
		   		);
			array_push($phoneArr, $phone1);
		}
		if(strlen($vcard->mobileNo2) > 5)
		{
			$phone2 = array(
		   			"phone" => '+'.$vcard->mobileNo2,
	                "wa_id" =>  $vcard->mobileNo2,
	                "type" => "WORK"
		   		);
			array_push($phoneArr, $phone2);
		}

		$emailArr = [];

		if(strlen($vcard->emailid1) > 5)
		{
			$email1 = array(
                "email" => $vcard->emailid1,
                "type" => "WORK"
            );
			array_push($emailArr, $email1);
		}
		if(strlen($vcard->emailid2) > 5)
		{
			$email2 = array(
                "email" => $vcard->emailid2,
                "type" => "WORK"
            );
			array_push($emailArr, $email2);
		}

		$addressArr = [];
		$address = [];
        $address["type"] = 'Work';
		if(strlen($vcard->addressLine1) > 0)
		{
			$address['street'] = $vcard->addressLine1;
		}
        if(strlen($vcard->addressLine1) > 0 && strlen($vcard->addressLine2) > 0)
		{
			$address['street'] = $vcard->addressLine1.", ".$vcard->addressLine2;
		}
		if(strlen($vcard->city) > 0)
		{
			$address['city'] = $vcard->city;
		}
		if(strlen($vcard->state) > 0)
		{
			$address['state'] = $vcard->state;
		}
		if(strlen($vcard->zipcode) > 0)
		{
			$address['zip'] = $vcard->zipcode;
		}
		if(strlen($vcard->country) > 0)
		{
			$address['country'] = $vcard->country;
            $country_code = Countries::select('iso2')->where('name','=',$vcard->country)->first();
            $address["country_code"] = $country_code->iso2;
		}

		array_push($addressArr, $address);

		$urlArr = [];

		if(strlen($vcard->facebookURL) > 5)
		{
            $url1 = array(
                "url" => $vcard->facebookURL,
                "type" => "Facebook Page"
            );
            array_push($urlArr, $url1);
        }

        if(strlen($vcard->instaURL) > 5)
		{
            $url2 = array(
                "url" => $vcard->instaURL,
                "type" => "Instagram"
            );
            array_push($urlArr, $url2);
        }

        if(strlen($vcard->twitterURL) > 5)
		{
            $url3 = array(
                "url" => $vcard->twitterURL,
                "type" => "Twitter"
            );
            array_push($urlArr, $url3);
        }

        if(strlen($vcard->linkdnURL) > 5)
		{
            $url4 = array(
                "url" => $vcard->linkdnURL,
                "type" => "LinkedIn"
            );
            array_push($urlArr, $url4);
        }

        if(strlen($vcard->bniURL) > 5)
		{
            $url5 = array(
                "url" => $vcard->bniURL,
                "type" => "BNI"
            );
            array_push($urlArr, $url5);
        }

        if(strlen($vcard->linkdinCompany) > 5)
		{
            $url5 = array(
                "url" => $vcard->linkdinCompany,
                "type" => "LinkedIn"
            );
            array_push($urlArr, $url5);
        }

        if(strlen($vcard->youtubeChannel) > 5)
		{
            $url5 = array(
                "url" => $vcard->youtubeChannel,
                "type" => "Youtube"
            );
            array_push($urlArr, $url5);
        }

        if(strlen($vcard->indiamartLink) > 5)
		{
            $url5 = array(
                "url" => $vcard->indiamartLink,
                "type" => "IndiaMart"
            );
            array_push($urlArr, $url5);
        }

        if(strlen($vcard->tradeIndia) > 5)
		{
            $url5 = array(
                "url" => $vcard->tradeIndia,
                "type" => "TradeIndia"
            );
            array_push($urlArr, $url5);
        }
        
        if(strlen($vcard->bniPublicProfile) > 5)
		{
            $url5 = array(
                "url" => $vcard->bniPublicProfile,
                "type" => "BNI Public"
            );
            array_push($urlArr, $url5);
        }

        if(strlen($vcard->website1) > 5)
		{
            $url5 = array(
                "url" => $vcard->website1,
                "type" => "Personal"
            );
            array_push($urlArr, $url5);
        }

		$contactArr = [];

		$contactArr['name'] = $nameArr;

		if(sizeof($orgArray) > 0)
		{
			$contactArr['org'] = $orgArray;
		}

		if(sizeof($phoneArr) > 0)
		{
			$contactArr['phones'] = $phoneArr;
		}

		if(sizeof($emailArr) > 0)
		{
			$contactArr['emails'] = $emailArr;
		}

		if(sizeof($urlArr) > 0)
		{
			$contactArr['urls'] = $urlArr;
		}

		if(sizeof($address) > 3)
		{
			$contactArr['addresses'] = $addressArr;
		}

        return $contactArr;
    }

    public function sendContactCard($mobile, $contactArr)
    {
        try{
            $data = array(
                    "messaging_product" => "whatsapp",
                    "to" => $mobile,
                    "type" => "contacts",
                    "contacts" => array(
                        $contactArr
                    )
                );
    
            $url = 'https://graph.facebook.com/'.($this->version).'/'.($this->meta_phone_id).'/messages';
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->system_user_token,
                'Content-Type' => 'application/json',
            ])->post($url, $data);
 
            $response_data = json_decode($response, true);
            $message_id = $response_data['messages'][0]['id'];
            
            if(strlen($message_id) > 0)
            {
                $sent = new DataSent();
                $sent->messageId = $message_id;
                $sent->sentTo = $mobile;
                $sent->messageType = 'ContactCard';
                $sent->contents = json_encode($contactArr);
                $sent->save();
            }
            else{
                Log::error("Issue in sending Contact Card. mobile:".$mobile." ContactArr:".json_encode($contactArr)."  Response:".$response);
                return 0;
            }

            return 1;
        }
        catch(\Exception $e)
        {
            Log::error("Issue in sending ContactCard. mobile:".$mobile." ContactArr:".json_encode($contactArr)." Error:".$e->getMessage());
            return 0;
        }
    }

    public function sendContactCardFromCard($mobile, $contactArr)
    {
        try{
            $data = array(
                    "messaging_product" => "whatsapp",
                    "to" => $mobile,
                    "type" => "contacts",
                    "contacts" => array(
                        $contactArr
                    )
                );
    
            $url = 'https://graph.facebook.com/'.($this->version).'/'.($this->meta_phone_id).'/messages';
            
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $this->system_user_token,
                'Content-Type' => 'application/json',
            ])->post($url, $data);

            $response_data = json_decode($response, true);
            $message_id = isset($response_data['messages'][0]['id']) ? $response_data['messages'][0]['id'] : "NO ID";

            Log::info("Send Contact Card {$response}");
            $sent = new DataSent();
            $sent->messageId = $message_id;
            $sent->sentTo = $mobile;
            $sent->messageType = 'ContactCard';
            $sent->contents = $response;
            $sent->save();
        
            return 1;
        }
        catch(\Exception $e)
        {
            Log::error("Issue in sending ContactCard. mobile:".$mobile." ContactArr:".json_encode($contactArr)." Error:".$e->getMessage());
            return 0;
        }
    }

    private function processReceivedMessage($from, $type, $text, $profileName, $contextId)
    {
        if($type == "text" && substr($text,0,10) == 'VerifyCard')
        {
            $codeForCard = substr($text, -12);
            $vcard = Vcard::select('id','mobileNo1','firstName')->where('uniqueCode','=',$codeForCard)->first();
            if(is_null($vcard))
            {
                Log::info("Card code not found".$codeForCard);
                $this->sendTextMessage($from, "Hi, The card is not found. Please cross-check the code and send the message again.");
                return;
            }
            if($from == $vcard->mobileNo1)
            {
                Vcard::where('id','=',$vcard->id)->update(['status' => 1]);
                $this->sendTextMessage($from, "Hi $vcard->firstName,\n\nYour vcard has been verified.\n\nThank You.");
            }
            else{
                $this->sendTextMessage($from, "Hi $vcard->firstName,\n\n The verification text should only be sent from the primary number in the vcard.\n\nThank You");
            }
            
            return;
        }
        
        if($type == "text" && trim($text) == 'My Whatsapp V Card')
        {
            $user = User::select('id')->where('mobileno','=', $from)->first();
            if(is_null($user))
            {
                $this->sendTextMessage($from, "We are unable to locate your registered mobile number in our records. If you believe this is a mistake, please reach out to the administrator for assistance.");
            }
            else
            {
                User::where('id', '=', $user['id'])->update(['isMobileVerified'=> 1]);
                $this->sendTextMessage($from, "Thank you for registering on savemyvcard.com. Your mobile number is verified. Please save the contact card to receive updates from us.");
                $business = new BusinessLogicController();
                $contactArr = $business->smvcContactCard();
                $ret = $this->sendContactCard($from, $contactArr);
            }
            return;
        }

        if($type == "text" && substr($text,0,10) != 'VerifyCard' &&  in_array(substr($text, -4), ['SMVC', 'SMMS', 'SMTC']))
        {
            $codeForCard = substr($text, -12);

            $restOfCode = strlen($text)> 12 ? substr($text, 0, -12) : '-NA-';
            
            $vcard = Vcard::select('id','mobileNo1','firstName')->where('uniqueCode','=',$codeForCard)->orWhere('premiumCode','=',$codeForCard)->first();
            if(is_null($vcard))
            {
                if(substr($codeForCard,-4) == 'SMMS')
                {
                    $vcard = Vcard::select('id','mobileNo1','firstName')->where('mobileStickerCode','=',$codeForCard)->first();
                }
                else if(substr($codeForCard,-4) == 'SMTC')
                {
                    $vcard = Vcard::select('id','mobileNo1','firstName')->where('tentCardCode','=',$codeForCard)->first();
                }
                else
                {
                    Log::info("Card code not found".$codeForCard);
                    return;
                }
            }

            
            $this->sendTextMessage($from, "Thank you for using savemyvcard.com vCard exchange facility. You will receive the requested person's card soon.");
            $contactArr = $this->createVCard($vcard->id);
            $ret = $this->sendContactCard($from, $contactArr);
            if(!$ret)
            {
                return;
            }
            $count = LeadNotification::leftJoin('users', 'lead_notification.downloader_no', '=', 'users.mobileno')
                    ->whereNull('users.mobileno')
                    ->where('lead_notification.downloader_no','=',$from)
                    ->count();

            if($count % 5 == 1)
            {
                $this->sendTextMessage($from, "Thank you for using our services. \nYou can also create your own vcard on www.savemyvcard.in. \nYou can save our contact card for future reference.");
                $business = new BusinessLogicController();
                $contactArr = $business->smvcContactCard();
                $ret = $this->sendContactCard($from, $contactArr);
            }

            $userVCard = UserVCard::where('vcardid', $vcard->id)->with('user')->first();

            if ($userVCard && $userVCard->user) {
                $mobileNo = $vcard->mobileNo1;
                $country = $userVCard->user->country;
                $verified = $userVCard->user->isMobileVerified;
                if($verified == 1 && $country == '91')
                {
                    $message_id = $this->sendTemplateMessages($mobileNo, 'lead_notification_utility', [$vcard->firstName, $profileName, $restOfCode]);
                    $lead = new LeadNotification();
                    $lead->message_id = $message_id;
                    $lead->user_id = $userVCard->user->id;
                    $lead->vcard_id = $vcard->id;
                    $lead->downloader_name = $profileName;
                    $lead->message_received = $restOfCode;
                    $lead->downloader_no = $from;
                    $lead->save();
                }
                else{
                    Log::info("Lead Notification not sent to $mobileNo  as either mobile is not verified or the country is not india");
                }
            }
            
            return;
        }

        if($type == "button" && trim($text) == 'Download Contact Card')
        {
            $lead = LeadNotification::select('downloader_name','downloader_no')->where('message_id','=',$contextId)->first();
            $contactArr = $this->createLeadNotificationVCard($lead);
            $ret = $this->sendContactCard($from, $contactArr);     
            return;
        }

        if($type == "text" && trim($text) == 'Forgot Password')
        {
            $user = User::select('id')->where('mobileno','=', $from)->first();
            if(is_null($user))
            {
                $this->sendTextMessage($from, "We are unable to locate your registered mobile number in our records. If you believe this is a mistake, please reach out to the administrator for assistance.");
            }
            else
            {
                $randomNumber = random_int(100000, 999999);
                User::where('id', '=', $user['id'])->update(['isMobileVerified'=> 1, 'password' => md5($randomNumber)]);
                $this->sendTextMessage($from, "Hi, \nYour password has been reset to *$randomNumber*\n\nLogin using your registered email id and the password on www.savemyvcard.com");
            }
            return;
        }

        if($type == 'image')
        {
            $this->classifyTheCard();
        }
    }

    public function classifyTheCard()
    {
        
            $dataReceived = DataReceived::select('id', 'mediaURL', 'URL', 'messageText', 'senderPhoneNo')
                            ->where('messagetype', 'image')
                            ->where('addressName', 'notprocessed')
                            ->where('created_at', '>=', Carbon::now()->subHour()) // Check last 1 hour
                            ->limit(2)
                            ->get();

            Log::info("Starting process OCR Images function");
            foreach ($dataReceived as $data) 
            {
                sleep(1);
                try 
                {
                    $openAIService = new OpenAIController();
                    $media_url = "businesscards/{$data->mediaURL}.jpeg";
                    $content = Storage::disk('s3')->get($media_url);
                    $imageUrl = Storage::disk('s3')->temporaryUrl("businesscards/{$data->mediaURL}.jpeg", now()->addMinutes(5));
                    //$imageData = base64_encode($content);
                    $confirmation = $openAIService->checkIfBusinessCard($imageUrl, 'url');//Check if the image is a business card
                    //$confirmation = $openAIService->checkIfBusinessCard($imageData, 'base64');//Check if the image is a business card
                    if ($confirmation) {
                        $cardtext = new CardExtractionDetails();
                        $cardtext->media_id = $data->mediaURL;
                        $cardtext->isProcessedByAi = '1';
                        $cardtext->aiResponse = '';
                        $cardtext->save();
                        $controller = new BusinessLogicController();
                        $response = $controller->processBusinessCardUsingAI();
                    } else {
                        Log::warning("This image doesnt contain a business card: " . $data->mediaURL);
                    }
                    DataReceived::where('messagetype','=','image')->where('id','=',$data->id)->update(['addressName' => 'processed']);
                } catch (\Throwable $th) {
                    Log::error("Error in OCR processing: " . $th->getMessage());
                }
            }
    }

    public function callOCRSpaceAPI($media_id)
    {
        $url = 'https://api.ocr.space/parse/image';
        $apiKey = env('FREE_OCR_API_KEY', 'K84246638388957');

        $media_url = "https://smvcbusinesscards.s3.us-east-1.amazonaws.com/businesscards/{$media_id}.jpeg";

        $imageData = base64_encode(file_get_contents($media_url));

        $response = Http::withHeaders([
                'apikey' => $apiKey,
            ])->attach(
                'language', 'eng'
            )->attach(
                'isOverlayRequired', 'false'
            )->attach(
                'url', $media_url
            )->attach(
                'iscreatesearchablepdf', 'false'
            )->attach(
                'issearchablepdfhidetextlayer', 'false'
            )->attach(
                'filetype' , 'JPG'
            )->post($url);

        Log::info("FREE OCR RESPONSE ::File Link: {$media_url} \n\nResponse{$response}");
        $responseData = $response->json(); // Convert response to array

        $parsedResults = $responseData['ParsedResults'] ?? [];

        return [
            'IsErroredOnProcessing' => $responseData['IsErroredOnProcessing'] ?? true,
            'ParsedText' => !empty($parsedResults) ? ($parsedResults[0]['ParsedText'] ?? '') : ''
        ];
    }

    private function createLeadNotificationVCard($leadArray)
    {
        $nameArr = array(
            "formatted_name" => $leadArray->downloader_name,
            "first_name" => $leadArray->downloader_name
        );
        
        $phoneArr = [];

        $mobile = array(
                "phone" => '+'.$leadArray->downloader_no,
                "wa_id" =>  $leadArray->downloader_no,
                "type" => "WORK"
            );

        array_push($phoneArr, $mobile);
        
        
        $contactArr = [];
        $contactArr['name'] = $nameArr;
        $contactArr['phones'] = $phoneArr;

        return $contactArr;
    }

    public function createVCardFromData($structuredData)
    {
        if(!isset($structuredData['Name']))
        {  
            return null;
        }

        $nameArr = [];

        if(is_array($structuredData['Name']))
        {
            $nameArr = array(
                "formatted_name" => implode(",",$structuredData['Name']),
                "first_name" => implode(",",$structuredData['Name'])
            );
        } else if(is_string($structuredData['Name']))
        {
            $nameArr = array(
                "formatted_name" => $structuredData['Name'],
                "first_name" => $structuredData['Name']
            );
        }

		$orgArray = [];

        if(is_string($structuredData['Company_Name']) && strlen($structuredData['Company_Name']) > 0)
		{
			$orgArray['company'] = $structuredData['Company_Name'];
		} else if (is_array($structuredData['Company_Name']))
		{
			$orgArray['company'] = implode(",",$structuredData['Company_Name']);
		} 

		if( is_string($structuredData['Job_Title']) && strlen($structuredData['Job_Title']) > 0)
		{
			$orgArray['title'] = $structuredData['Job_Title'];
		} else if( is_array($structuredData['Job_Title']))
		{
			$orgArray['title'] =  implode(",",$structuredData['Job_Title']);
		}
		
		$phoneArr = [];

        $contacts = $structuredData['Contact'];
        
        foreach($contacts as $contact)
        {
            $numbersOnly = preg_replace('/\D/', '', $contact);
            // If the number starts with "0", remove it
            if (strlen($numbersOnly) > 10 && $numbersOnly[0] === '0') {
                $numbersOnly = substr($numbersOnly, 1);
            }

            // If the number is exactly 10 digits, prepend "91"
            if (strlen($numbersOnly) === 10) {
                $numbersOnly = "91" . $numbersOnly;
            }
            
            if (preg_match('/^91[9876]/', $numbersOnly)) {
                $phone1 = array(
                        "phone" => '+'.$numbersOnly,
                        "wa_id" => $numbersOnly,
                        "type" => "WORK"
                    );
                array_push($phoneArr, $phone1);
            }
            else{
                $phone1 = array(
                        "phone" => '+'.$numbersOnly,
                        "type" => "WORK"
                    );
                array_push($phoneArr, $phone1);
            }
            
        }

		$emailArr = [];

        $emails = $structuredData['Email_ID'];
        if (is_array($emails))
        {
            foreach($emails as $email)
            {
                $email1 = array(
                    "email" => $email,
                    "type" => "WORK"
                );
                array_push($emailArr, $email1);
            }
        } else if(strlen($emails)>5){
            $email1 = array(
                "email" => $emails,
                "type" => "WORK"
            );
            array_push($emailArr, $email1);
        }



		$addressArr = [];
		
        if (is_array($structuredData['Address'])) {
            foreach($structuredData['Address'] as $adr)
            {
                $address = [];
                $address["type"] = 'Work';
                $address['street'] = $adr;
                array_push($addressArr, $address);
            }
            
        } elseif (is_string($structuredData['Address'])) {
            $address = [];
            $address["type"] = 'Work';
            $address['street'] = $structuredData['Address'];
            array_push($addressArr, $address);
        }

		

		$urlArr = [];

        if(is_array($structuredData['Website']))
        {
            foreach($structuredData['Website'] as $web)
            {
                $url5 = array(
                    "url" => $web,
                    "type" => "Company"
                );
                array_push($urlArr, $url5);
            }
        }
        else if(strlen($structuredData['Website']) > 5)
		{
            $url5 = array(
                "url" => $structuredData['Website'],
                "type" => "Company"
            );
            array_push($urlArr, $url5);
        }

		$contactArr = [];

		$contactArr['name'] = $nameArr;

		if(sizeof($orgArray) > 0)
		{
			$contactArr['org'] = $orgArray;
		}

		if(sizeof($phoneArr) > 0)
		{
			$contactArr['phones'] = $phoneArr;
		}

		if(sizeof($emailArr) > 0)
		{
			$contactArr['emails'] = $emailArr;
		}

		if(sizeof($urlArr) > 0)
		{
			$contactArr['urls'] = $urlArr;
		}

		if(sizeof($addressArr) > 0)
		{
			$contactArr['addresses'] = $addressArr;
		}
        Log::info("Structured contact json:: ".json_encode($contactArr));
        return $contactArr;
    }
}
