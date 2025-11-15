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
      <h1>Page Under Development</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ url('/memberDashboard'); }}">Home</a></li>
          <li class="breadcrumb-item">Under Development</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Currently Under Development. Stay Tuned!!!</h5>
            </div>
          </div>


        </div>

        

      </div>
    </section>

  </main><!-- End #main -->
@endsection