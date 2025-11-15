@php
  $session = session()->all();
  $count = 1;
  $serial_no = 1;
@endphp
<style>
  .toast {
    visibility: hidden;
    min-width: 250px;
    margin-left: -125px;
    background-color: rgb(255, 64, 0) !important;
    color: #000000;
    text-align: center;
    border-radius: 5px;
    padding: 16px;
    position: fixed;
    z-index: 1;
    left: 50%;
    bottom: 30px;
    font-size: 17px;
    opacity: 0;
    transition: opacity 0.5s, visibility 0.5s;
}

  .toast.show {
    visibility: visible;
    opacity: 1;
}
</style>
@extends('layoutAdmin.mainAdmin')
@section('main-section')
  <main id="main" class="main">

    <div class="pagetitle">
      <h1>User Profile</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ url('/adminDashboard'); }}">Back to Home</a></li>
          <li class="breadcrumb-item">Registered Users</li>
          <li class="breadcrumb-item">User Profile</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
    <section class="section profile">
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
      @if (session('status'))
          <div class="alert alert-success mt-5">
              {{ session('status') }}
          </div>
      @endif
      <div class="row">
        <div class="col-xl-12">

          <div class="card">
            <div class="card-body pt-3">
              <div id="toast" class="toast">
                Copied the code to Clipboard
              </div>
              <!-- Bordered Tabs -->
              <ul class="nav nav-tabs nav-tabs-bordered">

                <li class="nav-item">
                  <button class="nav-link active" data-bs-toggle="tab" data-bs-target="#profile-overview">Registration Details</button>
                </li>

                <li class="nav-item">
                  <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-vcards">VCards</button>
                </li>

                <li class="nav-item">
                  <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-vcards-download">Card Downloads</button>
                </li>

                <li class="nav-item">
                  <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-change-subscription">Subscription</button>
                </li>

                <li class="nav-item">
                  <button class="nav-link" data-bs-toggle="tab" data-bs-target="#profile-change-password">Change Password</button>
                </li>

              </ul>
              <div class="tab-content pt-2">

                <div class="tab-pane fade show active profile-overview" id="profile-overview">
                  
                  <h5 class="card-title">Profile Details</h5>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label ">Email Address:</div>
                    <div class="col-lg-9 col-md-8">{{ $user->email }}</div>
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Mobile Number</div>
                    <div class="col-lg-9 col-md-8">{{ $user->mobileno }}</div>
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Email Verified</div>
                    <div class="col-lg-9 col-md-8">
                      {{ $user->isemailverified == 1 ? "Yes" : "No" }} 
                      @if($user->isemailverified == 0)
                        <a href="{{ url('/verifyEmailByAdmin/'.$user->id) }}"><button class="btn btn-secondary btn-sm">Verify Email</button></a>                   
                      @endif
                    </div>
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Mobile Verified</div>
                    <div class="col-lg-9 col-md-8">
                      {{ $user->isMobileVerified == 1 ? "Yes" : "No" }} 
                      @if($user->isMobileVerified == 0)
                        <a href="{{ url('/verifyMobileByAdmin/'.$user->id) }}"><button class="btn btn-secondary btn-sm">Verify Mobile Number</button></a>                   
                      @endif

                    </div>
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">User Created </div>
                    <div class="col-lg-9 col-md-8">{{ $user->created_at }}</div>
                  </div>

                  <div class="row">
                    <div class="col-lg-3 col-md-4 label">Last Login</div>
                    <div class="col-lg-9 col-md-8">{{ $user->last_login }}</div>
                  </div>

                </div>

                <div class="tab-pane fade profile-edit pt-3" id="profile-vcards">

                  <h5 class="card-title">Vcard Created By User</h5>

                  <div class="row">
                    <div class="col-lg-12">
                      <table class="datatable">
                        <thead>
                          <tr>
                            <th>S.No</th>
                            <th>Name On Card</th>
                            <th>Card Unique Code</th>
                            <th>Status</th>
                            <th>Verify Now</th>
                            <th>Alternate Code</th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach ($vcards as $vcard)
                            <tr>
                              <td>{{ $count++ }}</td>
                              <td>{{ $vcard->salutation_text }} {{ $vcard->firstName }} {{ $vcard->middleName }} {{ $vcard->lastName }}</td>
                              <td>{{ $vcard->uniqueCode }} <button onclick="copyClipboard('{{ $vcard->uniqueCode }}')"><i class="bx bxs-copy"></i></button></td>
                              <td>{{ $vcard->status == 1 ? "Verfied" : "Unverified"}}</td>
                              <td>
                                @if($vcard->status == 0)
                                 <a href="{{ url('/verifyVcardByAdmin/'.$vcard->id) }}"><button class="btn btn-secondary btn-sm">Verify Card</button></a>
                                @endif
                              </td>
                              <td>
                                  @csrf
                                  <input type="hidden" name="vcardid" value="{{ $vcard->id }}"/>
                                  <button type="button" class="btn btn-secondary btn-sm" onclick="showQRCodeUpdateDiv({{ $vcard->id }})">Add/Update Alternate Codes</button>
                              </td>
                            </tr>
                          @endforeach
                        </tbody>
                      </table>
                    </div>
                  </div>


                </div>

                <div class="tab-pane fade pt-3" id="profile-vcards-download">

                  <div class="row">
                    <div class="col-lg-12">
                      <table class="datatable">
                        <thead>
                          <tr>
                            <th>S.No</th>
                            <th>Name On Card</th>
                            <th>Card Unique Code</th>
                            <th>Downloader Name</th>
                            <th>Downloader Number</th>
                            <th>Request Received At</th>
                            <th>Card Delivered At</th>
                            <th>Error?</th>
                          </tr>
                        </thead>
                        <tbody>
                          @foreach ($downloads as $row)
                            <tr>
                              <td>{{ $serial_no++; }}</td>
                              <td>{{ $row->salutation_text }} {{ $row->firstName }} {{ $row->middleName }} {{ $row->lastName }}</td>
                              <td>{{ $row->uniqueCode }} </td>
                              <td>{{ $row->downloader_name}}</td>
                              <td>{{ $row->downloader_no }} </td>
                              <td>{{ $row->created_at }}</td>
                              <td>
                                @php
                                if(is_null($row->deliveredAt))
                                {
                                  echo "-";
                                }
                                else
                                {
                                  $timestamp = $row->deliveredAt;
                                  $date = new DateTime("@$timestamp");
                                  $date->setTimezone(new DateTimeZone('Asia/Kolkata'));
                                  echo $date->format('Y-m-d H:i:s');
                                }
                                @endphp 
                              </td>
                              <td>{{ $row->isError }}</td>
                            </tr>
                          @endforeach
                        </tbody>
                      </table>
                    </div>
                  </div>

                </div>

                <div class="tab-pane fade pt-3" id="profile-change-password">
                  <!-- Change Password Form -->
                  <form>

                    <div class="row mb-3">
                      <label for="currentPassword" class="col-md-4 col-lg-3 col-form-label">Current Password</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="password" type="password" class="form-control" id="currentPassword">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="newPassword" class="col-md-4 col-lg-3 col-form-label">New Password</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="newpassword" type="password" class="form-control" id="newPassword">
                      </div>
                    </div>

                    <div class="row mb-3">
                      <label for="renewPassword" class="col-md-4 col-lg-3 col-form-label">Re-enter New Password</label>
                      <div class="col-md-8 col-lg-9">
                        <input name="renewpassword" type="password" class="form-control" id="renewPassword">
                      </div>
                    </div>

                    <div class="text-center">
                      <button type="submit" class="btn btn-primary">Change Password</button>
                    </div>
                  </form><!-- End Change Password Form -->

                </div>

              </div><!-- End Bordered Tabs -->

            </div>
          </div>

        </div>

        <div class="col-xl-12" id="addAlternateCodes" style="display: none">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title" id="alternateCardTitle">#</h5>
              <table class="table">
                <thead>
                  <tr>
                    <th>S.No</th>
                    <th>Type</th>
                    <th>Add/Update Code</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td scope="row">1.</td>
                    <td>Alternate QR Code:</td>
                    <td>
                      <input type="hidden" name="vcardid" id="vcardidAlternateCodes" value=""/>
                      <input type="text" name="alternateCode" id="alternateCode" style="width:140px" maxlength="12"> 
                      <button type="button" class="btn btn-secondary btn-sm" onclick="updateAlternateCard()">Add/Update Code</button> 
                    </td>
                  </tr>
                  <tr>
                    <td scope="row">2.</td>
                    <td>Mobile Sticker Code:</td>
                    <td>
                      <input type="text" name="mobileStickerCode" id="mobileStickerCode" style="width:140px" maxlength="12"> 
                      <button type="button" class="btn btn-warning btn-sm" onclick="updateMobileStickerCode()">Add/Update Code</button> 
                    </td>
                  </tr>
                  <tr>
                    <td scope="row">3.</td>
                    <td>Tent Card Code:</td>
                    <td>
                      <input type="text" name="tentCardCode" id="tentCardCode" style="width:140px" maxlength="12"> 
                      <button type="button" class="btn btn-primary btn-sm" onclick="updateTentCardCode()">Add/Update Code</button> 
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>

        </div>
      </div>
    </section>

  </main><!-- End #main -->
  <script>
    function copyClipboard(text)
    {
      navigator.clipboard.writeText(text);
      showToast("Copied the code to Clipboard");
    }
  </script>
@endsection