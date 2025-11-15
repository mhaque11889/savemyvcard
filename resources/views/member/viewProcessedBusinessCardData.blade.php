@php
  $session = session()->all();
  $count=1;
@endphp
@extends('layoutMember.main')
@section('main-section')
  <main id="main" class="main">

    <div class="pagetitle">
      <h1>All Processed Business Cards</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ url('/memberDashboard'); }}">Back to Home</a></li>
          <li class="breadcrumb-item">Processed Business Cards Data</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
    
    <section class="section">
      <div class="row">
        <div class="col-lg-12">
          <!-- Date Filter Card -->
          <div class="card mb-3">
            <div class="card-body">
              <h5 class="card-title">Filter by Date Range</h5>
              <form method="GET" action="{{ url('/viewProcessedBusinessCardData') }}">
                <div class="row align-items-end">
                  <div class="col-md-4">
                    <label for="start_date" class="form-label">Start Date</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" 
                           value="{{ request('start_date') }}">
                  </div>
                  <div class="col-md-4">
                    <label for="end_date" class="form-label">End Date</label>
                    <input type="date" class="form-control" id="end_date" name="end_date" 
                           value="{{ request('end_date') }}">
                  </div>
                  <div class="col-md-4">
                    <button type="submit" class="btn btn-primary me-2">
                      <i class="bi bi-funnel"></i> Filter
                    </button>
                    <a href="{{ url('/viewProcessedBusinessCardData') }}" class="btn btn-secondary">
                      <i class="bi bi-x-circle"></i> Clear
                    </a>
                  </div>
                </div>
              </form>
            </div>
          </div>
          
          <!-- Data Table Card -->
          <div class="card">
            <div class="card-body">
              <div class="d-flex justify-content-between align-items-center">
                <h5 class="card-title">
                  All The cards along with the result that has been processed.
                  @if(request('start_date') || request('end_date'))
                    <small class="text-muted">
                      (Filtered: {{ request('start_date') ? \Carbon\Carbon::parse(request('start_date'))->format('M d, Y') : 'All' }} 
                      to {{ request('end_date') ? \Carbon\Carbon::parse(request('end_date'))->format('M d, Y') : 'All' }})
                    </small>
                  @endif
                </h5>
                <span class="badge bg-info">Total Records: {{ count($cards) }}</span>
              </div>
              <table class="table" id="memberTable">
                <thead>
                  <tr>
                    <th>S.No</th>
                    <th>Image</th>
                    <th>Sender Name</th>
                    <th>Sender Number</th>
                    <th>Name</th>
                    <th>Job Title</th>
                    <th>Company</th>
                    <th>Contact No 1</th>
                    <th>Contact No 2</th>
                    <th>Contact No 3</th>
                    <th>Contact No 4</th>
                    <th>Contact No 5</th>
                    <th>Email ID 1</th>
                    <th>Email Id 2</th>
                    <th>Website 1</th>
                    <th>Website 2</th>
                    <th>Address</th>
                    <th>Creation Date</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($cards as $index => $card)
                      @php
                          $data = json_decode($card->aiResponse, true);
                          
                          // Extract and normalize data from AI response
                          $name = '';
                          $jobTitle = '';
                          $companyName = '';
                          $contacts = ['', '', '', '', ''];
                          $emails = ['', ''];
                          $websites = ['', ''];
                          $address = '';
                          
                          if($data) {
                              // Handle Name
                              $name = is_array($data['Name'] ?? null) ? implode(', ', $data['Name']) : ($data['Name'] ?? '');
                              
                              // Handle Job Title
                              $jobTitle = is_array($data['Job_Title'] ?? null) ? implode(', ', $data['Job_Title']) : ($data['Job_Title'] ?? '');
                              
                              // Handle Company Name
                              $companyName = is_array($data['Company_Name'] ?? null) ? implode(', ', $data['Company_Name']) : ($data['Company_Name'] ?? '');
                              
                              // Handle Contacts - split multiple contacts
                              $contactData = is_array($data['Contact'] ?? null) ? $data['Contact'] : [($data['Contact'] ?? '')];
                              $allContacts = [];
                              foreach($contactData as $contact) {
                                  if(is_string($contact) && $contact) {
                                      // Split by comma or other delimiters and clean up
                                      $splitContacts = preg_split('/[,\s]+/', $contact);
                                      foreach($splitContacts as $c) {
                                          $c = trim($c);
                                          if($c && strlen($c) > 5) { // Basic validation for phone numbers
                                              $allContacts[] = $c;
                                          }
                                      }
                                  }
                              }
                              // Fill up to 5 contact slots
                              for($i = 0; $i < min(5, count($allContacts)); $i++) {
                                  $contacts[$i] = $allContacts[$i];
                              }
                              
                              // Handle Emails
                              $emailData = is_array($data['Email_ID'] ?? null) ? $data['Email_ID'] : [($data['Email_ID'] ?? '')];
                              $allEmails = [];
                              foreach($emailData as $email) {
                                  if(is_string($email) && $email) {
                                      $splitEmails = preg_split('/[,\s]+/', $email);
                                      foreach($splitEmails as $e) {
                                          $e = trim($e);
                                          if($e && filter_var($e, FILTER_VALIDATE_EMAIL)) {
                                              $allEmails[] = $e;
                                          }
                                      }
                                  }
                              }

                              for($i = 0; $i < min(2, count($allEmails)); $i++) {
                                  $emails[$i] = $allEmails[$i];
                              }
                              
                              // Handle Websites
                              $websiteData = is_array($data['Website'] ?? null) ? $data['Website'] : [($data['Website'] ?? '')];
                              $allWebsites = [];
                              foreach($websiteData as $website) {
                                  if(is_string($website) && $website) {
                                      $splitWebsites = preg_split('/[,\s]+/', $website);
                                      foreach($splitWebsites as $w) {
                                          $w = trim($w);
                                          if($w) {
                                              $allWebsites[] = $w;
                                          }
                                      }
                                  }
                              }
                              for($i = 0; $i < min(2, count($allWebsites)); $i++) {
                                  $websites[$i] = $allWebsites[$i];
                              }
                              
                              // Handle Address
                              $address = is_array($data['Address'] ?? null) ? implode(', ', $data['Address']) : ($data['Address'] ?? '');
                          }
                      @endphp
                      <tr>
                          <td>{{ $index + 1 }}</td>
                          <td>
                              <a href="https://smvcbusinesscards.s3.us-east-1.amazonaws.com/businesscards/{{ $card->mediaURL ?? '' }}.jpeg" target="_blank">
                                  <img src="https://smvcbusinesscards.s3.us-east-1.amazonaws.com/businesscards/{{ $card->mediaURL ?? '' }}.jpeg" style="max-height:100px;max-width:150px"/>
                              </a>
                          </td>
                          <td>{{ $card->senderProfileName ?? '' }}</td>
                          <td>{{ $card->senderPhoneNo ?? '' }}</td>
                          <td>{{ $name }}</td>
                          <td>{{ $jobTitle }}</td>
                          <td>{{ $companyName }}</td>
                          <td>{{ $contacts[0] }}</td>
                          <td>{{ $contacts[1] }}</td>
                          <td>{{ $contacts[2] }}</td>
                          <td>{{ $contacts[3] }}</td>
                          <td>{{ $contacts[4] }}</td>
                          <td>{{ $emails[0] }}</td>
                          <td>{{ $emails[1] }}</td>
                          <td>{{ $websites[0] }}</td>
                          <td>{{ $websites[1] }}</td>
                          <td>{{ $address }}</td>
                          
                          <td>{{ \Carbon\Carbon::parse($card->created_at)->format('M d, Y h:i A') }}</td>
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
    // Initialize DataTable
    new DataTable('#memberTable', {
        buttons: [ 'pageLength','copy', 'excel'],
        layout: {
            topStart: 'buttons'
        }
    });
    
    // Date filter validation
    document.addEventListener('DOMContentLoaded', function() {
        const startDateInput = document.getElementById('start_date');
        const endDateInput = document.getElementById('end_date');
        const filterForm = document.querySelector('form');
        
        // Set max date to today for both inputs
        const today = new Date().toISOString().split('T')[0];
        startDateInput.setAttribute('max', today);
        endDateInput.setAttribute('max', today);
        
        // Update end date min when start date changes
        startDateInput.addEventListener('change', function() {
            if (this.value) {
                endDateInput.setAttribute('min', this.value);
            } else {
                endDateInput.removeAttribute('min');
            }
        });
        
        // Update start date max when end date changes
        endDateInput.addEventListener('change', function() {
            if (this.value) {
                startDateInput.setAttribute('max', this.value);
            } else {
                startDateInput.setAttribute('max', today);
            }
        });
        
        // Form validation
        filterForm.addEventListener('submit', function(e) {
            const startDate = startDateInput.value;
            const endDate = endDateInput.value;
            
            if (startDate && endDate && startDate > endDate) {
                e.preventDefault();
                alert('Start date cannot be after end date!');
                return false;
            }
            
            if (!startDate && !endDate) {
                e.preventDefault();
                alert('Please select at least one date to filter!');
                return false;
            }
        });
    });
  </script>
@endsection