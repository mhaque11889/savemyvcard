@php
  $session = session()->all();
@endphp

<style>
.digitalCardTable{
    width: 634px;
    height: 400px;
    text-align: center;
    background: black;
    color: white;
    border-radius: 10px;
}

.digitalCardName {
    width: 400px;
    font-size: 24px;
}

.whatsappQRCode{
    width: 233px;
}


.whatsappQRCode img{
    height: 200px;
    background: #fff;
}
</style>
@extends('layoutMember.main')
@section('main-section')
  <main id="main" class="main">

    <div class="pagetitle">
      <h1>API Integrations</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ url('/memberDashboard'); }}">Home</a></li>
          <li class="breadcrumb-item">APIs</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Get the leads in your own CRM (or Google Sheets)</h5>
              
              <p>URL : https://savemyvcard.com/api/v1/getLeads</p>
              <p>Method: POST</p>
              <p>Body: <br/>
                <code>
                <pre>
                {
                  "token" : "{{ $newToken }}"
                }
                </pre>
                </code>
              </p>
              <p>The token will refresh if you change your password. </p>
              <p>API Response Format<br/>

                <ul>
                  <li>status (string) : possible values 'success' or 'failed'</li>
                  <li>data (array) : An array of objects</li>
                  <ol>
                    <li>cardFirstName (string): The first name of the individual associated with the card.</li>
                    <li>cardMobileNumber (string): The mobile number of the individual associated with the card (international format).</li>
                    <li>downloader_name (string): The name of the person who downloaded or accessed the information.</li>
                    <li>downloader_no (string): The mobile number of the person who downloaded or accessed the information (international format).</li>
                    <li>message_received (string or null): A message or information received from the downloader. If no message is received, this field will be null.</li>
                    <li>timestamp (integer): The Unix timestamp indicating when the event or action occurred</li>
                  </ol>
                  <li>message (string) : Only if status is failed</li>
                </ul>
              </p>
            </div>
          </div>


        </div>

        

      </div>
    </section>

  </main><!-- End #main -->
@endsection