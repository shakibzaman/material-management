@extends('layouts.admin')
@section('content')
<div class="card container-fluid">
    <form id="showroom-pos">
        @csrf
    <div class="row">
        <div class="col-md-6">
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
        </div>
        <div class="col-md-6">
            <div class="form-group {{ $errors->has('material_id') ? 'has-error' : '' }}">
                <label for="name">Color Name *</label>
                <select name="product_color_id" id="product_color_id" class="form-control select2 product_color_id" required>

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
        <div class="col-md-9">
            <div class="cart-body" style="background: #ffe8e8">
                <table class="table table-bordered" id="myTable">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Color</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Line Total</th>
                    </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
        <div class="col-md-3">
            <div class="col-md-12">
                <input type="hidden" name="department_id" value="3">
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
            <div class="col-md-12">
                <div class="form-group {{ $errors->has('invoice_id') ? 'has-error' : '' }}">
                    <label for="name">Invoice No*</label>
                    <input type="text" name="invoice_id" id="invoice_id" class="form-control" required>
                    @if($errors->has('invoice_id'))
                        <em class="invalid-feedback">
                            {{ $errors->first('invoice_id') }}
                        </em>
                    @endif
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group {{ $errors->has('date') ? 'has-error' : '' }}">
                    <label for="name">Date *</label>
                    <input type="date" name="date" id="date" class="form-control" required>
                    @if($errors->has('date'))
                        <em class="invalid-feedback">
                            {{ $errors->first('date') }}
                        </em>
                    @endif
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group {{ $errors->has('payment_process') ? 'has-error' : '' }}">
                    <label for="payment_process">Payment Process *</label>
                    <select name="payment_process" id="payment_process" class="form-control" required>
                        <option value="">---</option>
                        <option value="bank">Bank</option>
                        <option value="bkash">Bkash</option>
                        <option value="cash">Cash</option>
                    </select>
                    @if($errors->has('payment_process'))
                        <em class="invalid-feedback">
                            {{ $errors->first('payment_process') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('cruds.expense.fields.entry_date_helper') }}
                    </p>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group {{ $errors->has('payment_info') ? 'has-error' : '' }}">
                    <label for="payment_info">Payment Info </label>
                    <input type="text" id="payment_info" name="payment_info" class="form-control" required>
                    @if($errors->has('payment_info'))
                        <em class="invalid-feedback">
                            {{ $errors->first('payment_info') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('cruds.expense.fields.entry_date_helper') }}
                    </p>
                </div>
            </div>

        </div>

    </div>
        <div class="row">
            <div class="col-md-2">
                <div class="form-group {{ $errors->has('total') ? 'has-error' : '' }}">
                    <label for="name">Total</label>
                    <input type="number" name="total" class="form-control"  >
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group {{ $errors->has('discount') ? 'has-error' : '' }}">
                    <label for="name">Discount</label>
                    <input type="number" name="discount" id="discount" class="form-control">
                </div>
            </div>

            <div class="col-md-2">
                <div class="form-group {{ $errors->has('sub_total') ? 'has-error' : '' }}">
                    <label for="name">Sub Total *</label>
                    <input type="number" name="sub_total" id="sub_total" class="form-control" >
                </div>
            </div>
            <div class="col-md-2">
                <div class="form-group {{ $errors->has('paid') ? 'has-error' : '' }}">
                    <label for="name">Paid *</label>
                    <input type="number" name="paid" id="paid" class="form-control">
                </div>
            </div>
            <div class="col-md-2">
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
    $(document).ready(function(){

        $( "#product_id" ).change(function() {

            let product_id_number = $("#product_id").val();


            $.ajax({
                url: '/admin/material/search/'+product_id_number,
                type: 'GET',
                cache: false,
                datatype: 'application/json',

                success:function(data){
                    // console.log(data);
                    // console.log(data.color);
                    var op ='<option value="0" selected>--- Select Color ---</option>';
                    for(var i=0;i<data.color.length;i++){
                        op+='<option value="'+data.color[i].id+'">'+data.color[i].name+'</option>';
                    }
                    // set value to the Color
                    $('.product_color_id').html("");
                    $('.product_color_id').append(op);
                    // renderList(data.material);


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

        $( "#product_color_id" ).change(function() {

            let product_id_number = $("#product_id").val();
            let product_color_id = $("#product_color_id").val();


            $.ajax({
                url: '/admin/add-to-cart-test/'+product_id_number+'/'+product_color_id,
                type: 'GET',
                cache: false,
                datatype: 'application/json',

                success:function(data){
                    console.log(data);
                    // renderList(data);
                    // $('#myTable > tbody:last-child').append(data);


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


        // $(".quantity").keydown(function() {
        //     alert("Hi");
        //     console.log("ok");
        // });

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
                    console.log("succ");
                    console.log(data);
                },
                error:function(data){
                    if(data.status ==422 ){
                        Swal.fire({
                            icon: 'error',
                            title: data.responseText,
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
