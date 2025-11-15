@php
  $session = session()->all();
@endphp

@extends('layoutMember.main')
@section('main-section')
  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Dashboard</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="index.html">Home</a></li>
          <li class="breadcrumb-item">Dashboard</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">LifeLong Learning Community</h5>
              <p>Get started by <a href="https://llc.aes.ac.in/courses">selecting the course.</a></p>
            </div>
          </div>


        </div>

        

      </div>
    </section>

  </main><!-- End #main -->
@endsection