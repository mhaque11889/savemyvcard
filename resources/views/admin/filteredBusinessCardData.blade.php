@php
  $session = session()->all();
  $count=1;
@endphp
@extends('layoutAdmin.mainAdmin')
@section('main-section')
  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Business Card Data</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ url('/adminDashboard'); }}">Back to Home</a></li>
          <li class="breadcrumb-item">Business Card Data</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Business Card Data.</h5>
              <table class="table" id="memberTable">
                <thead>
                  <tr><th>S.No</th><th>Name</th><th>JobTitle</th><th>Company</th><th>Contact No 1</th><th>Contact No 2</th><th>Contact No 3</th> <th>Contact No 4</th><th>Contact No 5</th><th>Email ID 1</th><th>Email Id 2</th><th>Website 1</th><th>Website 2</th><th>Address</th><th>Creation Date</th> </tr>
                </thead>
                <tbody>
                  @foreach ($extractedCards as $index=>$card)
                      <tr>
                        <td>{{ $index+1 }} </td>
                        <td>{{ $card->cardName }} </td>
                        <td>{{ $card->jobTitle }} </td>
                        <td>{{ $card->companyName }} </td>
                        <td>{{ $card->contactNo1 }} </td>
                        <td>{{ $card->contactNo2 }} </td>
                        <td>{{ $card->contactNo3 }} </td>
                        <td>{{ $card->contactNo4 }} </td>
                        <td>{{ $card->contactNo5 }} </td>
                        <td>{{ $card->email1 }} </td>
                        <td>{{ $card->email2 }} </td>
                        <td>{{ $card->website1 }} </td>
                        <td>{{ $card->website2 }} </td>
                        <td>{{ $card->address }} </td>
                        <td>{{ $card->created_at }} </td>
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