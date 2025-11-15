@php
  $session = session()->all();
@endphp

@extends('layoutMember.main')
@section('main-section')
  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Edit V Card</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ url('/memberDashboard'); }}">Home</a></li>
          <li class="breadcrumb-item">Edit V Card</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Edit V Card</h5>
              <p style="font-size:12px; color:red">Fields with asterisk (*) sign are mandatory. If primary number is changed, you have to activate the card again.</p>
              <form class="row g-3" method="post" action="{{ url('/updateVCard') }}">
                @csrf
                <div class="col-md-2">
                    <label for="salutation" class="form-label">Salutation</label>
                    <input type="text" class="form-control" id="salutation" name="salutation_text" value="{{ $vcard->salutation_text }}">
                </div>
                <div class="col-md-4">
                  <label for="firstName" class="form-label">First name* </label>
                  <input type="text" class="form-control" id="firstName" name="firstName" required="" value="{{ $vcard->firstName }}">
                  <input type="hidden" name="useremail" value="{{ $session['username']; }}"/>
                  <input type="hidden" name="vcardid" value="{{ $vcard->id; }}"/>
                </div>
                <div class="col-md-2">
                    <label for="middleName" class="form-label">Middle Name</label>
                    <input type="text" class="form-control" id="middleName" name="middleName" value="{{ $vcard->middleName }}">
                </div>
                <div class="col-md-4">
                  <label for="lastName" class="form-label">Last name* </label>
                  <input type="text" class="form-control" id="lastName" required="" name="lastName" value="{{ $vcard->lastName }}">
                </div>
                <div class="col-md-6">
                    <label for="jobTitle" class="form-label">Job Title</label>
                    <input type="text" class="form-control" id="jobTitle" name="jobTitle" value="{{ $vcard->jobTitle }}">
                </div>
                <div class="col-md-6">
                  <label for="companyName" class="form-label">Company Name</label>
                  <input type="text" class="form-control" id="companyName"  name="companyName" value="{{ $vcard->companyName }}">
                </div>
                <div class="col-md-6">
                  <label for="industryName" class="form-label">Industry</label>
                  <input type="text" class="form-control" id="industryName" name="industry" value="{{ $vcard->industry }}">
              </div>
              <div class="col-md-6">
                <label for="subIndustryName" class="form-label">Sub Industry</label>
                <input type="text" class="form-control" id="subIndustryName" name="subIndustry" value="{{ $vcard->subIndustry }}">
              </div>
                <div class="col-md-6">
                    <label for="emailAddress1" class="form-label">Email Address 1*</label>
                    <div class="input-group">
                        <span class="input-group-text" id="emailAddress1sign">@</span>
                        <input type="text" class="form-control" id="emailAddress1" aria-describedby="emailAddress1sign" required="" name="emailid1" value="{{ $vcard->emailid1 }}">
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="emailAddress2" class="form-label">Email Address 2</label>
                    <div class="input-group">
                        <span class="input-group-text" id="emailAddress2sign">@</span>
                        <input type="text" class="form-control" id="emailAddress2" aria-describedby="emailAddress2sign" name="emailid2" value="{{ $vcard->emailid2 }}">
                    </div>
                </div>

                <div class="col-md-2 col-sm-4">
                    <label for="countryCode1" class="form-label">CountryCode 1*</label>
                    <select class="form-select" id="countryCode1" required="" name="cc1">
                      <option selected="" disabled="" value="">Choose...</option>
                      <option data-countrycode="IN" value="91">India (+91)</option>
                      <optgroup label="--">
                        @foreach ($countries as $country)
                          @if($country->phonecode == $vcard->countryCode1)
                          <option value="{{ $country->phonecode }}" selected="selected">{{ $country->name }}(+{{ $country->phonecode }})</option>
                          @else
                          <option value="{{ $country->phonecode }}">{{ $country->name }}(+{{ $country->phonecode }})</option>
                          @endif
                        @endforeach
                      </optgroup>
                    </select>
                  </div>
                <div class="col-md-4 col-sm-8">
                  <label for="mobileNumber1" class="form-label">Mobile Number 1*</label>
                  <input type="number" class="form-control" id="mobileNo1" name="mobileNo1" required="" value="{{ substr($vcard->mobileNo1, strlen($vcard->countryCode1), strlen($vcard->mobileNo1)) }}">
                </div>

                <div class="col-md-2 col-sm-4">
                    <label for="countryCode2" class="form-label">CountryCode 2</label>
                    <select class="form-select" id="countryCode2" name="cc2"> 
                      <option selected="" disabled="" value="">Choose...</option>
                      <option data-countrycode="IN" value="91">India (+91)</option>
                      <optgroup label="--">
                        @foreach ($countries as $country)
                          @if($country->phonecode == $vcard->countryCode2)
                          <option value="{{ $country->phonecode }}" selected="selected">{{ $country->name }}(+{{ $country->phonecode }})</option>
                          @else
                          <option value="{{ $country->phonecode }}">{{ $country->name }}(+{{ $country->phonecode }})</option>
                          @endif
                          
                        @endforeach
                      </optgroup>
                    </select>
                  </div>
                <div class="col-md-4 col-sm-8">
                  <label for="mobileNumber2" class="form-label">Mobile Number 2</label>
                  <input type="number" class="form-control" id="mobileNumber2" name="mobileNo2"  value="{{ substr($vcard->mobileNo2, strlen($vcard->countryCode1), strlen($vcard->mobileNo2)) }}">
                </div>

                <div class="col-md-6 col-sm-12">
                    <label for="addressLine1" class="form-label">Address Line 1</label>
                    <input type="text" class="form-control" id="addressLine1" name="addressLine1" value="{{ $vcard->addressLine1 }}">
                </div>

                <div class="col-md-6 col-sm-12">
                    <label for="addressLine2" class="form-label">Address Line 2</label>
                    <input type="text" class="form-control" id="addressLine2" name="addressLine2" value="{{ $vcard->addressLine2 }}">
                </div>

                <div class="col-md-3 col-sm-6">
                    <label for="country" class="form-label">Country</label>
                    <select class="form-select" id="country" name="country" onchange="populateState(this.value)">
                      @if(is_null($vcard->country) || strlen($vcard->country) == 0)
                        <option disabled="" selected="selected" value="">Choose...</option>
                      @endif
                      
                      @foreach ($countries as $country)
                        @if(strcasecmp($vcard->country, $country->name) == 0)
                        <option value="{{ $country->id }}" selected="selected">{{ $country->name }}</option>
                        @else
                        <option value="{{ $country->id }}">{{ $country->name }}</option>
                        @endif
                      @endforeach
                    </select>
                </div>

                <div class="col-md-3 col-sm-6">
                    <label for="CountryState" class="form-label">State</label>
                    <select class="form-select" id="CountryState" name="state" onchange="populateCity(this.value)">
                      <option disabled="" value="--">Choose</option>
                      <option selected="selected" value="{{ $vcard->state }}">{{ $vcard->state }}</option>
                    </select>
                </div>

                <div class="col-md-3 col-sm-6">
                    <label for="cityName" class="form-label">City</label>
                    <select class="form-select" id="cityName" name="city">
                      <option disabled="" value="--">Choose</option>
                      <option selected="selected" value="{{ $vcard->city }}"> {{ $vcard->city }}</option>
                    </select>
                </div>

                <div class="col-md-3 col-sm-6">
                    <label for="zipCode" class="form-label">Zip Code</label>
                    <input type="text" class="form-control" id="zipCode" name="zipcode" value="{{ $vcard->zipcode }}">
                </div>

                <div class="col-md-6">
                  <label for="linkedInURLbox" class="form-label">LinkedIn URL</label>
                  <div class="input-group">
                    <span class="input-group-text" id="linkedInURL"><i class="bi bi-linkedin"></i></span>
                    <input type="text" class="form-control" id="linkedInURLbox" aria-describedby="linkedInURL" name="linkdnURL" value="{{ $vcard->linkdnURL }}">
                  </div>
                </div>

                <div class="col-md-6">
                    <label for="twitterURLBox" class="form-label">Twitter</label>
                    <div class="input-group">
                      <span class="input-group-text" id="twitterURL"><i class="bi bi-twitter"></i></span>
                      <input type="text" class="form-control" id="twitterURLBox" aria-describedby="twitterURL" name="twitterURL" value="{{ $vcard->twitterURL }}">
                    </div>
                </div>

                <div class="col-md-6">
                    <label for="instaURLBox" class="form-label">Instagram</label>
                    <div class="input-group">
                      <span class="input-group-text" id="twitterURL"><i class="bi bi-instagram"></i></span>
                      <input type="text" class="form-control" id="instaURLBox" aria-describedby="twitterURL" name="instaURL" value="{{ $vcard->instaURL }}">
                    </div>
                </div>

                <div class="col-md-6">
                    <label for="fbURLBox" class="form-label">Facebook</label>
                    <div class="input-group">
                      <span class="input-group-text" id="fbURL"><i class="bi bi-facebook"></i></span>
                      <input type="text" class="form-control" id="fbURLBox" aria-describedby="fbURL" name="facebookURL" value="{{ $vcard->facebookURL }}">
                    </div>
                </div>

                <div class="col-md-6">
                    <label for="youtubeURLBox" class="form-label">Youtube</label>
                    <div class="input-group">
                      <span class="input-group-text" id="youtubeURL"><i class="bi bi-youtube"></i></span>
                      <input type="text" class="form-control" id="youtubeURLBox" aria-describedby="youtubeURL" name="youtubeChannel" value="{{ $vcard->youtubeChannel }}">
                    </div>
                </div>

                <div class="col-md-6">
                    <label for="indiaURLBox" class="form-label">IndiaMart</label>
                    <div class="input-group">
                      <span class="input-group-text" id="indiaURL"><i class="ri-earth-fill"></i></span>
                      <input type="text" class="form-control" id="indiaURLBox" aria-describedby="indiaURL" name="indiamartLink" value="{{ $vcard->indiamartLink }}">
                    </div>
                </div>

                <div class="col-md-6">
                    <label for="bniURLBox" class="form-label">BNI Connect</label>
                    <div class="input-group">
                      <span class="input-group-text" id="bniURL"><i class="ri-earth-fill"></i></span>
                      <input type="text" class="form-control" id="bniURLBox" aria-describedby="bniURL" name="bniURL" value="{{ $vcard->bniURL }}">
                    </div>
                </div>

                <div class="col-md-6">
                    <label for="tradeIndiaURLBox" class="form-label">Trade India</label>
                    <div class="input-group">
                      <span class="input-group-text" id="tradeIndiaURL"><i class="ri-earth-fill"></i></span>
                      <input type="text" class="form-control" id="tradeIndiaURLBox" aria-describedby="tradeIndiaURL" name="tradeIndia" value="{{ $vcard->tradeIndia }}">
                    </div>
                </div>

                <div class="col-md-6">
                    <label for="bniPublicProfileBox" class="form-label">BNI Public Profile Link</label>
                    <div class="input-group">
                      <span class="input-group-text" id="bniPublicProfile"><i class="ri-earth-fill"></i></span>
                      <input type="text" class="form-control" id="bniPublicProfileBox" aria-describedby="bniPublicProfile" name="bniPublicProfile" value="{{ $vcard->bniPublicProfile }}">
                    </div>
                </div>

                <div class="col-md-6">
                    <label for="website1Box" class="form-label">Other Website</label>
                    <div class="input-group">
                      <span class="input-group-text" id="website1"><i class="ri-earth-fill"></i></span>
                      <input type="text" class="form-control" id="website1Box" aria-describedby="website1" name="website1" value="{{ $vcard->website1 }}">
                    </div>
                </div>
                
                
                
                <div class="col-12">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="" id="invalidCheck2" required="">
                    <label class="form-check-label" for="invalidCheck2">
                      Agree to terms and conditions
                    </label>
                  </div>
                </div>
                <div class="col-12">
                  <button class="btn btn-primary" type="submit">Update</button>
                </div>
              </form>
            </div>
          </div>


        </div>

        

      </div>
    </section>

  </main><!-- End #main -->

  <script>
    function populateState(countryId)
    {
      console.log('Populate State. Country Id:'+countryId);
      const CSRF_TOKEN = $('[name="_token"]').val();
      $.ajax
      ({
        type:'POST',
        url:'/getStates',
        data: {_token: CSRF_TOKEN, message: {'countryId' : countryId}},
        success:function(states) 
        {
          $('#CountryState').children('option:not(:first)').remove();
          $.each(states, function(index, state) {
              $('#CountryState').append('<option value="' + state.id + '">' + state.name + '</option>');
          });
        },
        error: function(message)
        {
          console.log(message);
        }
      });
    }

    function populateCity(stateid)
    {
      const CSRF_TOKEN = $('[name="_token"]').val();
      $.ajax
      ({
        type:'POST',
        url:'/getCities',
        data: {_token: CSRF_TOKEN, message: {'stateid' : stateid}},
        success:function(cities) 
        {
          $('#cityName').children('option:not(:first)').remove();
          $.each(cities, function(index, city) {
              $('#cityName').append('<option value="' + city.id + '">' + city.name + '</option>');
          });
        }
      });
    }

  </script>
@endsection