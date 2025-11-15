@php
  $session = session()->all();
  $count=1;
@endphp
@extends('admin.layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Subscription Management</h3>
                    <button type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#addSubscriptionModal">
                        Add New Subscription
                    </button>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-striped">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($subscriptions as $subscription)
                            <tr>
                                <td>{{ $subscription->id }}</td>
                                <td>{{ $subscription->name }}</td>
                                <td>
                                    <span class="badge badge-{{ $subscription->is_active ? 'success' : 'danger' }}">
                                        {{ $subscription->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-info edit-btn" 
                                            data-id="{{ $subscription->id }}"
                                            data-name="{{ $subscription->name }}"
                                            data-toggle="modal" 
                                            data-target="#editSubscriptionModal">
                                        Edit
                                    </button>
                                    <button class="btn btn-sm {{ $subscription->is_active ? 'btn-danger' : 'btn-success' }} toggle-status"
                                            data-id="{{ $subscription->id }}"
                                            data-status="{{ $subscription->is_active }}">
                                        {{ $subscription->is_active ? 'Deactivate' : 'Activate' }}
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
</div>

<!-- Add Subscription Modal -->
<div class="modal fade" id="addSubscriptionModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Subscription</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="addSubscriptionForm">
                <div class="modal-body">
                    <div class="form-group">
                        <label>Subscription Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Subscription Modal -->
<div class="modal fade" id="editSubscriptionModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Subscription</h5>
                <button type="button" class="close" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <form id="editSubscriptionForm">
                <div class="modal-body">
                    <input type="hidden" name="subscription_id">
                    <div class="form-group">
                        <label>Subscription Name</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Add Subscription
    $('#addSubscriptionForm').on('submit', function(e) {
        e.preventDefault();
        $.ajax({
            url: '/admin/subscriptions',
            type: 'POST',
            data: $(this).serialize(),
            success: function(response) {
                $('#addSubscriptionModal').modal('hide');
                location.reload();
            },
            error: function(xhr) {
                alert('Error occurred while adding subscription');
            }
        });
    });

    // Edit Subscription
    $('.edit-btn').click(function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        $('#editSubscriptionForm input[name="subscription_id"]').val(id);
        $('#editSubscriptionForm input[name="name"]').val(name);
    });

    $('#editSubscriptionForm').on('submit', function(e) {
        e.preventDefault();
        var id = $('input[name="subscription_id"]').val();
        $.ajax({
            url: '/admin/subscriptions/' + id,
            type: 'PUT',
            data: $(this).serialize(),
            success: function(response) {
                $('#editSubscriptionModal').modal('hide');
                location.reload();
            },
            error: function(xhr) {
                alert('Error occurred while updating subscription');
            }
        });
    });

    // Toggle Status
    $('.toggle-status').click(function() {
        var id = $(this).data('id');
        var status = $(this).data('status');
        $.ajax({
            url: '/admin/subscriptions/' + id + '/toggle-status',
            type: 'PUT',
            data: {
                is_active: !status
            },
            success: function(response) {
                location.reload();
            },
            error: function(xhr) {
                alert('Error occurred while updating status');
            }
        });
    });
});
</script>
@endpush
