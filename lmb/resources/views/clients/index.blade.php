@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h6 class='text-center'>კლიენტების სია</h6>
    <form method="GET" action="{{ route('clients.index') }}" style="display: flex; justify-content: center; margin-bottom: 20px;">
    <div style="width: 100%; max-width: 600px; display: flex;">
        <input type="text" class="form-control" name="search" placeholder="ძებნა სახელით ან პირადი ნომრით" value="{{ request('search') }}" style="flex-grow: 1; margin-right: 10px;">
        <button type="submit" class="btn btn-primary">ძებნა</button>
    </div>
</form>
    
    <table class="table mt-4">
        <thead>
            <tr>
                <th>ID</th>
                <th>სახელი</th>
                <th>პირადი ნომერი</th>
                <th>ტელეფონი</th>
                <th>რედაქტირება</th>
            </tr>
        </thead>
        <tbody>
            @forelse($clients as $client)
                <tr>
                    <td>{{$client->client_id }}</td>
                    <td>{{ $client->name }}</td>
                    <td>{{ $client->personal_id }}</td>
                    <td>{{ $client->phone }}</td>
                    <td>
                        <a href="{{ route('clients.show', $client->client_id )}}" class="btn btn-info btn-sm">Details</a>
                        <a href="{{ route('clients.edit', $client->client_id )}}" class="btn btn-primary btn-sm">Edit</a>
                        <!-- Delete button -->
                    </td>
                </tr>
            @empty
                <tr><td colspan="4">No clients found.</td></tr>
            @endforelse
        </tbody>
    </table>

    <!-- Pagination Links -->
    {{ $clients->links() }}
</div>
@endsection
