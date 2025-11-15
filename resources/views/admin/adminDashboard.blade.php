@php
  $session = session()->all();
  $count = 1;
@endphp
<style>

  .btn-flip{
    margin: 5px;
  opacity: 1;
  outline: 0;
  color: #305638;
  line-height: 40px;
  position: relative;
  text-align: center;
  letter-spacing: 1px;
  display: inline-block;
  text-decoration: none;
  font-family: 'Open Sans';
  text-transform: uppercase;
  
  &:hover{
    
    &:after{
      opacity: 1;
      transform: translateY(0) rotateX(0);
    }
    
    &:before{
      opacity: 0;
      transform: translateY(50%) rotateX(90deg);
    }
  }
  
  &:after{
    top: 0;
    left: 0;
    opacity: 0;
    width: 100%;
    color: #305638;
    display: block;
    transition: 0.5s;
    position: absolute;
    background: #fff;
    content: attr(data-back);
    transform: translateY(-50%) rotateX(90deg);
    border: 1px solid black;
    border-radius: 5px;
  }
  
  &:before{
    top: 0;
    left: 0;
    opacity: 1;
    color: #fff;
    display: block;
    padding: 0 30px;
    line-height: 40px;
    transition: 0.5s;
    position: relative;
    background: #305638;
    content: attr(data-front);
    transform: translateY(0) rotateX(0);
    border: 1px solid #fff;
    border-radius: 5px;
  }
}
</style>
@extends('layoutAdmin.mainAdmin')
@section('main-section')
  <main id="main" class="main">

    <div class="pagetitle">
      <h1>Admin Dashboard</h1>
      <nav>
        <ol class="breadcrumb">
          <li class="breadcrumb-item"><a href="{{ url('/adminDashboard'); }}">Home</a></li>
          <li class="breadcrumb-item">Admin Dashboard</li>
        </ol>
      </nav>
    </div><!-- End Page Title -->
    <section class="section">
      <div class="row">
        <div class="col-lg-12">

          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Vcard download Summary</h5>
              <table class="table datatable">
                <thead>
                  <tr>
                    <th>Serial No</th>
                    <th>Name of Person</th>
                    <th>Card Code</th>
                    <th>Number of Downloads</th>
                    <th>Last Download at</th>
                  </tr>
                </thead>
                <tbody>
                  @foreach ($data as $row)
                    <tr>
                      <td>{{ $count++ }}</td>
                      <td>{{ $row->salutation_text }} {{ $row->firstName }} {{ $row->middleName }} {{ $row->lastName }}</td>
                      <td>{{ $row->uniqueCode }}</td>
                      <td>{{ $row->download_number }}</td>
                      <td>{{ $row->last_download_at }}</td>
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