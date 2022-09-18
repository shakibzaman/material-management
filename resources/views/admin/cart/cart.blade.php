@extends('layouts.admin')
@section('content')
<div class="card">
    <div class="form-group {{ $errors->has('material_id') ? 'has-error' : '' }}">
        <label for="name">Product Name *</label>
        <select name="product_id" id="product_id" class="form-control select2" required>
            @foreach($materials as $id => $material)
                <option value="{{ $id }}" >{{ $material }}</option>
            @endforeach
        </select>
        @if($errors->has('product_id'))
            <em class="invalid-feedback">
                {{ $errors->first('product_id') }}
            </em>
        @endif
    </div>
    <div class="cart-body">
        <form action="">
            <table class="table table-bordered" id="myTable">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Line Total</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
            <button type="submit" class="btn btn-primary submit-btn">Sell</button>
        </form>
    </div>
</div>
@endsection
@parent
@section('scripts')
<script>
    $(document).ready(function(){
        if ($('#Table1 tr').length == 0) {
            $(".submit-btn").hide();
        }
        else{
            $(".submit-btn").show();
        }
        $( "#product_id" ).change(function() {
            let product_id_number = $("#product_id").val();

            $.ajax({
                url: '/admin/material/search/'+product_id_number,
                type: 'GET',
                cache: false,
                datatype: 'application/json',

                success:function(data){
                    // console.log(data);
                    $('#myTable > tbody:last-child').append(data);


                },
                error:function(data){
                    // console.log(data);
                    // sweet alert
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: "Unable to load data form server",
                        footer: 'Contact with Your Admin'
                    })
                    // swal("Error", 'Unable to load data form server', "error");
                }
            });
        });
    })
</script>
@endsection
