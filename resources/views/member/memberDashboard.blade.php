@php
  $session = session()->all();
@endphp
<style>

  .btn-flip{
    margin: 5px;
  opacity: 1;
  outline: 0;
  color: #305638;
  line-height: 40px;
  position: relative;
  text-align: center;
  letter-spacing: 1px;
  display: inline-block;
  text-decoration: none;
  font-family: 'Open Sans';
  text-transform: uppercase;
  
  &:hover{
    
    &:after{
      opacity: 1;
      transform: translateY(0) rotateX(0);
    }
    
    &:before{
      opacity: 0;
      transform: translateY(50%) rotateX(90deg);
    }
  }
  
  &:after{
    top: 0;
    left: 0;
    opacity: 0;
    width: 100%;
    color: #305638;
    display: block;
    transition: 0.5s;
    position: absolute;
    background: #fff;
    content: attr(data-back);
    transform: translateY(-50%) rotateX(90deg);
    border: 1px solid black;
    border-radius: 5px;
  }
  
  &:before{
    top: 0;
    left: 0;
    opacity: 1;
    color: #fff;
    display: block;
    padding: 0 30px;
    line-height: 40px;
    transition: 0.5s;
    position: relative;
    background: #305638;
    content: attr(data-front);
    transform: translateY(0) rotateX(0);
    border: 1px solid #fff;
    border-radius: 5px;
  }
}
</style>
@extends('layoutMember.main')
@section('main-section')
  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Dashboard</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ url('/memberDashboard'); }}">Home</a></li>
          <li class="breadcrumb-item">Dashboard</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Dashboard</h5>
              @if (isset($message))
                <div class="alert alert-danger alert-dismissible fade show">
                  {{ $message }}
                </div>
              @endif

              @if(isset($session['password']))
              <div class="alert alert-success bg-success text-light border-0 alert-dismissible fade show" role="alert">
                Your password for this portal is : {{ $session['password'] }} . Please save it at a secure place, If you lose it you will have to reset the password.
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
              @endif

            @if($isMobileVerified == 0 || $isEmailVerified == 0)
            
                <p>Please verify your mobile number and email before proceeding to the next step.</p>
            
            @endif
                
              @if($isMobileVerified == 0)
              <p>Your mobile number is not verified.<button class="btn btn-link" onclick="verifyImage()">Verify Now</button> </p>
              @endif
              <p style="display:none; text-align:center; text-shadow: 1px 1px #034e4c" id="verifyImage">
                <img src="{{url('/img/JXNNJUBYYXUCO1.png')}}" width="200"/>
                <br/>Scan this QR Code using your WhatsApp Camera or any QR Scanner
                <br/>Alternatively, You can <a href="https://wa.me/message/JXNNJUBYYXUCO1?src=qr" target="blank">Click here</a> to send the verification text if you are accesing from your mobile website.
              </p>
              @if($isEmailVerified == 0)
              <p>Your email is not verified. <button class="btn btn-link" onclick="sendVerificationEmail()">Verify Now</button> </p>
              @endif
              <p id="otp" style="display:none">
                Please provide the verification code sent to your email. (Do not refresh this page)<br/>
                @csrf
                <input type="hidden" id="verificationCode1" value=""/>
                <input type="hidden" id="userId" value="{{$session['username']}}"/>
                <input type="text" id="verificationCode2" placehoder="check your email"/> <br/><br/>
                <button type="button" class="btn btn-success" onclick="verifyCode()">Verify</button>
              </p> 

              @if($isMobileVerified == 1 && $isEmailVerified == 1)
              <div class="alert alert-success bg-success text-light border-0 alert-dismissible fade show" role="alert">
                Great. You have verified your Mobile and Email. 
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
              </div>
              <br/>
              Next Steps:-
              <ul>
              <li><a href="{{ url('/vcards') }}" class="btn-flip" data-back="Create Vcards" data-front="Create VCards"></a></li>
              <li><a href="{{ url('/leadNotification') }}" class="btn-flip" data-back="Check Your Leads" data-front="Check Your Leads"></a></li>
              <li><a href="{{ url('/digitalCards') }}" class="btn-flip" data-back="Get Your Digital Cards" data-front="Get Your Digital Cards"></a></li>
              @endif
            </div>
          </div>


        </div>

        

      </div>
    </section>

  </main><!-- End #main -->

  <script>
    function verifyImage()
    {
      $('#verifyImage').css('display', 'block');
    }

    function sendVerificationEmail()
    {
      $('#otp').css('display','');
      var seq = (Math.floor(Math.random() * 10000) + 10000).toString().substring(1);
      $('#verificationCode1').val(seq);
      const CSRF_TOKEN = $('[name="_token"]').val();
      const userId = $('#userId').val();
      $.ajax
      ({
        type:'POST',
        url:'sendOTPEmail',
        data: {_token: CSRF_TOKEN, message: {'otp' : seq,'userId': userId}},
        success:function(resp) 
        {
          console.log('response',resp);
          if(resp.status == "true"){
            //
          }
        }
      });
    }  
    function verifyCode()
    {
      const otp1 = $('#verificationCode1').val();
      const otp2 = $('#verificationCode2').val();

      if(otp1 == otp2)
      {
        const CSRF_TOKEN = $('[name="_token"]').val();
        const userId = $('#userId').val();
        $.ajax
        ({
          type:'POST',
          url:'emailVerified',
          data: {_token: CSRF_TOKEN, message: {'userId': userId}},
          success:function(resp) 
          {
            console.log('response',resp);
            if(resp == "success"){
              //
              window.location.reload();
            }
          }
        });
      }
      else{
        alert("Incorrect Code, Please try again");
      }
    }
  </script>
@endsection