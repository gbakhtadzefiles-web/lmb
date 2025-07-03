@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h6 class="text-center">ახალი კონტრაქტი</h6>
    <hr>
    <form action="{{ route('loans.store') }}" method="post">
        @csrf
        <div class="row">
            {{-- Client Section --}}
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">კლიენტის ინფორმაცია</div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="personal_id">პირადი ნომერი:</label>
                            <input type="text" class="form-control" id="personal_id" name="personal_id" required>
                        </div>
                        <div class="form-group">
                            <label for="name">სახელი:</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="form-group">
                            <label for="phone">ტელეფონი:</label>
                            <input type="text" class="form-control" id="phone" name="phone" required>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Collaterals Section --}}
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">უზრუნველყოფა</div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="collateral_type">Type:</label>
                            <select class="form-control" id="collateral_type" name="collateral_type">
                                <option value="1">ტელეფონი</option>
                                <option value="2">ლეპტოპი</option>
                                <option value="3">სხვა..</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="brand">ბრენდი:</label>
                            <select class="form-control" id="brand" name="brand">
                                {{-- Brands will be populated dynamically --}}
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="model">მოდელი:</label>
                            <input type="text" class="form-control" id="model" name="model" required>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-3">
            {{-- Loans Section --}}
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">სესხის ინფორმაცია</div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="loan_amount">თანხა:</label>
                            <input type="number" class="form-control" id="loan_amount" name="loan_amount" required>
                        </div>
                    <div class="form-group">
    <label for="interest_option">პირობები:</label>
    <select class="form-control" id="interest_option" required>
        <option value="" disabled selected>აირჩიეთ</option>
        <option data-rate="7" data-days="10">7% - 10 დღე</option>
        <option data-rate="10" data-days="15">10% - 15 დღე</option>
    </select>
</div>

<input type="hidden" name="interest_rate" id="interest_rate" value="">
<input type="hidden" name="number_of_d" id="number_of_d" value="">
                        <div class="form-group">
                            <label for="loan_start_date">სესხის დაწყების თარიღი:</label>
                            <input type="date" class="form-control" id="loan_start_date" name="loan_start_date" value="{{ date('Y-m-d') }}" required readonly>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Additional Details Section -->
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">დამატებითი ინფორმაცია</div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="email">მეილი:</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="pass">კოდი:</label>
                            <input type="text" class="form-control" id="pass" name="pass">
                        </div>
                        <div class="form-group">
                            <label for="imei">IMEI კოდი:</label>
                            <input type="text" class="form-control" id="imei" name="imei">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Comments Section -->
        <div class="row mt-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">კომენტარი</div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="comment">კომენტარი:</label>
                            <textarea class="form-control" id="comment" name="comment" rows="3"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="mt-3">
            <button type="submit" class="btn btn-primary">შენახვა</button>
        </div>
    </form>
    <br/>
    @if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif
</div>


<script>
    $('#interest_option').change(function() {
    const selectedOption = $(this).find(':selected');
    const rate = selectedOption.data('rate');
    const days = selectedOption.data('days');

    $('#interest_rate').val(rate);
    $('#number_of_d').val(days);
});

$(document).ready(function() {
    // Function to load brands based on typeId
    function loadBrands(typeId) {
        var brandSelect = $('#brand'); // Get the brand select element

        // Make an AJAX request to the server
        $.ajax({
            url: '/api/brands/' + typeId,
            type: 'GET',
            dataType: 'json',
            success: function(response) {
                brandSelect.empty(); // Remove old options
                brandSelect.append($('<option>').text('Select a brand').attr('value', '')); // Add a default option
                $.each(response, function(index, brand) {
                    brandSelect.append($('<option>').text(brand.name).attr('value', brand.id)); // Assuming 'name' and 'id' are attributes of the brand
                });
            },
            error: function(xhr, status, error) {
                console.error("Error fetching brands: " + error);
            }
        });
    }

    // Load brands for typeId 1 immediately on page load
    loadBrands(1);

    // Set the change event handler for the collateral type select
    $('#collateral_type').change(function() {
        var typeId = $(this).val(); // Get the selected collateral type ID
        loadBrands(typeId); // Load brands for the selected typeId
    });
});
</script>



@endsection




<!-- </body>
</html> -->