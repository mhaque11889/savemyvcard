@php
  $session = session()->all();
@endphp

@extends('layoutMember.main')
@section('main-section')
  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Edit Scanned Business Card</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ url('/memberDashboard'); }}">Home</a></li>
          <li class="breadcrumb-item">Edit Scanned Business Card</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Edit Scanned Business Card</h5>
              <p style="font-size:12px; color:red">Fields with asterisk (*) sign are mandatory. If primary number is changed, you have to activate the card again.</p>
              <form class="row g-3" method="post" action="{{ url('/updateScannedBusinessCard') }}">
                @csrf
                @php
                  $data = json_decode($carddetails->aiResponse, true); // Decode JSON into array
                @endphp
                <div class="col-md-6 col-sm-12">
                  <label for="cardName" class="form-label">Name* </label>
                  <input type="text" class="form-control" id="cardName" name="cardName" required="" value=" {{ is_array($data['Name']) ? implode(', ', $data['Name'] ?? []) : $data['Name'] }}">
                  <input type="hidden" name="vcardid" value=""/>
                </div>
                
                <div class="col-md-6 col-sm-12">
                    <label for="jobTitle" class="form-label">Job Title</label>
                    <input type="text" class="form-control" id="jobTitle" name="jobTitle" value="{{ is_array($data['Job_Title']) ? implode(', ', $data['Job_Title'] ?? []) : $data['Job_Title'] }}">
                </div>
                <div class="col-md-6 col-sm-12">
                  <label for="companyName" class="form-label">Company Name</label>
                  <input type="text" class="form-control" id="companyName"  name="companyName" value="{{ is_array($data['Company_Name']) ? implode(', ', $data['Company_Name'] ?? []) : $data['Company_Name'] }} ">
                </div>

                @if(is_array($data['Contact']))
                  
                  @foreach ($data['Contact'] as $index => $contact)
                    <div class="col-md-6 col-sm-12">
                      <label for="contactNo{{ $index+1 }}" class="form-label">Mobile Number {{ $index+1 }}</label>
                      <input type="number" class="form-control" id="contactNo{{ $index+1 }}" name="contactNo{{ $index+1 }}" value="{{ $contact }}">
                    </div>
                  @endforeach

                @else
                  <div class="col-md-6 col-sm-12">
                    <label for="contactNo1" class="form-label">Mobile Number 1</label>
                    <input type="number" class="form-control" id="contactNo1" name="contactNo1" value=" {{ $data['Contact'] }}" >
                  </div>
                @endif


                @if(is_array($data['Email_ID']))
                  
                  @foreach ($data['Email_ID'] as $index => $emailidRow)
                    <div class="col-md-6 col-sm-12">
                      <label for="emailAddress{{ $index+1 }}" class="form-label">Email Address  {{ $index+1 }}</label>
                      <input type="text" class="form-control" id="emailAddress{{ $index+1 }}" aria-describedby="emailAddress{{  $index+1 }}sign" name="emailid{{ $index+1 }}" value="{{ $emailidRow }}">
                    </div>
                  @endforeach

                @else
                  <div class="col-md-6 col-sm-12">
                    <label for="emailAddress1" class="form-label">Email Address </label>
                    <div class="input-group">
                        <span class="input-group-text" id="emailAddress1sign">@</span>
                        <input type="text" class="form-control" id="emailAddress1" aria-describedby="emailAddress1sign" name="emailid1" value="{{  $data['Email_ID'] }}">
                    </div>
                  </div>
                @endif
                
                

                <div class="col-md-12 col-sm-12">
                    <label for="address" class="form-label">Address</label>
                    <input type="text" class="form-control" id="address" name="address" value="{{ is_array($data['Address']) ? implode(', ', $data['Address']) ?? [] : $data['Address'] }}">
                </div>

                <div class="col-md-6">
                    <label for="website1Box" class="form-label">Website 1</label>
                    <div class="input-group">
                      <span class="input-group-text" id="website1"><i class="ri-earth-fill"></i></span>
                      <input type="text" class="form-control" id="website1Box" aria-describedby="website1" name="website1" >
                    </div>
                </div>

                <div class="col-md-6">
                  <label for="website2Box" class="form-label">Website 2</label>
                  <div class="input-group">
                    <span class="input-group-text" id="website2"><i class="ri-earth-fill"></i></span>
                    <input type="text" class="form-control" id="website2Box" aria-describedby="website2" name="website2" >
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
                  {{-- <button class="btn btn-primary" type="submit">Update</button> --}}
                </div>
              </form>
            </div>
          </div>


        </div>

        

      </div>
    </section>

  </main><!-- End #main -->

  <script>
    
  </script>
@endsection