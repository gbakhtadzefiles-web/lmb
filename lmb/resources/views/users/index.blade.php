@extends('layouts.app')

@section('content')

<div class="container">
    <h6 class='text-center'>მომხმარებლების მართვა</h6>
    <a href="{{ route('register') }}" class="btn btn-success mb-2">ახალი მომხმარებელი</a>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

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
            @foreach($users as $user)
            <tr>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->role->name }}</td>
                <td>{{ $user->branch->name }}</td>
                <td>{{ $user->status == 1 ? 'აქტიური' : 'გამორთული' }}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-warning"
                        data-toggle="modal"
                        data-target="#passwordModal"
                        data-user-id="{{ $user->id }}"
                        data-user-name="{{ $user->name }}">
                        პაროლის შეცვლა
                    </button>
                    @if($user->status == 1)
                    <a href="{{ route('users.disable', $user->id) }}" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')">გათიშვა</a>
                    @else
                    <a href="{{ route('users.enable', $user->id) }}" class="btn btn-sm btn-success" onclick="return confirm('Are you sure?')">გააქტიურება</a>
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Password Update Modal -->
<div class="modal fade" id="passwordModal" tabindex="-1" aria-labelledby="passwordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="passwordModalLabel">პაროლის შეცვლა</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <form id="passwordForm" method="POST" action="">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <p class="text-muted mb-3">მომხმარებელი: <strong id="modalUserName"></strong></p>
                    <div class="mb-3">
                        <label for="new_password" class="form-label">ახალი პაროლი</label>
                        <input type="password" class="form-control" id="new_password" name="password" required minlength="8">
                    </div>
                    <div class="mb-3">
                        <label for="new_password_confirmation" class="form-label">გაიმეორეთ პაროლი</label>
                        <input type="password" class="form-control" id="new_password_confirmation" name="password_confirmation" required minlength="8">
                    </div>
                    <div id="passwordError" class="text-danger d-none">პაროლები არ ემთხვევა</div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">გაუქმება</button>
                    <button type="submit" class="btn btn-warning">შეცვლა</button>
                </div>
            </form>
        </div>
    </div>
</div>

@if ($errors->any())
<div class="alert alert-danger mt-2">
    <ul class="mb-0">
        @foreach ($errors->all() as $error)
            <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif

<script>
    $('#passwordModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var userId = button.data('user-id');
        var userName = button.data('user-name');

        $('#modalUserName').text(userName);
        $('#passwordForm').attr('action', '/users/' + userId + '/password');
        $('#new_password').val('');
        $('#new_password_confirmation').val('');
        $('#passwordError').addClass('d-none');
    });

    $('#passwordForm').on('submit', function (e) {
        var pw = $('#new_password').val();
        var pwc = $('#new_password_confirmation').val();
        if (pw !== pwc) {
            e.preventDefault();
            $('#passwordError').removeClass('d-none');
        }
    });
</script>
@endsection
