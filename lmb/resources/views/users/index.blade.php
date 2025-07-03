@extends('layouts.app')

@section('content')



<div class="container">
    <h6 class='text-center'>მომხმარებლების მართვა</h6>
    <a href="{{ route('register') }}" class="btn btn-success mb-2">ახალი მომხმარებელი</a>
    <table class="table">
        <thead>
            <tr>
                <th>სახელი</th>
                <th>მეილი</th>
                <th>როლი</th>
                <th>ფილიალი</th>
                <th>სტატუსი</th>
                <th>ქმედებები</th>
            </tr>
        </thead>
        <tbody>
        <pre>

</pre>
            @foreach($users as $user)
            <tr>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->role->name }}</td>
                <td>{{ $user->branch->name }}</td>
                <td>{{ $user->status == 1 ? 'აქტიური' : 'გამორთული' }}</td>
                <td>
                    <a href="{{ route('users.reset', $user->id) }}" class="btn btn-sm btn-warning">პაროლის შეცვლა</a>
                    @if($user->status == 1)
                    <a href="{{ route('users.disable', $user->id) }}" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">გათიშვა</a>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@if ($errors->any())
<div class="alert alert-danger">
    <ul>
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif
@endsection
