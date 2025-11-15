<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OpenAIController extends Controller
{
    protected $apiKey = 'OpenAI_API_Key_Here';
    
    public function __construct()
    {
        $this->apiKey = env('OPENAI_API_KEY');
    }

    public function checkIfBusinessCard($image, $type)
    {
        // Define the request payload
        $imageUrl = '';
        if($type == 'url')
        {
            $imageUrl = $image;
        }
        else if($type=="base64"){
            $imageUrl = "data:image/jpeg;base64," . $image;
        }

        $payload = [
            "model" => "gpt-4o-mini",
            "messages" => [
                [
                    "role" => "user",
                    "content" => [
                        [
                            "type" => "text",
                            "text" => "Does this image contain a business card? Define this as a business card only if a contact number and name/company name is detected. Reply with 'Yes' or 'No'."
                        ],
                        [
                            "type" => "image_url",
                            "image_url" => [
                                "url" => "{$imageUrl}",
                                "detail" => "low"
                            ]
                        ]
                    ]
                ]
            ],
            "response_format" => [
                "type" => "text"
            ],
            "temperature" => 0,
            "max_tokens" => 10
        ];

        $url = "https://api.openai.com/v1/responses";
        $url2 = "https://api.openai.com/v1/chat/completions";
        // Send API request to OpenAI
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json'
        ])->post($url2, $payload);

        Log::info("OpenAI API Response: " . $response->body());
        // Extract response text
        if ($response->successful()) {
            $responseText = strtolower(trim($response->json()['choices'][0]['message']['content']));
            return ($responseText === 'yes');
        }

        return false;
    }

    public function extractBusinessCardDetails($image, $type)
    {
        // Define the request payload
        $imageUrl = '';
        if($type == 'url')
        {
            $imageUrl = $image;
        }
        else if($type=="base64"){
            $imageUrl = "data:image/jpeg;base64," . $image;
        }

        $payload = [
            "model" => "gpt-4o-mini",
            "messages" => [
                [
                    "role" => "user",
                    "content" => [
                        [
                            "type" => "text",
                            "text" => "Please extract business card details from the image and format them into structured JSON? Required fields: Name, Job_Title, Company_Name, Contact, Email_ID, Address, Website. There can be more than one Contact_number,email_id, or address. Also check for Fax and Cell number and include it in contact. Website should not have any spacing. Return only valid JSON without additional text."
                        ],
                        [
                            "type" => "image_url",
                            "image_url" => [
                                "url" => "{$imageUrl}"
                            ]
                        ]
                    ]
                ]
            ],
            "response_format" => [
                "type" => "text"
            ],
            "temperature" => 0,
            "max_tokens" => 500
        ];
        Log::info('OpenAI Payload:', $payload);
        $url = "https://api.openai.com/v1/responses";
        $url2 = "https://api.openai.com/v1/chat/completions";
        // Send API request to OpenAI
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->apiKey,
            'Content-Type' => 'application/json'
        ])->post($url2, $payload);

        // Log response for debugging
        Log::info("OpenAI API Response: " . $response->body());

        // Extract response data
        if ($response->successful()) {
            $jsonContent = $response->json()['choices'][0]['message']['content'];
            $cleanedJson = preg_replace('/```json|```/', '', trim($jsonContent)); // Remove markdown formatting
            return json_decode($cleanedJson, true);
        }

        return null;
    }

    public function extractCardCreationData($json)
    {
          
        $instruction = <<<EOT
        From the following JSON, extract and convert the data into a normalized JSON format with the exact keys:
        firstname, lastname, emailid, contactno, jobtitle, companyname, address, website.
        
        Only return one value per key. If the field is empty or not present, return null.
        - Parse "Name" to get firstname and lastname. If only one name is present, assume it's the firstname.
        - For "Contact" and "Email_ID", return only the first value if multiple.
        - Do not include any explanation, only return valid JSON.
        
        Input JSON:
        $json
        EOT;
        
        $payload = [
            'model' => 'gpt-4o-mini',
            'messages' => [
                ['role' => 'user', 'content' => $instruction]
            ],
            'temperature' => 0,
            'max_tokens' => 300,
            'response_format' => ['type' => 'text']
        ];

        Log::info('OpenAI Payload:', $payload);
        $url = "https://api.openai.com/v1/responses";
        $url2 = "https://api.openai.com/v1/chat/completions";
        // Send API request to OpenAI
        $response = Http::withHeaders([
            'Authorization' => 'Bearer '.$this->apiKey,
            'Content-Type' => 'application/json'
        ])->post($url2, $payload);

        // Log response for debugging
        Log::info("OpenAI API Response: " . $response->body());

        // Extract response data
        if ($response->successful()) {
            $jsonContent = $response->json()['choices'][0]['message']['content'] ?? null;
            return $jsonContent;
        }

        return null;
    }
}
