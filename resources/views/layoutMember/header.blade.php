<!DOCTYPE HTML>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">

  <title>Save My V Card - Create & Share Digital Business Cards</title>
  <meta name="description" content="Take control of your digital presence with Save My Vcard. Create and share stunning digital business cards instantly. Generate leads and exchange contact details seamlessly via WhatsApp." />
  <meta name="keywords" content="digital business card, contact card creator, virtual business card, online business card, electronic business card, lead generation tool, capture leads online, business networking tool, generate leads from contacts, share business card, WhatsApp contact card, exchange contact details, digital contact sharing, mobile contact card, contact management tool, sales lead management, professional networking">
  <meta content="Md Manzarul Haque - manzar2004@gmail.com" name="author">
  <meta name="robots" content="noindex">
  <meta name="googlebot" content="noindex">

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
 
</head>

<body>

  <!-- ======= Header ======= -->
  <header id="header" class="header fixed-top d-flex align-items-center">

    <div class="d-flex align-items-center justify-content-between">
      <a href="{{ url('/memberDashboard')}}" class="logo d-flex align-items-center">
        <span class="d-none d-lg-block">Save My V Card</span>
      </a>
      <i class="bi bi-list toggle-sidebar-btn"></i>
    </div><!-- End Logo -->

    <!-- <div class="search-bar">
      <form class="search-form d-flex align-items-center" method="POST" action="#">
        <input type="text" name="query" placeholder="Search" title="Enter search keyword">
        <button type="submit" title="Search"><i class="bi bi-search"></i></button>
      </form>
    </div> -->
    
    <!-- End Search Bar -->

    <nav class="header-nav ms-auto">
      <ul class="d-flex align-items-center">

        <li class="nav-item d-block d-lg-none">
          <a class="nav-link nav-icon search-bar-toggle " href="#">
            <i class="bi bi-search"></i>
          </a>
        </li><!-- End Search Icon-->


        <li class="nav-item dropdown pe-3">

          <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
            <img src="{{url('/img/user-icon.png')}}" alt="Profile" class="rounded-circle">
            <span class="d-none d-md-block dropdown-toggle ps-2">{{ $session['mobile'] }}</span>
          </a><!-- End Profile Image Icon -->

          <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
            <li>
              <a class="dropdown-item d-flex align-items-center" href="{{url('/userProfile')}}">
                <i class="bi bi-person"></i>
                <span>{{ $session['username'] }}</span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="{{url('/changePassword')}}">
                <i class="bi bi-gear"></i>
                <span>Password Change</span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>
            <li>
              <a class="dropdown-item d-flex align-items-center" href="#">
                <i class="bi bi-gear"></i>
                <span>Subscription: {{ $session['subscription'] }}</span>
              </a>
            </li>
            <li>
              <hr class="dropdown-divider">
            </li>

            <li>
              <a class="dropdown-item d-flex align-items-center" href="{{url('/logout')}}">
                <i class="bi bi-box-arrow-right"></i>
                <span>Sign Out</span>
              </a>
            </li>

          </ul><!-- End Profile Dropdown Items -->
        </li><!-- End Profile Nav -->

      </ul>
    </nav><!-- End Icons Navigation -->

  </header><!-- End Header -->
