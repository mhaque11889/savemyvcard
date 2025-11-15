<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Save My V Card</title>
  <meta content="" name="description">
  <meta content="" name="keywords">

  
  <!-- Favicons -->
  <link href="{{url('/img/favicon.png')}}" rel="icon">
  <link href="{{url('/img/favicon.png')}}" rel="apple-touch-icon">

  <!-- Google Fonts -->
  <link href="https://fonts.gstatic.com" rel="preconnect">
  <link href="https://fonts.googleapis.com/css?family=Open+Sans:300,300i,400,400i,600,600i,700,700i|Nunito:300,300i,400,400i,600,600i,700,700i|Poppins:300,300i,400,400i,500,500i,600,600i,700,700i" rel="stylesheet">

  

  <!-- Vendor CSS Files -->
  <link href="{{url('/vendor/bootstrap/css/bootstrap.min.css')}}" rel="stylesheet">
  <link href="{{url('/vendor/bootstrap-icons/bootstrap-icons.css')}}" rel="stylesheet">
  <link href="{{url('/vendor/boxicons/css/boxicons.min.css')}}" rel="stylesheet">
  <link href="{{url('/vendor/quill/quill.snow.css')}}" rel="stylesheet">
  <link href="{{url('/vendor/quill/quill.bubble.css')}}" rel="stylesheet">
  <link href="{{url('/vendor/remixicon/remixicon.css')}}" rel="stylesheet">
  <link href="{{url('/vendor/simple-datatables/style.css')}}" rel="stylesheet">
  <!-- Template Main CSS File -->
  <link href="{{url('/css/style.css')}}" rel="stylesheet">
  <style>
    .abcRioButtonLightBlue{
      width: 100% !important;
    }
    .image-rings .ring_0 {
        -webkit-animation: ring_1 2s 0s ease-out infinite;
        animation: ring_1 2s 0s ease-out infinite;
    }
    .image-rings .ring_1 {
        -webkit-animation: ring_1 2s .3s ease-out infinite;
        animation: ring_1 2s .3s ease-out infinite;
    }
    .image-rings .ring_2 {
        -webkit-animation: ring_1 2s .6s ease-out infinite;
        animation: ring_1 2s .6s ease-out infinite;
    }
    .image-rings .ring_3 {
        -webkit-animation: ring_1 2s .9s ease-out infinite;
        animation: ring_1 2s .9s ease-out infinite;
    }
    .image-rings {
        max-width: 597.69px;
        width: 100%;
        height: 100%;
    }
    .image-rings .ring {
        position: absolute;
        margin: auto;
        display: block;
        border-radius: 100%;
        width: 478px;
        height: 478px;
        -webkit-transform: scale(.1, .1);
        -ms-transform: scale(.1, .1);
        transform: scale(.1, .1);
        border: 0.829861px solid #6FD943;
        z-index: -1;
    }
    @-webkit-keyframes ring_1 {
        0% {
            -webkit-transform: scale(.1, .1);
            transform: scale(.1, .1);
            opacity: 1;
        }

        50% {
            opacity: 0.8;
        }

        80% {
            opacity: 0.5;
        }


        100% {
            -webkit-transform: scale(1, 1);
            transform: scale(1, 1);
            opacity: 0;
        }
    }

    @keyframes ring_1 {
        0% {
            -webkit-transform: scale(.1, .1);
            transform: scale(.1, .1);
            opacity: 1;
        }

        50% {
            opacity: 0.8;
        }

        80% {
            opacity: 0.5;
        }

        100% {
            -webkit-transform: scale(1, 1);
            transform: scale(1, 1);
            opacity: 0;
        }
    }
    </style>
</head>

<body style="background: #022332;">

  <main>
    <div class="container">

      <section class="section register min-vh-100 d-flex flex-column justify-content-center py-4">
        <div class="container">
            @if (strlen($error) > 2)
                <div class="alert alert-danger alert-dismissible fade show">
                    {{ $error }}
                </div>
            @endif

          <div class="row">
            <div class="col-lg-12 col-md-12 d-flex flex-column align-items-center justify-content-center">
              <div class="card mb-3">
                <div class="card-body">
                    <div class="pt-4 pb-2">
                        <h5 class="card-title text-center pb-0 fs-4">Save My V Card<br/>Admin Login</h5>
                        <p class="text-center small">Enter your username & password to login</p>
                    </div>
                    <form class="row g-3 needs-validation" novalidate method="POST" action="{{url('/loginAdmin')}}" id="adminLogin">
                        @csrf
                        <div class="col-12">
                            <label for="yourUsername" class="form-label">Username</label>
                            <div class="input-group has-validation">
                                <input type="text" name="username" class="form-control" id="yourUsername" required>
                                <div class="invalid-feedback">Please enter your admin username.</div>
                            </div>
                        </div>

                        <div class="col-12">
                            <label for="yourPassword" class="form-label">Password</label>
                            <input type="password" name="password" class="form-control" id="yourPassword" required>
                            <div class="invalid-feedback">Please enter your password!</div>
                        </div>

                        <div class="col-12">
                            <button class="btn btn-primary w-100" type="submit">Login</button>
                        </div>
                    </form>
                </div>
              </div>
            </div>
          </div>         
        </div>

      </section>

    </div>
  </main><!-- End #main -->

  <a href="#" class="back-to-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>
  <!-- Vendor JS Files -->
  <script src="{{url('/vendor/apexcharts/apexcharts.min.js')}}"></script>
  <script src="{{url('/vendor/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
  <script src="{{url('/vendor/chart.js/chart.min.js')}}"></script>
  <script src="{{url('/vendor/echarts/echarts.min.js')}}"></script>
  <script src="{{url('/vendor/quill/quill.min.js')}}"></script>
  <script src="{{url('/vendor/simple-datatables/simple-datatables.js')}}"></script>
  <script src="{{url('/vendor/tinymce/tinymce.min.js')}}"></script>
  <script src="{{url('/vendor/php-email-form/validate.js')}}"></script>

  
  <script src="https://code.jquery.com/jquery-3.6.0.js" integrity="sha256-H+K7U5CnXl1h5ywQfKtSj8PCmoN9aaq30gDh27Xc0jk=" crossorigin="anonymous"></script>

  <!-- Template Main JS File -->
  <script src="{{url('/js/main.js')}}"></script>
  <script>
   
  </script> 
  
</body>

</html>