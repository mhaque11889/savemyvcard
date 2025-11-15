@php
  $session = session()->all();
  $count=1;
@endphp
@extends('layoutAdmin.mainAdmin')
@section('main-section')
  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Registered Users</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ url('/adminDashboard'); }}">Back to Home</a></li>
          <li class="breadcrumb-item">Registered Users</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">All Users</h5>
              <table class="table" id="memberTable">
                <thead>
                  <tr><th>S.No</th><th>Mobile Number</th><th>Email Id</th><th>Email Verified?</th><th>Mobile Verified?</th><th>User Creation Date</th><th>Vcard Count</th><th>Login</th></tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                        <td>{{ $count++; }}</td>
                        <td>{{ $user->mobileno; }}</td>
                        <td><a href="{{ url('userProfile/'.$user->id) }}">{{ $user->email }} </a></td>
                        <td>{{ $user->isemailverified == 0 ? "No" : "Yes" }}</td>
                        <td>{{ $user->isMobileVerified == 0 ? "No" : "Yes" }}</td>
                        <td>{{ $user->created_at; }}</td>
                        <td>{{ $user->vcardcount; }}</td>
                        <td><a href="{{ url('loginImpersonate/'.$user->id) }}"><button class="btn btn-primary btn-sm">Click</button></a></td>
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