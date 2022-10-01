@extends('layouts.admin')

@section('content')
    <form id="checkout">
        @csrf
        <div class="card">
        <div class="card-body">
            <div id="filter-box">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group {{ $errors->has('material_id') ? 'has-error' : '' }}">
                            <label for="name">Product Name *</label>
                            <select name="product_id" id="product_id" class="form-control select2" >
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
                            <select name="product_color_id" id="product_color_id" class="form-control select2 product_color_id" >

                            </select>
                            @if($errors->has('product_id'))
                                <em class="invalid-feedback">
                                    {{ $errors->first('product_id') }}
                                </em>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-9">
                    <div id="cart-box">
                        <table id="cart" class="table table-hover table-condensed">
                            <thead>
                            <tr>
                                <th style="width:30%">Product</th>
                                <th style="width:30%">Color</th>
                                <th style="width:10%">Price</th>
                                <th style="width:8%">Quantity</th>
                                <th style="width:12%" class="text-center">Subtotal</th>
                                <th style="width:10%">Action</th>
                            </tr>
                            </thead>
                            <tbody>
                            @php $total = 0 @endphp
                            @if(session('cart'))
                                @foreach(session('cart') as $id => $details)
                                    @php $total += $details['price'] * $details['quantity'] @endphp
                                    <tr data-id="{{ $id }}">
                                        <td data-th="Product">
                                            <input type="hidden" name="material_id[]" value="{{ $details['id'] }}">
                                            {{ $details['name'] }}
                                        </td>
                                        <td data-th="Color">
                                            <input type="hidden" name="color_id[]" value="{{ $details['color_id'] }}">
                                            {{ $details['color'] }}
                                        </td>
                                        <td data-th="Price">
                                            <input type="hidden" name="selling_price[]" value="{{ $details['price'] }}">
                                            ${{ $details['price'] }}
                                        </td>
                                        <td data-th="Quantity">
                                            <input type="number" value="{{ $details['quantity'] }}" name="quantity[]" class="form-control quantity update-cart" />
                                        </td>
                                        <td data-th="Subtotal" class="text-center">
                                            <input type="hidden" name="line_total[]" class="sub_total" value="{{ $details['price'] * $details['quantity'] }}">
                                            ${{ $details['price'] * $details['quantity'] }}</td>
                                        <td class="actions" data-th="">
                                            <button class="btn btn-danger btn-sm remove-from-cart"><i class="fa fa-trash-o"></i></button>
                                        </td>
                                    </tr>
                                @endforeach
                            @endif
                            </tbody>
                            <tfoot>
                            <tr>
                                <input type="hidden" name="sub_total" id="sub-total" value="{{ $total }}">
                                <td colspan="5" class="text-right"><h3><strong>Sub Total ${{ $total }}</strong></h3></td>
                            </tr>
                            </tfoot>
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
                    <div class="form-group {{ $errors->has('discount') ? 'has-error' : '' }}">
                        <label for="name">Discount</label>
                        <input type="number" name="discount" id="discount" class="form-control">
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group {{ $errors->has('sub_total') ? 'has-error' : '' }}">
                        <label for="name">Total *</label>
                        <input type="number" name="total" id="total" class="form-control" readonly>
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
                        <input type="number" name="due" id="due" class="form-control" readonly>
                    </div>
                </div>
            </div>
        </div>
            <button class="btn btn-success" type="submit">Checkout</button>
    </div>
    </form>

@endsection

@section('scripts')
<script>

    function calculateTotal(){
        let total = $("#sub-total").val() - $("#discount").val();
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

        function calculateTotal(){
            let total = $("#sub-total").val() - $("#discount").val();
            $("#total").val(total);
            let due = total - $("#paid").val()
            $("#due").val(due);
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
                    Swal.fire({
                        position: 'top-end',
                        icon: 'success',
                        title: 'Product Added to Cart',
                        timer: 1500
                    })
                    // $( "#cart-box" ).load(window.location.href + " #cart-box" );
                    // $( "#filter-box" ).load(window.location.href + " #filter-box" );
                    window.location.reload();

                    let total = $("#sub-total").val() - $("#discount").val();
                    $("#total").val(total);
                    let due = total - $("#paid").val()
                    $("#due").val(due);

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


        $("#checkout").on('submit',function (e){
            e.preventDefault();

            showroomOrder();
        })
        function showroomOrder(){
            $.ajax({
                url:'/admin/showroom/cart/order',
                type:'POST',
                data:$("form#checkout").serialize(),

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
<script type="text/javascript">

    $(".update-cart").change(function (e) {
        e.preventDefault();

        var ele = $(this);

        $.ajax({
            url: '{{ route('admin.update.cart') }}',
            method: "patch",
            data: {
                _token: '{{ csrf_token() }}',
                id: ele.parents("tr").attr("data-id"),
                quantity: ele.parents("tr").find(".quantity").val()
            },
            success: function (response) {
               window.location.reload();
            }
        });
    });

    $(".remove-from-cart").click(function (e) {
        e.preventDefault();

        var ele = $(this);

        if(confirm("Are you sure want to remove?")) {
            $.ajax({
                url: '{{ route('admin.remove.from.cart') }}',
                method: "DELETE",
                data: {
                    _token: '{{ csrf_token() }}',
                    id: ele.parents("tr").attr("data-id")
                },
                success: function (response) {
                    window.location.reload();
                }
            });
        }
    });

</script>
@endsection
