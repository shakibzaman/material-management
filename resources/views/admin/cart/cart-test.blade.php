@extends('layouts.admin')
@section('content')
<div class="card container-fluid">
    <div class="card-title bg-primary text-center">
        <div class="container">
            <h2>{{$department->name}} POS</h2>
        </div>
    </div>
    <form id="showroom-pos">
        <input type="hidden" name="department_id" id="department_id" value="{{$department->id}}">

        @csrf
    <div class="row bg-info">
        <div class="col-md-4">
            <div class="form-group {{ $errors->has('invoice_id') ? 'has-error' : '' }}">
                <label for="name">Invoice No*</label>
                <input type="text" name="invoice_id" value="{{$invoice_id}}" id="invoice_id" class="form-control" required>
                @if($errors->has('invoice_id'))
                    <em class="invalid-feedback">
                        {{ $errors->first('invoice_id') }}
                    </em>
                @endif
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group {{ $errors->has('date') ? 'has-error' : '' }}">
                <label for="name">Date *</label>
                <input type="date" name="date" id="date" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
                @if($errors->has('date'))
                    <em class="invalid-feedback">
                        {{ $errors->first('date') }}
                    </em>
                @endif
            </div>
        </div>

        <div class="col-md-4">
            <div class="form-group {{ $errors->has('customer_id') ? 'has-error' : '' }}">
                <label for="name">Customer *</label>
                <select name="customer_id" id="customer_id" class="form-control select2" required>
                    @foreach($customers as $id => $customer)
                        <option value="{{ $id }}" >{{ $customer }}</option>
                    @endforeach
                </select>
                @if($errors->has('customer_id'))
                    <em class="invalid-feedback">
                        {{ $errors->first('customer_id') }}
                    </em>
                @endif
            </div>
        </div>
    </div>
        <div class="row">
        <div class="col-md-6">
            <div class="form-group {{ $errors->has('material_id') ? 'has-error' : '' }}">
                <label for="name">Color Name *</label>
                <select name="product_color_id" id="product_color_id" class="form-control select2 product_color_id" required>
                    @foreach($materials as $key => $id)
                        <option value="{{$id }}" >{{ $key }}</option>
                    @endforeach
                </select>
                @if($errors->has('product_id'))
                    <em class="invalid-feedback">
                        {{ $errors->first('product_id') }}
                    </em>
                @endif
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="cart-body" style="background: #ffe8e8">
                <table class="table table-bordered" id="myTable">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Available Qty</th>
                        <th>Quantity</th>
                        <th>Price</th>
                        <th>Line Total</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
        <div class="row">
            <div class="col-md-10"></div>
            <div class="col-md-2 bg-success align-self-end">
                <div class="form-group {{ $errors->has('sub_total') ? 'has-error' : '' }}">
                    <label for="name">Sub Total *</label>
                    <input type="number" name="sub_total" id="sub_total" class="form-control" >
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <div class="form-group {{ $errors->has('discount') ? 'has-error' : '' }}">
                    <label for="name">Discount</label>
                    <input type="number" name="discount" id="discount" class="form-control">
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group {{ $errors->has('total') ? 'has-error' : '' }}">
                    <label for="name">Total</label>
                    <input type="number" id="total" name="total" class="form-control"  >
                </div>
            </div>

            <div class="col-md-3">
                <div class="form-group {{ $errors->has('paid') ? 'has-error' : '' }}">
                    <label for="name">Paid *</label>
                    <input type="number" name="paid" id="paid" class="form-control">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group {{ $errors->has('due') ? 'has-error' : '' }}">
                    <label for="name">Due *</label>
                    <input type="number" name="due" id="due" class="form-control" >
                </div>
            </div>
        </div>


        <button type="submit" class="btn btn-primary submit-btn">Sell</button>
    </form>
</div>
@endsection
@parent
@section('scripts')
<script>
    function calculateTotal(){
        let total = $("#sub_total").val() - $("#discount").val();
        $("#total").val(total);
        let due = total - $("#paid").val()
        $("#due").val(due);
    }
    $("#discount").keyup(function (){
        calculateTotal();
    })
    $("#paid").keyup(function (){
        calculateTotal();
    })

    $(document).ready(function(){

        $(document).on('change', '.quantity', function() {
            var ele = $(this);
            let quantity = ele.parents("tr").find(".quantity").val();
            let price = ele.parents("tr").find(".price").val();
            let total = quantity * price;
            let line_total = ele.parents("tr").find(".line_total").val(total);


            let sum = 0;
            let line_total_sum = document.querySelectorAll('.line_total');
            for(total_sum of line_total_sum) {
                sum += (parseFloat(total_sum.value));
            }
            document.getElementById('sub_total').value = sum;
            document.getElementById('total').value = sum;
            document.getElementById('due').value = sum;

        });

        $(document).on('change', '.price', function() {
            var ele = $(this);
            let quantity = ele.parents("tr").find(".quantity").val();
            let price = ele.parents("tr").find(".price").val();
            let total = quantity * price;
            let line_total = ele.parents("tr").find(".line_total").val(total);

            let sum = 0;
            let line_total_sum = document.querySelectorAll('.line_total');
            for(total_sum of line_total_sum) {
                sum += (parseFloat(total_sum.value));
            }
            document.getElementById('sub_total').value = sum;
            document.getElementById('total').value = sum;
            document.getElementById('due').value = sum;



        });
        $(document).on('click', '.remove-from-cart', function() {
            var ele = $(this);
            var row_id = ele.parents("tr").remove();
        });

        function calculateTotal(){
            let total = $("#sub-total").val() - $("#discount").val();
            $("#total").val(total);
            let due = total - $("#paid").val()
            $("#due").val(due);
        }

        $( "#product_color_id" ).change(function() {

            let product_id_number = $("#product_id").val();
            let product_color_id = $("#product_color_id").val();
            let department_id = $("#department_id").val();


            $.ajax({
                url: '/admin/add-to-cart-product/'+department_id+'/'+product_color_id,
                type: 'GET',
                cache: false,
                datatype: 'application/json',

                success:function(data){
                    // console.log(data);
                    // renderList(data);
                    $('#myTable > tbody:last-child').append(data.html);


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

        $("#showroom-pos").on('submit',function (e){
            e.preventDefault();

            showroomOrder();
        })
        function showroomOrder(){
            $.ajax({
                url:'/admin/showroom/cart/order',
                type:'POST',
                data:$("form#showroom-pos").serialize(),

                success(data){
                    if(data.status == 200){
                        Swal.fire({
                            position: 'top-end',
                            icon: 'success',
                            title: 'Order Done successfully',
                            timer: 1500
                        })

                        window.location.reload();
                    }
                    if(data.status == 104){
                        Swal.fire({
                            icon: 'error',
                            title: data.message,
                            footer: 'Please Recheck your stock',
                        })
                    }
                },
                error:function(data){
                    if(data.status == 422 ){
                        Swal.fire({
                            icon: 'error',
                            title: "Invoice number already used",
                            text: "Unable to load data form server",
                            footer: 'Please Recheck your details'
                        })
                    }
                }
            })
        }

    })
</script>
@endsection
