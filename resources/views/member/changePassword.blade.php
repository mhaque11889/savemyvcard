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
      <h1>Change Password</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ url('/memberDashboard'); }}">Home</a></li>
          <li class="breadcrumb-item">Change Password</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
    <section class="section">
      <div class="row">
        <div class="col-lg-6">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Change Password</h5>
              @if ($errors->has('error'))
                  <div class="alert alert-warning">
                      {{ $errors->first('error') }}
                  </div>
              @endif
              @if ($errors->has('message'))
                  <div class="alert alert-success">
                      {{ $errors->first('message') }}
                  </div>
              @endif

              <p style="font-size:12px; color:red">Fields with asterisk (*) sign are mandatory.</p>
              <form class="row g-3" method="post" action="{{ url('/changePassword') }}">
                @csrf
                <div class="col-md-12">
                  <label for="oldPassword" class="form-label">Current Password*</label>
                  <input type="hidden" class="form-control" id="userid" name="userid" value="{{ $session['username']; }}">
                  <input type="text" class="form-control" id="oldPassword" name="oldPassword" required="">
                </div>
                <div class="col-md-12">
                  <label for="newPassword" class="form-label">New Password*</label>
                  <input type="text" class="form-control" id="newPassword" name="newPassword" required="">
                </div>
                <div class="col-md-12">
                  <label for="confirmNewPassword" class="form-label">Confirm New Password*</label>
                  <input type="text" class="form-control" id="confirmNewPassword" name="confirmNewPassword" required="">
                </div>
                <div class="col-12">
                  <button class="btn btn-primary" type="submit">Change</button>
                </div>
              </form>
            </div>
          </div>


        </div>

        

      </div>
    </section>

  </main><!-- End #main -->
@endsection