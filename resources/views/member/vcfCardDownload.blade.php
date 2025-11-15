@php
// Set the headers to download the file as a VCF
header('Content-Type: text/vcard');
header('Content-Disposition: attachment; filename="contact.vcf"');
// Define the VCard content
$vcard = "BEGIN:VCARD\r\n";
$vcard .= "VERSION:3.0\r\n";

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

$vcard .= "FN:".$nameArr['formatted_name']."\r\n"; // Full Name

    if(strlen($vcard->companyName) > 0)
		{
			$vcard .= "ORG:$vcard->companyName\r\n"; // Organization
		}
		if(strlen($vcard->jobTitle) > 0)
		{
			$vcard .= "TITLE:$vcard->jobTitle\r\n"; // Job tiTLe
		}

		if(strlen($vcard->mobileNo1) > 5)
		{
      $vcard .= "TEL;TYPE=WORK,VOICE:+$vcard->mobileNo1\r\n"; // Work Phone
		}
		if(strlen($vcard->mobileNo2) > 5)
		{
			$vcard .= "TEL;TYPE=WORK,VOICE:+$vcard->mobileNo2\r\n"; // Work Phone
		}
    
		if(strlen($vcard->emailid1) > 5)
		{
      $vcard .= "EMAIL;TYPE=WORK,INTERNET:$vcard->emailid1\r\n"; // Work Email
		}
		if(strlen($vcard->emailid2) > 5)
		{
			$vcard .= "EMAIL;TYPE=WORK,INTERNET:$vcard->emailid2\r\n"; // Work Email
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
		}

		$urlArr = [];

    if(strlen($vcard->facebookURL) > 5)
    {
    $url1 = array(
    "url" => $vcard->facebookURL,
    "type" => "Facebook Page"
    );
    $vcard .= "URL;TYPE=WORK:$vcard->facebookURL\r\n"; // Work Website
    }

    if(strlen($vcard->instaURL) > 5)
    {
    $url2 = array(
    "url" => $vcard->instaURL,
    "type" => "Instagram"
    );
    $vcard .= "URL;TYPE=WORK:$vcard->instaURL\r\n"; // Work Website
    }

    if(strlen($vcard->twitterURL) > 5)
    {
    $url3 = array(
    "url" => $vcard->twitterURL,
    "type" => "Twitter"
    );
    $vcard .= "URL;TYPE=WORK:$vcard->twitterURL\r\n"; // Work Website
    }

    if(strlen($vcard->linkdnURL) > 5)
    {
    $url4 = array(
    "url" => $vcard->linkdnURL,
    "type" => "LinkedIn"
    );
    $vcard .= "URL;TYPE=WORK:$vcard->linkdnURL\r\n"; // Work Website
    }

    if(strlen($vcard->bniURL) > 5)
    {
    $url5 = array(
    "url" => $vcard->bniURL,
    "type" => "BNI"
    );
    $vcard .= "URL;TYPE=WORK:$vcard->bniURL\r\n"; // Work Website
    }

    if(strlen($vcard->linkdinCompany) > 5)
    {
    $url5 = array(
    "url" => $vcard->linkdinCompany,
    "type" => "LinkedIn"
    );
    $vcard .= "URL;TYPE=WORK:$vcard->linkdinCompany\r\n"; // Work Website
    }

    if(strlen($vcard->youtubeChannel) > 5)
    {
    $url5 = array(
    "url" => $vcard->youtubeChannel,
    "type" => "Youtube"
    );
    $vcard .= "URL;TYPE=WORK:$vcard->youtubeChannel\r\n"; // Work Website
    }

    if(strlen($vcard->indiamartLink) > 5)
    {
    $url5 = array(
    "url" => $vcard->indiamartLink,
    "type" => "IndiaMart"
    );
    $vcard .= "URL;TYPE=WORK:$vcard->indiamartLink\r\n"; // Work Website
    }

    if(strlen($vcard->tradeIndia) > 5)
    {
    $url5 = array(
    "url" => $vcard->tradeIndia,
    "type" => "TradeIndia"
    );
    $vcard .= "URL;TYPE=WORK:$vcard->tradeIndia\r\n"; // Work Website
    }

    if(strlen($vcard->bniPublicProfile) > 5)
    {
    $url5 = array(
    "url" => $vcard->bniPublicProfile,
    "type" => "BNI Public"
    );
    $vcard .= "URL;TYPE=WORK:$vcard->bniPublicProfile\r\n"; // Work Website
    }

    if(strlen($vcard->website1) > 5)
    {
    $url5 = array(
    "url" => $vcard->website1,
    "type" => "Personal"
    );
    $vcard .= "URL;TYPE=WORK:$vcard->website1\r\n"; // Work Website
    }

		

		if(sizeof($address) > 3)
		{
      $vcard .= "ADR;TYPE#HOME:;;".$address['street'].";".$address['city'].";".$address['state'].";".$address['zip'].";".$address['country']."\r\n"; // Home Address
      $vcard .= "LABEL;TYPE#HOME:".$address['street']."\n".$address['city']."\n".$address['state']."\n".$address['zip']."\n".$address['country']."\r\n"; // Home Address
		}

    $vcard .= "END:VCARD\r\n";
    echo $vcard;
@endphp