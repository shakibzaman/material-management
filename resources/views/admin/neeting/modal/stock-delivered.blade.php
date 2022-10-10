@php
    $total = $knittingStock->sum('rest_quantity');
    @endphp
<div class="card-title">
    <h5>Delivered stock</h5>
</div>

    <div class="card-body">
        <table class="table table-bordered">
            <tbody>
            <tr>
                <th>Available Stock</th>
                <td>{{$total}}</td>
                <input type="hidden" name="total_stock" value="{{$total}}">

            </tr>
            @foreach($rest_quantity as $key=>$quantity)
                <tr>
                    <th>{{$material_key_by[$key]->name}}</th>
                    <td>{{$quantity->sum('rest_quantity')}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <form id = "delivered_stock">
            <input type="hidden" name="_token" value="{{csrf_token()}}">
            <input type="hidden" name="company_id" value="{{$company_id}}">
            <input type="hidden" name="type" value="{{$type}}">
            <table class="table table-bordered">
            <tr>
                <th>Delivered Stock</th>
                <td>
                    <div class="form-group {{ $errors->has('material_id') ? 'has-error' : '' }}">
                        <label for="name">Product Name *</label>
                        <select name="product_id" id="product_id" class="form-control select2" required>
                            @foreach($product_list as $id => $product)
                                <option value="{{ $id }}" >{{ $product }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('product_id'))
                            <em class="invalid-feedback">
                                {{ $errors->first('product_id') }}
                            </em>
                        @endif
                    </div>
                    <div class="form-group {{ $errors->has('transfer_stock') ? 'has-error' : '' }}">
                        <label for="name"> Enter Delivered Quantity *</label>
                        <input type="number" id="transfer_stock" name="transfer_stock" class="form-control" placeholder="Enter Quantity" required>
                        <input type="hidden" id="type" name="type" value="1">
                        @if($errors->has('transfer_stock'))
                            <em class="invalid-feedback">
                                {{ $errors->first('transfer_stock') }}
                            </em>
                        @endif
                        <p class="helper-block">
                            {{ trans('cruds.expense.fields.entry_date_helper') }}
                        </p>
                    </div>
                    <div class="form-group {{ $errors->has('transfer_stock') ? 'has-error' : '' }}">
                        <label for="name"> Enter Bill/Unit *</label>
                        <input type="number" id="bill" name="bill" class="form-control" placeholder="Enter Bill" required>
                        @if($errors->has('reason'))
                            <em class="invalid-feedback">
                                {{ $errors->first('bill') }}
                            </em>
                        @endif
                        <p class="helper-block">
                            {{ trans('cruds.expense.fields.entry_date_helper') }}
                        </p>
                    </div>
                    <div class="form-group {{ $errors->has('bill_total') ? 'has-error' : '' }}">
                        <label for="name"> Total Bill *</label>
                        <input type="text" id="bill-total" name="bill_total" class="form-control" value="" required readonly>
                        @if($errors->has('bill_total'))
                            <em class="invalid-feedback">
                                {{ $errors->first('bill_total') }}
                            </em>
                        @endif
                        <p class="helper-block">
                            {{ trans('cruds.expense.fields.entry_date_helper') }}
                        </p>
                    </div>

                </td>
            </tr>
        </table>
            <div class="row">

                <div class="col-md-3">
                    <div class="form-group {{ $errors->has('discount') ? 'has-error' : '' }}">
                        <label for="name">Discount</label>
                        <input type="number" name="discount" id="discount" class="form-control">
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
                        <input type="number" name="due" id="due" class="form-control" readonly>
                    </div>
                </div>

                <div class="col-md-3">
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

                <div class="col-md-4">
                    <div class="form-group {{ $errors->has('payment_process') ? 'has-error' : '' }}">
                        <label for="payment_process">Payment Process *</label>
                        <select name="payment_process" id="payment_process" class="form-control" required>
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
                <div class="col-md-4">
                    <div class="form-group {{ $errors->has('payment_type') ? 'has-error' : '' }}">
                        <label for="payment_type">Select Account *</label>
                        <select name="payment_type" id="payment_type" class="form-control select2 payment_type" >

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


                <div class="col-md-4">
                    <div class="form-group {{ $errors->has('payment_info') ? 'has-error' : '' }}">
                        <label for="payment_info">Payment Info </label>
                        <input type="text" id="payment_info" name="payment_info" class="form-control">
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
            <div id="bill-preview"></div>
            <button id="return_stock_to_main" type="submit" class="btn btn-primary pull-right">Delivered</button>
        </form>
    </div>
<script>
    $( "#payment_process" ).change(function() {

        let payment_type = $(this).val();
        //
        $.ajax({
            url: '/admin/supplier/payment/type/'+payment_type,
            type: 'GET',
            cache: false,
            datatype: 'application/json',

            success:function(data){
                console.log(data);

                var op ='<option value="0" selected>--- Select Account ---</option>';
                for(var i=0;i<data.length;i++){

                    op+='<option value="'+data[i].id+'">'+data[i].name+'</option>';
                }
                // set value to the Color
                $('.payment_type').html("");
                $('.payment_type').append(op);

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
    $("#transfer_stock").change(function (){
        console.log($(this).val());

            $.ajax({
                url: '/admin/knitting/delivered/stock/check',
                type: 'POST',
                cache: false,
                data: $('form#delivered_stock').serialize(),
                datatype: 'html',
                // datatype: 'application/json',

                beforeSend: function() {
                    // show waiting dialog
                    // waitingDialog.show('Loading...');
                },

                success:function(data){
                    console.log(data);
                    $("#bill-preview").html('');
                    $("#bill-preview").append(data);
                },
                error:function(data){
                    console.log(data);
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

    })
    $("#bill").change(function (){
        let bill = $(this).val();
        let stock = $("#transfer_stock").val();
        let total_bill = bill * stock;
        $("#bill-total").val(total_bill);
        $("#due").val(total_bill);


    })
    $("#discount").change(function (){
        let discount = $(this).val();
        let total = $("#bill-total").val();
        let due_bill = total - discount;
        $("#due").val(due_bill);


    })
    $("#paid").change(function (){
        let paid = $(this).val();
        let total = $("#bill-total").val();
        let discount = $("#discount").val();
        let due_bill = total - discount - paid;
        $("#due").val(due_bill);


    })
    jQuery(document).ready(function () {
        $('form#delivered_stock').on('submit', function (e) {
            e.preventDefault();
                searchStockSet();
        });

        function searchStockSet(){
            $.ajax({
                url: '/admin/knitting/delivered/stock',
                type: 'POST',
                cache: false,
                data: $('form#delivered_stock').serialize(),
                datatype: 'html',
                // datatype: 'application/json',

                beforeSend: function() {
                    // show waiting dialog
                    // waitingDialog.show('Loading...');
                },

                success:function(data){
                    console.log(data);
                    if(data) {
                        if(data.status == 103){
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: data.message,
                                footer: 'Check your Stock'
                            })
                        }
                        else{
                            Swal.fire({
                                position: 'top-end',
                                icon: 'success',
                                title: 'Product Successfully Delivered',
                                showConfirmButton: false,
                                timer: 1500
                            })
                            $('#mediumModal').modal('hide');
                            window.location.reload();

                        }
                    }
                },
                error:function(data){
                    console.log(data);
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
        }
    })
</script>

