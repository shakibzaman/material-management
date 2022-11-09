@extends('layouts.admin')
@section('content')

    <div class="card">
        <div class="card-header">
            Raw Products Purchase
        </div>

        <div class="card-body">
            <form action="{{ route("admin.material-in.store") }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="row bg-info">
                    <div class="col-md-4">
                        <div class="col-md-12">
                            <div class="form-group {{ $errors->has('buying_date') ? 'has-error' : '' }}">
                                <label for="buying_date">Buying Date *</label>
                                <input type="text" id="buying_date" name="buying_date" class="form-control date"
                                       required>
                                <input type="hidden" name="type" value="2">
                                @if($errors->has('buying_date'))
                                    <em class="invalid-feedback">
                                        {{ $errors->first('buying_date') }}
                                    </em>
                                @endif
                                <p class="helper-block">
                                    {{ trans('cruds.expense.fields.entry_date_helper') }}
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="col-md-12">
                            <div class="form-group {{ $errors->has('unit') ? 'has-error' : '' }}">
                                <label for="supplied_by">Supplier *</label>
                                <select name="supplier_id" id="supplier_id" class="form-control select2" required>
                                    @foreach($suppliers as $id=>$supplier)
                                        <option value="{{$id}}">{{$supplier}}</option>
                                    @endforeach
                                </select>
                                @if($errors->has('supplier_id'))
                                    <em class="invalid-feedback">
                                        {{ $errors->first('supplier_id') }}
                                    </em>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="col-md-12">
                            <div class="form-group {{ $errors->has('inv_number') ? 'has-error' : '' }}">
                                <label for="inv_number">INV number *</label>
                                <input type="text" id="inv_number" name="inv_number" class="form-control" required>
                                @if($errors->has('inv_number'))
                                    <em class="invalid-feedback">
                                        {{ $errors->first('inv_number') }}
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
                    <div class="col-md-6">
                        <div class="form-group {{ $errors->has('material_id') ? 'has-error' : '' }}">
                            <label for="name">Material Name *</label>
                            <select name="product_id" id="product_id" class="form-control select2 product_id" required>
                                @foreach($materials as $key => $material)
                                    <option value={{$key }} >{{ $material }}</option>
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
                <div class="row bg-dark">
                    <div class="col-md-2">
                        <div class="form-group {{ $errors->has('discount') ? 'has-error' : '' }}">
                            <label for="name">Discount</label>
                            <input type="number" name="discount" id="discount" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group {{ $errors->has('total_price') ? 'has-error' : '' }}">
                            <label for="total_price">Total Price *</label>
                            <input type="text" id="total_price" name="total_price" class="form-control" required>
                            @if($errors->has('total_price'))
                                <em class="invalid-feedback">
                                    {{ $errors->first('total_price') }}
                                </em>
                            @endif
                            <p class="helper-block">
                                {{ trans('cruds.expense.fields.entry_date_helper') }}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group {{ $errors->has('paid_amount') ? 'has-error' : '' }}">
                            <label for="paid_amount">Paid Amount *</label>
                            <input type="number" id="paid_amount" name="paid_amount" class="form-control" >
                            @if($errors->has('paid_amount'))
                                <em class="invalid-feedback">
                                    {{ $errors->first('paid_amount') }}
                                </em>
                            @endif
                            <p class="helper-block">
                                {{ trans('cruds.expense.fields.entry_date_helper') }}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group {{ $errors->has('due_amount') ? 'has-error' : '' }}">
                            <label for="due_amount">Due amount *</label>
                            <input type="number" id="due_amount" name="due_amount" class="form-control">
                            @if($errors->has('due_amount'))
                                <em class="invalid-feedback">
                                    {{ $errors->first('due_amount') }}
                                </em>
                            @endif
                            <p class="helper-block">
                                {{ trans('cruds.expense.fields.entry_date_helper') }}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group {{ $errors->has('payment_process') ? 'has-error' : '' }}">
                            <label for="payment_process">Payment Process *</label>
                            <select name="payment_process" id="payment_process" class="form-control">
                                <option value="">---</option>
                                <option value="bank">Bank</option>
                                <option value="account">Funds</option>
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
                    <div class="col-md-2">
                        <div class="form-group {{ $errors->has('payment_type') ? 'has-error' : '' }}">
                            <label for="payment_type">Select Account *</label>
                            <select name="payment_type" id="payment_type" class="form-control select2 payment_type">

                            </select>
                            @if($errors->has('payment_type'))
                                <em class="invalid-feedback">
                                    {{ $errors->first('payment_type') }}
                                </em>
                            @endif
                            <p class="helper-block">
                                {{ trans('cruds.expense.fields.entry_date_helper') }}
                            </p>
                        </div>
                    </div>
                </div>
                <div>
                    <input class="btn btn-danger mt-2" type="submit" value="Purchase">
                </div>
            </form>


        </div>
    </div>

@endsection

@section('scripts')
    @parent
    <script>
        $("#payment_process").change(function () {

            let payment_type = $(this).val();
            //
            $.ajax({
                url: '/admin/supplier/payment/type/' + payment_type,
                type: 'GET',
                cache: false,
                datatype: 'application/json',

                success: function (data) {
                    console.log(data);

                    var op = '<option value="0" selected>--- Select Account ---</option>';
                    for (var i = 0; i < data.length; i++) {

                        op += '<option value="' + data[i].id + '">' + data[i].name + '</option>';
                    }
                    // set value to the Color
                    $('.payment_type').html("");
                    $('.payment_type').append(op);

                },
                error: function (data) {
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
    </script>

@endsection

@section('scripts')
    @parent
    <script>
        $("#discount").keyup(function (){
            calculateTotal();
        })
        $("#paid").keyup(function (){
            calculateTotal();
        })
        $('#paid_amount').on('keyup', function (e) {
            let paid_amount = $('#paid_amount').val();
            let total_amount = $("#total_price").val();
            let due_amount = total_amount - paid_amount;
            $("#due_amount").val(due_amount);
        })
        function calculateTotal(){
            let total = $("#sub_total").val() - $("#discount").val();
            $("#total_price").val(total);
            let due = total - $("#paid_amount").val()
            $("#due_amount").val(due);
        }


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
            document.getElementById('total_price').value = sum - $("#discount").val();
            document.getElementById('due_amount').value = sum - $("#discount").val() - $("#paid_amount").val() ;

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
            document.getElementById('total_price').value = sum - $("#discount").val();
            document.getElementById('due_amount').value = sum - $("#discount").val() - $("#paid_amount").val() ;


        });
        $(document).on('click', '.remove-from-cart', function() {
            var ele = $(this);
            var row_id = ele.parents("tr").remove();
        });

        $( "#product_id" ).change(function() {

            let material_id = $("#product_id").val();


            $.ajax({
                url: '/admin/add-to-purchase-product/'+material_id,
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


        $("#payment_process").change(function () {

            let payment_type = $(this).val();
            //
            $.ajax({
                url: '/admin/supplier/payment/type/' + payment_type,
                type: 'GET',
                cache: false,
                datatype: 'application/json',

                success: function (data) {
                    console.log(data);

                    var op = '<option value="0" selected>--- Select Account ---</option>';
                    for (var i = 0; i < data.length; i++) {

                        op += '<option value="' + data[i].id + '">' + data[i].name + '</option>';
                    }
                    // set value to the Color
                    $('.payment_type').html("");
                    $('.payment_type').append(op);

                },
                error: function (data) {
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
    </script>

@endsection
