@php
  $session = session()->all();
  $count = 1;
@endphp
<style>

</style>
@extends('layoutAdmin.mainAdmin')
@section('main-section')
  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Failed Messages</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ url('/adminDashboard'); }}">Home</a></li>
          <li class="breadcrumb-item">Failed Messages</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Failed Messages</h5>
              <table class="table datatable">
                <thead>
                  <tr>
                    <th>Serial No</th>
                    <th>Mobile no</th>
                    <th>Email Id</th>
                    <th>Member of SMVC</th>
                    <th>Message Type</th>
                    <th>Error Message</th>
                    <th>Created At</th>
                    <th>Content</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($failedMessages as $row)
                    <tr>
                      <td>{{ $count++; }}</td>
                      <td>{{ $row->sentTo; }}</td>
                      <td>{{ is_null($row->email) ? $row->downloader_name : $row->email }}</td>
                      <td>{{ is_null($row->email) ? "No" : "Yes" }}</td>
                      <td>{{ $row->messageType }}</td>
                      <td>{{ $row->errorMessage }}</td>
                      <td>{{ $row->created_at }}</td>
                      <td>
                        @php
                          if($row->messageType == 'ContactCard')
                          {
                              // Decode JSON into an associative array
                              $data = json_decode($row->contents, true);

                              // Extract formatted_name
                              $formattedName = $data['name']['formatted_name'] ?? null;

                              // Extract wa_id (if multiple, collect all)
                              $waIds = array_column($data['phones'], 'wa_id');

                              // Extract email (if any)
                              $emails = array_column($data['emails'], 'email');

                              // Output the results
                              echo "Name: " . ($formattedName ?? 'N/A') . "<br>";
                              echo "Number: " . implode(', ', $waIds) . "<br>";
                              echo "Emails: " . (count($emails) > 0 ? implode(', ', $emails) : 'N/A') . "<br>";
                          }
                          else{
                            echo $row->contents;
                          }
                        @endphp
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
@endsection