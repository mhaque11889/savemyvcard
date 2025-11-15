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
}

#companyName, #jobtitle {
  font-size: 28px;
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
      <h1>Digital Cards</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ url('/memberDashboard'); }}">Home</a></li>
          <li class="breadcrumb-item">Digital Cards</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Download Digital Card</h5>
              <ul>
              @foreach ($vcards as $card)
                <li style="margin-bottom: 10px"><a href="{{ url('/digitalCards/'.$card->uniqueCode) }}"><button type="button" class="btn btn-warning col-sm-4">{{ $card->salutation_text }} {{ $card->firstName }} {{ $card->middleName }} {{ $card->lastName }}</button></a></li>
              @endforeach
              </ul>
              
            </div>
          </div>


        </div>

        

      </div>
    </section>

  </main><!-- End #main -->
@endsection