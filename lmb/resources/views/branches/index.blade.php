@extends('layouts.app')

@section('content')
<div class="container">
    <h6 class="text-center mb-3">ფილიალები</h6>

    <button type="button" class="btn btn-success mb-2" data-toggle="modal" data-target="#createBranchModal">
        ახალი ფილიალი
    </button>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    <table class="table">
        <thead>
            <tr>
                <th>#</th>
                <th>სახელი</th>
                <th>მომხმარებლები</th>
            </tr>
        </thead>
        <tbody>
            @foreach($branches as $branch)
            <tr>
                <td>{{ $branch->id }}</td>
                <td>{{ $branch->name }}</td>
                <td>{{ $branch->users_count }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Create Branch Modal -->
<div class="modal fade" id="createBranchModal" tabindex="-1" role="dialog" aria-labelledby="createBranchModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createBranchModalLabel">ახალი ფილიალი</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="{{ route('branches.store') }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="branch_name">ფილიალის სახელი</label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                            id="branch_name" name="name" value="{{ old('name') }}" required>
                        @error('name')
                            <span class="invalid-feedback">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">გაუქმება</button>
                    <button type="submit" class="btn btn-success">დამატება</button>
                </div>
            </form>
        </div>
    </div>
</div>

@if($errors->any())
<script>
    $(document).ready(function () {
        $('#createBranchModal').modal('show');
    });
</script>
@endif

@endsection
