@php
  $session = session()->all();
  $count=1;
@endphp

@extends('layoutMember.main')
@section('main-section')
  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Lead Notification</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ url('/memberDashboard'); }}">Home</a></li>
          <li class="breadcrumb-item">Lead Notification</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Lead Notifications</h5>
              <table class="table" id="memberTable">
                <thead>
                  <tr><th>S.No</th><th>VCard Name</th><th>Downloader Name</th><th>Downloader Phone Number</th><th>Message Received</th><th>TimeStamp</th><th>Status</th></tr>
                </thead>
                <tbody>
                  @foreach ($vcardList  as $list)
                    <tr><td>{{ $count++ }}</td><td>{{ $list->firstName }}</td><td>{{ $list->downloader_name }}</td><td>{{ $list->downloader_no }}</td><td>{{ $list->message_received }}</td><td>{{ $list->created_at }}</td><td>{{ is_null($list->isError) && $list->deliveredAt > '17' ? 'Delivered' : 'Not Delivered' }}</td></tr>                    
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