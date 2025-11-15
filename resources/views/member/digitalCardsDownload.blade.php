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
    font-size: 32px;
    font-weight: 700;
}

.whatsappQRCode{
    height: 230px;
    padding-top: 60px;
}

#companyName, #jobtitle {
  font-size: 24px;
}

.whatsappQRCode img{
    height: 180px;
    margin-left: 226px;
    background: #fff;
    padding: 5px;
    border: 1px solid black;
    border-radius: 5px;
}

canvas{
  background-color: white;
  padding: 5px;
}

</style>
<script src="https://cdn.jsdelivr.net/gh/davidshimjs/qrcodejs/qrcode.min.js"></script>
@extends('layoutMember.main')
@section('main-section')
  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Download Digital Cards</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ url('/memberDashboard'); }}">Home</a></li>
          <li class="breadcrumb-item">Download Digital Cards</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Download Digital Card</h5>
              <input type="hidden" value="{{url('/processCard/'.$vcard->uniqueCode);}}" id="qrCodeText"/>
              <table class="digitalCardTable" id="digitalCard">
                <tr><td class="whatsappQRCode"><span id="qrcode"></span></td></tr>
                <tr style="height: 40px"><td class="digitalCardName">{{ $vcard->salutation_text }} {{ $vcard->firstName }} {{ $vcard->middleName }} {{ $vcard->lastName }}</td></tr>
                <tr style="height: 32px"><td id="jobtitle">{{ $vcard->jobTitle }}</td> </tr>
                <tr><td id="companyName" style="vertical-align: baseline">{{ $vcard->companyName }}</td></tr>
              </table>
              <hr/>
              <p>
              <button class="btn btn-primary" id="downloadBtn">Download Card</button>
              </p>
            </div>
          </div>


        </div>

        

      </div>
    </section>

  </main><!-- End #main -->
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
  <script>
    const qrcode = new QRCode(document.getElementById('qrcode'), {
        text: document.getElementById('qrCodeText').value,
        width: 180,
        height: 180,
        colorDark : '#000',
        colorLight : '#fff',
        correctLevel : QRCode.CorrectLevel.H
    });

    document.getElementById("downloadBtn").addEventListener("click", function() {
        var element = document.getElementById("digitalCard");
        html2canvas(element).then(function(canvas) {
            // Create an image from the canvas
            var imgData = canvas.toDataURL("image/png");
            
            // Create a link to download the image
            var link = document.createElement('a');
            link.href = imgData;
            link.download = 'digital_card.png';
            
            // Simulate click to trigger download
            link.click();
        });
    });
  </script>
@endsection