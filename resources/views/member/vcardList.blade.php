@php
  $session = session()->all();
  $count = 1;
@endphp
<style>
  .toast {
    visibility: hidden;
    min-width: 250px;
    margin-left: -125px;
    background-color: #333;
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

.changeColor {
  color: #012970;
  text-align: center;
  animation-name: changeColor;
  animation-duration: 4s;
  animation-iteration-count: infinite;
}

@keyframes changeColor {
  0%   {color:red; }
  50%  {color:blue; }
  100% {color:red; }
}
</style>
@extends('layoutMember.main')
@section('main-section')
  <main id="main" class="main">

    <div class="pagetitle">
      <h1>V Card List</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ url('/memberDashboard'); }}">Home</a></li>
          <li class="breadcrumb-item">V Card List</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">V Card List</h5>
              <div id="toast" class="toast">
                Copied the code to Clipboard
              </div>
              @if (isset($message))
                <div class="alert alert-danger alert-dismissible fade show">
                  {{ $message }}
                </div>
              @endif
              @if ($errors->has('error'))
                  <div class="alert alert-danger">
                      {{ $errors->first('error') }}
                  </div>
              @endif
              <p style="text-align: end"><a href="/addNewVCards"><button type="button" class="btn btn-warning">+ Add New V Card</button></a></p>
              <table class="table datatable">
                <thead>
                <tr><th>S.No</th><th>Number</th><th>Profile Name</th><th>Digital Card</th><th>Status</th><th class="changeColor">Change Interaction type</th> <th>Edit</th></tr>
                </thead>
                <tbody>
                @if (isset($vcards))
                  @foreach ($vcards as $vcard)
                  <tr>
                    <td> {{ $count++}}</td>
                    <td> {{ $vcard->mobileNo1 }}</td>
                    <td> {{ $vcard->salutation_text }} {{ $vcard->firstName }} {{ $vcard->middleName }} {{ $vcard->lastName }}</td>
                    <td><a href="{{ url('/digitalCards/'.$vcard->uniqueCode) }}"> Click here</a> <button onclick="copyClipboard('{{ $vcard->uniqueCode }}')"><i class="bx bxs-copy"></i></button></td>
                    <td> {{ $vcard->status == 1 ? 'Verified' : 'Unverified' }} 
                          @if ($vcard->status == 0)
                            <a target="blank" href="https://wa.me/919007900976?text={{ urlencode('VerifyCard '.$vcard->uniqueCode) }}">Verify Now</a>
                          @endif
                    </td>
                    <td> <a href="{{ url('changeInteractionType/'.$vcard->id) }}">Click Here</a> </td>
                    <td> <a href="{{ url('editDigitalCard/'.$vcard->id) }}">Edit</a> </td>
                  </tr>
                  @endforeach  
                @else
                <tr><td colspan="4"> No data to display</td></tr>
                @endif
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
      var toast = document.getElementById("toast");
      toast.className = "toast show";
      setTimeout(function(){ 
          toast.className = toast.className.replace("show", ""); 
      }, 3000); // Toast will disappear after 3 seconds
    }
  </script>
@endsection