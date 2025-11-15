@php
  $session = session()->all();
  $count = 1;
@endphp
@extends('layoutAdmin.mainAdmin')
@section('main-section')
  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Download Business Card Data</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ url('/adminDashboard'); }}">Home</a></li>
          <li class="breadcrumb-item">Admin Dashboard</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
    <section class="section">
      <div class="row">
        <div class="col-lg-12">
            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{$error}} </li>                
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Select Date Range</h5>
              <form action="{{ route('filterBusinessCardData') }}" method="POST">
                @csrf
                <div class="row">
                    <div class="col-md-6 col-sm-12">
                        <label for="startDate" class="form-label">Start Date</label>
                        <input type="date" class="form-control" id="startDate" name="startDate">
                    </div>
                    <div class="col-md-6 col-sm-12">
                        <label for="endDate" class="form-label">End Date</label>
                        <input type="date" class="form-control" id="endDate" name="endDate">
                    </div>
                    <div class="col-12" style="margin-top: 10px">
                        <button type="submit" class="btn btn-sm btn-secondary">Fetch Data</button>
                    </div>
                </div>
                
              </form>
            </div>
          </div>


        </div>

        

      </div>
    </section>

  </main><!-- End #main -->
@endsection