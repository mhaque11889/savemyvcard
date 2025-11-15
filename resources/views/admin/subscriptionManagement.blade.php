@php
  $session = session()->all();
  $count=1;
@endphp

@extends('layoutAdmin.mainAdmin')
@section('main-section')
<main id="main" class="main">
    <div class="pagetitle">
        <h1>Subscription Management</h1>
        <nav>
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ url('/adminDashboard') }}">Home</a></li>
                <li class="breadcrumb-item active">Subscription Management</li>
            </ol>
        </nav>
    </div><!-- End Page Title -->

    <section class="section">
        <div class="row">
            <div class="col-lg-12">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="card-title">Manage Subscriptions</h5>
                            <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addSubscriptionModal">
                                <i class="bi bi-plus-circle"></i> Add New Subscription
                            </button>
                        </div>

                        @if(session('success'))
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                {{ session('success') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        @if(session('error'))
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                {{ session('error') }}
                                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                            </div>
                        @endif

                        <table class="table table-striped datatable">
                            <thead>
                                <tr>
                                    <th>Serial No</th>
                                    <th>Name</th>
                                    <th>Price</th>
                                    <th>Duration (Days)</th>
                                    <th>Max VCards</th>
                                    <th>Scans Allowed</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($subscriptions as $subscription)
                                <tr>
                                    <td>{{ $count++ }}</td>
                                    <td>{{ $subscription->subscriptionName }}</td>
                                    <td>₹{{ number_format($subscription->price, 2) }}</td>
                                    <td>{{ $subscription->daysAllowed }}</td>
                                    <td>{{ $subscription->vcardAllowed }}</td>
                                    <td>{{ $subscription->scansAllowed ?? 0 }}</td>
                                    <td>
                                        <span class="badge bg-{{ $subscription->isActive ? 'success' : 'danger' }}">
                                            {{ $subscription->isActive ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                    <td>{{ $subscription->created_at ? $subscription->created_at->format('M d, Y') : 'N/A' }}</td>
                                    <td>
                                        <button class="btn btn-sm btn-info edit-subscription-btn" 
                                                data-id="{{ $subscription->id }}"
                                                data-name="{{ $subscription->subscriptionName }}"
                                                data-description="{{ $subscription->description }}"
                                                data-price="{{ $subscription->price }}"
                                                data-duration="{{ $subscription->daysAllowed }}"
                                                data-max-vcards="{{ $subscription->vcardAllowed }}"
                                                data-scan-allowed="{{ $subscription->scansAllowed ?? 0 }}"
                                                data-active="{{ $subscription->isActive }}"
                                                data-bs-toggle="modal" 
                                                data-bs-target="#editSubscriptionModal">
                                            <i class="bi bi-pencil"></i> Edit
                                        </button>
                                        <button class="btn btn-sm {{ $subscription->isActive ? 'btn-warning' : 'btn-success' }} toggle-status-btn"
                                                data-id="{{ $subscription->id }}"
                                                data-status="{{ $subscription->isActive }}">
                                            <i class="bi bi-{{ $subscription->isActive ? 'pause' : 'play' }}-circle"></i>
                                            {{ $subscription->isActive ? 'Deactivate' : 'Activate' }}
                                        </button>
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
</main>

<!-- Add Subscription Modal -->
<div class="modal fade" id="addSubscriptionModal" tabindex="-1" aria-labelledby="addSubscriptionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addSubscriptionModalLabel">Add New Subscription</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ url('/addSubscription') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="subscriptionName" class="form-label">Subscription Name *</label>
                                <input type="text" class="form-control" id="subscriptionName" name="subscriptionName" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="price" class="form-label">Price (₹) *</label>
                                <input type="number" step="0.01" class="form-control" id="price" name="price" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="daysAllowed" class="form-label">Duration (Days) *</label>
                                <input type="number" class="form-control" id="daysAllowed" name="daysAllowed" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="vcardAllowed" class="form-label">Max VCards *</label>
                                <input type="number" class="form-control" id="vcardAllowed" name="vcardAllowed" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="scansAllowed" class="form-label">Scans Allowed</label>
                                <input type="number" class="form-control" id="scansAllowed" name="scansAllowed" value="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3 form-check" style="margin-top: 32px;">
                                <input type="checkbox" class="form-check-input" id="isActive" name="isActive" checked>
                                <label class="form-check-label" for="isActive">
                                    Active
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Subscription</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Subscription Modal -->
<div class="modal fade" id="editSubscriptionModal" tabindex="-1" aria-labelledby="editSubscriptionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editSubscriptionModalLabel">Edit Subscription</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="{{ url('/updateSubscription') }}" method="POST">
                @csrf
                <input type="hidden" id="edit_subscription_id" name="subscription_id">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_subscriptionName" class="form-label">Subscription Name *</label>
                                <input type="text" class="form-control" id="edit_subscriptionName" name="subscriptionName" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_price" class="form-label">Price (₹) *</label>
                                <input type="number" step="0.01" class="form-control" id="edit_price" name="price" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_daysAllowed" class="form-label">Duration (Days) *</label>
                                <input type="number" class="form-control" id="edit_daysAllowed" name="daysAllowed" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_vcardAllowed" class="form-label">Max VCards *</label>
                                <input type="number" class="form-control" id="edit_vcardAllowed" name="vcardAllowed" required>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="edit_scansAllowed" class="form-label">Scans Allowed</label>
                                <input type="number" class="form-control" id="edit_scansAllowed" name="scansAllowed" value="0">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3 form-check" style="margin-top: 32px;">
                                <input type="checkbox" class="form-check-input" id="edit_isActive" name="isActive">
                                <label class="form-check-label" for="edit_isActive">
                                    Active
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="edit_description" class="form-label">Description</label>
                        <textarea class="form-control" id="edit_description" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Subscription</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Handle edit subscription modal
    const editButtons = document.querySelectorAll('.edit-subscription-btn');
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const description = this.getAttribute('data-description');
            const price = this.getAttribute('data-price');
            const duration = this.getAttribute('data-duration');
            const maxVcards = this.getAttribute('data-max-vcards');
            const scanAllowed = this.getAttribute('data-scan-allowed') || '0';
            const active = this.getAttribute('data-active') === '1';

            document.getElementById('edit_subscription_id').value = id;
            document.getElementById('edit_subscriptionName').value = name;
            document.getElementById('edit_description').value = description || '';
            document.getElementById('edit_price').value = price;
            document.getElementById('edit_daysAllowed').value = duration;
            document.getElementById('edit_vcardAllowed').value = maxVcards;
            document.getElementById('edit_scansAllowed').value = scanAllowed;
            document.getElementById('edit_isActive').checked = active;
        });
    });

    // Handle toggle status
    const toggleButtons = document.querySelectorAll('.toggle-status-btn');
    toggleButtons.forEach(button => {
        button.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const currentStatus = this.getAttribute('data-status') === '1';
            
            if (confirm(`Are you sure you want to ${currentStatus ? 'deactivate' : 'activate'} this subscription?`)) {
                fetch('{{ url("/toggleSubscriptionStatus") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        subscription_id: id
                    })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        location.reload();
                    } else {
                        alert('Error: ' + data.message);
                    }
                })
                .catch(error => {
                    alert('An error occurred. Please try again.');
                    console.error('Error:', error);
                });
            }
        });
    });
});
</script>

@endsection
