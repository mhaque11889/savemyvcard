@php
  $session = session()->all();
@endphp

@extends('layoutMember.main')
@section('main-section')
  <main id="main" class="main">

    <div class="pagetitle">
      <h1>VCard Interaction</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ url('/memberDashboard'); }}">Home</a></li>
          <li class="breadcrumb-item">Edit VCard Interaction</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">How User interact when they scan your Save My VCard QR code.</h5>
              <p style="font-size:12px; color:red">This option is dynamic and the same QR code will change behavious as per the option selected.</p>
              @if ($errors->any())
                  <div class="alert alert-danger">
                      <ul>
                          @foreach ($errors->all() as $error)
                              <li>{{ $error }}</li>
                          @endforeach
                      </ul>
                  </div>
              @endif

              @if (session('success'))
                  <div class="alert alert-success">
                      <ul>
                          <li>{{ session('success') }}</li>
                      </ul>
                  </div>
              @endif

              <form class="row g-3" method="post" action="{{ url('/updateVCardInteractionType') }}">
                @csrf

                <div class="col-sm-12">
                  <label for="interactionType" class="form-label">Select Interaction Type</label>
                  <select class="form-select" id="interactionType" name="interactionType" onchange="changeFirstMessageValue(this.value)">
                    <option disabled="" value="--">Choose</option>
                    <option {{ $vcard->connectType == 1 ? "selected" : "" }} value="1"> Exchange Your Card Via Save My V Card Platform</option>
                    <option {{ $vcard->connectType == 2 ? "selected" : "" }}  value="2"> Let the User connect directly with me</option>
                    <option {{ $vcard->connectType == 3 ? "selected" : "" }}  value="3"> User Can Download VCF contact card directly (OFFLINE)</option>
                  </select>
                </div>
 
                <div class="col-sm-12">
                    <label for="firstMessage" class="form-label">Set First Message that will be sent on scanning the QR Code</label>
                    <input type="text" class="form-control" id="firstMessage" name="firstMessage" value="{{ $vcard->firstMessage ?? 'Hi' }}">
                    <input type="hidden" id="vcardName" value="{{ $vcard->salutation_text }}{{ $vcard->firstName }} {{ $vcard->middleName }} {{ $vcard->lastName }}">
                    <input type="hidden" name="vcardid" value="{{ $vcard->vcardid }}">
                </div>
                
                
                
                <div class="col-12">
                  <div class="form-check">
                    <input class="form-check-input" type="checkbox" value="" id="invalidCheck2" required="">
                    <label class="form-check-label" for="invalidCheck2">
                      I agree to the following terms and condition of selecting the interaction type.
                      <ul>
                        <li>Selecting the first option i.e. Exchange Your Card Via Save My V Card Platform may incur additional fee in future depending upon number of requests processed.</li>
                        <li>Selecting the second option i.e. letting the user directly connect with you may trigger several notifications in high interaction environments like Conference, Seminar etc </li>
                        <li>Selecting the thid option will not have any tracking available, as to who and when downloaded your card.</li>
                      </ul>
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
    

  </script>
@endsection