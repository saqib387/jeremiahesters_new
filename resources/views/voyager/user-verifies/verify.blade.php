@extends('voyager::master')

@section('page_title', 'Verify User Identity')

@section('page_header')
    <div class="container-fluid">
        <h1 class="page-title">
            <i class="voyager-check"></i> Verify User Identity
        </h1>
        <a href="{{ url('/admin/user-verifies') }}" class="btn btn-default">
            <i class="voyager-angle-left"></i> <span>Back to List</span>
        </a>
    </div>
@stop

@section('content')
    <div class="page-content container-fluid">
        @include('voyager::alerts')
        
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-bordered">
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h3>User Information</h3>
                                <table class="table table-bordered">
                                    <tr>
                                        <th width="30%">Name:</th>
                                        <td>{{ $userVerify->user->name ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Email:</th>
                                        <td>{{ $userVerify->user->email ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>Username:</th>
                                        <td>{{ $userVerify->user->username ?? 'N/A' }}</td>
                                    </tr>
                                    <tr>
                                        <th>User ID:</th>
                                        <td>{{ $userVerify->user_id }}</td>
                                    </tr>
                                    <tr>
                                        <th>Status:</th>
                                        <td>
                                            @if($userVerify->status == 'pending')
                                                <span class="label label-warning">Pending</span>
                                            @elseif($userVerify->status == 'verified')
                                                <span class="label label-success">Verified</span>
                                            @elseif($userVerify->status == 'rejected')
                                                <span class="label label-danger">Rejected</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Submitted:</th>
                                        <td>{{ $userVerify->created_at->format('F d, Y g:i A') }}</td>
                                    </tr>
                                    @if($userVerify->rejectionReason)
                                    <tr>
                                        <th>Rejection Reason:</th>
                                        <td>{{ $userVerify->rejectionReason }}</td>
                                    </tr>
                                    @endif
                                </table>
                                
                                <div class="mt-3">
                                    <a href="{{ route('profile', ['username' => $userVerify->user->username ?? '']) }}" 
                                       target="_blank" 
                                       class="btn btn-info">
                                        <i class="voyager-person"></i> View User Profile
                                    </a>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <h3>ID Documents</h3>
                                @php
                                    $files = json_decode($userVerify->files, true);
                                @endphp
                                
                                @if($files && count($files) > 0)
                                    <div class="row">
                                        @foreach($files as $fileId)
                                            @php
                                                $attachment = \App\Model\Attachment::find($fileId);
                                            @endphp
                                            @if($attachment)
                                                <div class="col-md-6 mb-3">
                                                    <div class="card">
                                                        <div class="card-body text-center">
                                                            <img src="{{ $attachment->path }}" 
                                                                 class="img-responsive" 
                                                                 style="max-width: 100%; max-height: 300px; cursor: pointer;"
                                                                 onclick="window.open('{{ $attachment->path }}', '_blank')"
                                                                 alt="ID Document">
                                                            <p class="mt-2 mb-0">
                                                                <small>{{ $attachment->attachmentType ?? 'Document' }}</small>
                                                            </p>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endif
                                        @endforeach
                                    </div>
                                @else
                                    <p class="text-muted">No documents uploaded.</p>
                                @endif
                            </div>
                        </div>
                        
                        <hr>
                        
                        <div class="row">
                            <div class="col-md-12">
                                <h3>Verification Actions</h3>
                                <form action="{{ route('voyager.user-verifies.update-status', $userVerify->id) }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    
                                    <div class="form-group">
                                        <label for="status">Status:</label>
                                        <select name="status" id="status" class="form-control" required>
                                            <option value="pending" {{ $userVerify->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                            <option value="verified" {{ $userVerify->status == 'verified' ? 'selected' : '' }}>Verified</option>
                                            <option value="rejected" {{ $userVerify->status == 'rejected' ? 'selected' : '' }}>Rejected</option>
                                        </select>
                                    </div>
                                    
                                    <div class="form-group" id="rejection-reason-group" style="display: none;">
                                        <label for="rejectionReason">Rejection Reason:</label>
                                        <textarea name="rejectionReason" 
                                                  id="rejectionReason" 
                                                  class="form-control" 
                                                  rows="3" 
                                                  placeholder="Please provide a reason for rejection...">{{ $userVerify->rejectionReason }}</textarea>
                                    </div>
                                    
                                    <div class="form-group">
                                        <button type="submit" class="btn btn-success">
                                            <i class="voyager-check"></i> Update Status
                                        </button>
                                        <a href="{{ url('/admin/user-verifies') }}" class="btn btn-default">
                                            Cancel
                                        </a>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@stop

@section('javascript')
<script>
    $(document).ready(function() {
        $('#status').on('change', function() {
            if ($(this).val() === 'rejected') {
                $('#rejection-reason-group').slideDown();
                $('#rejectionReason').prop('required', true);
            } else {
                $('#rejection-reason-group').slideUp();
                $('#rejectionReason').prop('required', false);
            }
        });
        
        // Trigger on page load
        $('#status').trigger('change');
    });
</script>
@stop
