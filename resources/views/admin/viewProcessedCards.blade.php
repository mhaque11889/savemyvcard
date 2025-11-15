@php
  $session = session()->all();
  $count=1;
@endphp
@extends('layoutAdmin.mainAdmin')
@section('main-section')
  <main id="main" class="main">

    <div class="pagetitle">
      <h1>All Processed Cards</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ url('/adminDashboard'); }}">Back to Home</a></li>
          <li class="breadcrumb-item">Processed Cards</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">All The cards along with the result that has been processed.</h5>
              <table class="table" id="memberTable">
                <thead>
                  <tr><th>S.No</th><th>Sender Number</th><th>Sender Name</th><th>Image</th><th>Processed Data</th><th>Received at</th>
                    {{-- <th>Processing Time</th> --}}
                  </tr>
                </thead>
                <tbody>
                  @foreach ($cards as $index => $card)
                      <tr>
                          <td>{{ $index + 1 }}</td> {{-- Using $index for serial numbering --}}
                          <td>{{ $card->senderPhoneNo }}</td>
                          <td>{{ $card->senderProfileName }}</td>
                          <td>
                              <a href="https://smvcbusinesscards.s3.us-east-1.amazonaws.com/businesscards/{{ $card->media_id }}.jpeg" target="_blank">
                                  <img src="https://smvcbusinesscards.s3.us-east-1.amazonaws.com/businesscards/{{ $card->media_id }}.jpeg" style="height:100px"/>
                              </a>
                          </td>
                          <td>
                              @php
                                  $data = json_decode($card->aiResponse, true); // Decode JSON into array
                              @endphp

                              @if($data)
                                <strong>Name:</strong> {{ is_array($data['Name']) ? implode(', ', $data['Name'] ?? []) : $data['Name'] }} <br>
                                <strong>Job Title:</strong> {{ is_array($data['Job_Title']) ? implode(', ', $data['Job_Title'] ?? []) : $data['Job_Title'] }} <br>
                                <strong>Company Name:</strong> {{ is_array($data['Company_Name']) ? implode(', ', $data['Company_Name'] ?? []) : $data['Company_Name'] }} <br>
                                <strong>Contact:</strong> {{ is_array($data['Contact'] ) ? implode(', ', $data['Contact'] ?? []) : $data['Contact'] }} <br>
                                <strong>Email ID:</strong> {{ is_array($data['Email_ID']) ? implode(', ', $data['Email_ID'] ?? []) : $data['Email_ID'] }} <br>
                                <strong>Address:</strong> {{ is_array($data['Address']) ? implode(', ', $data['Address'] ?? []) : $data['Address'] }} <br>
                                <strong>Website:</strong> {{ is_array($data['Website']) ? implode(',', $data['Website'] ?? []) : $data['Website'] }}
                              @else
                                  <span class="text-danger">Invalid AI response data</span>
                              @endif
                          </td>
                          <td>
                            {{ \Carbon\Carbon::parse($card->created_at)->format('d-M-Y h:i A') ?? null }}<br/><br/>
                            Processed in <br/>
                            @if(!empty($card->processed_at->created_at))
                            {{ \Carbon\Carbon::parse($card->created_at)->diffInSeconds($card->processed_at->created_at) }}  seconds
                            @endif
                          </td>
                          {{-- <td>
                            {{ \Carbon\Carbon::parse($card->created_at)->diff(\Carbon\Carbon::parse($card->updated_at))->format('%H:%I:%S') }}
                          </td> --}}
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