<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Members extends Model
{
    use HasFactory;

    protected $table = 'sellers';
    protected $primaryKey = 'id';

    protected $fillable = [
        'salutation',
        'firstName',
		'middleName',
		'lastName',
		'emailId',
		'password',
		'address',
		'address1',
		'country',
		'state',
		'city',
		'locations',
		'pinCode',
		'mobile',
		'cc1',
		'cc2',
		'cc3',
		'phone',
		'phone1',
		'phone2',
		'commercialBuidingName',
		'industries',
		'otherIndustry',
		'otherLocation',
		'subindustries',
		'mapAddress',
		'latitude',
		'longitude',
		'facebookURL',
		'instaURL',
		'twitterURL',
		'linkdnURL',
		'bniURL',
		'website',
		'linkdnCompany',
		'facebookPage',
		'youtubeChannel',
		'indiamartLink',
		'tradeIndia',
		'bniPublicProfile',
		'otherLink1',
		'otherLink2',
		'products',
		'otherProducts',
		'otherEmailId',
		'status',
		'uniqueCode1',
		'uniqueCode2',
		'validation'
    ];
}
