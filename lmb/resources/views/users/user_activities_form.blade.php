{{-- User and Date Range Form --}}
<form action="{{ route('user.activities') }}" method="GET" class="form-row align-items-center mb-4">
    <div class="col-3">
        <select class="form-control" name="user_id">
            <option value="">Select User</option>
            @foreach($users as $user)
                <option value="{{ $user->id }}" {{ request()->get('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-3">
        <input type="date" class="form-control" name="start_date" value="{{ request()->get('start_date') }}">
    </div>
    <div class="col-3">
        <input type="date" class="form-control" name="end_date" value="{{ request()->get('end_date') }}">
    </div>
    <div class="col-3">
        <button type="submit" class="btn btn-primary">Filter</button>
    </div>
</form>
