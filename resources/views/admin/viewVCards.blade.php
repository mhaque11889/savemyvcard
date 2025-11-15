@php
  $session = session()->all();
  $count=1;
@endphp
@extends('layoutAdmin.mainAdmin')
@section('main-section')
  <main id="main" class="main">

    <div class="pagetitle">
      <h1>All VCards</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ url('/adminDashboard'); }}">Back to Home</a></li>
          <li class="breadcrumb-item">All V Cards</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">All VCards Created by Registered Users.</h5>
              <table class="table" id="memberTable">
                <thead>
                  <tr><th>S.No</th><th>VCard Code</th><th>VCard Name</th><th>VCard Number</th><th>Job title</th><th>Company Name</th><th>Status</th> <th>Created at</th> </tr>
                </thead>
                <tbody>
                  @foreach ($cards as $index => $card)
                      <tr>
                          <td>{{ $index + 1 }}</td> {{-- Using $index for serial numbering --}}
                          <td>{{ $card->uniqueCode }} <br/><span style="font-size:10px"><a href="/userProfile/{{ $card->userid }}">User Profile</a></span></td>
                          <td>{{ $card->salutation_text }}{{ $card->firstName }} {{ $card->middleName }} {{ $card->lastName }}</td>
                          <td>{{ $card->mobileNo1 }}</td>
                          <td>{{ $card->jobTitle }}</td>
                          <td>{{ $card->companyName }}</td>
                          <td>{{ $card->status == 1 ? "Verified" : "Unverified" }}</td>
                          <td>
                            {{ \Carbon\Carbon::parse($card->created_at)->format('d-M-Y h:i A') }}
                          </td>
                      </tr>
                  @endforeach
                </tbody>
              </table>
            </div>
          </div>


        </div>

        

      </div>
    </section>
  </main><!-- End #main -->
  <link href="https://cdn.datatables.net/2.1.5/css/dataTables.dataTables.css" rel="stylesheet">
  <link href="https://cdn.datatables.net/buttons/3.1.2/css/buttons.dataTables.css" rel="stylesheet">
  <script src="https://code.jquery.com/jquery-3.7.1.js"></script>
  <script src="https://cdn.datatables.net/2.1.5/js/dataTables.js"></script>
  <script src="https://cdn.datatables.net/buttons/3.1.2/js/dataTables.buttons.js"></script>
  <script src="https://cdn.datatables.net/buttons/3.1.2/js/buttons.dataTables.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/pdfmake.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.2.7/vfs_fonts.js"></script>
  <script src="https://cdn.datatables.net/buttons/3.1.2/js/buttons.html5.min.js"></script>
  <script>
    new DataTable('#memberTable', {
        buttons: [ 'pageLength','copy', 'excel'],
        layout: {
            topStart: 'buttons'
        }
    });
    </script>
@endsection