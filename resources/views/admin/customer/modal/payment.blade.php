
    <div class="card-title">
       <h3>Make Payment</h3>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <tbody>
                <tr>
                    <th>Supplier Name</th>
                    <td>{{$customer_detail->name}}</td>
                </tr>
            </tbody>
        </table>
        <form id="payment-form">
            @csrf
            <div class="form-group {{ $errors->has('total_amount') ? 'has-error' : '' }}">
                    <label for="total_amount">Total Amount </label>
                    <input type="hidden" name="customer_id" value="{{$customer_detail->id}}">
                    <input type="number" id="total_amount" name="total_amount" class="form-control" value="{{$all_dues}}" readonly>
            </div>
            <div class="form-group {{ $errors->has('paid_amount') ? 'has-error' : '' }}">
                    <label for="paid_amount">Paid Amount *</label>
                    <input type="number" id="paid_amount" name="paid_amount" class="form-control" required>
                    @if($errors->has('paid_amount'))
                        <em class="invalid-feedback">
                            {{ $errors->first('paid_amount') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('cruds.expense.fields.entry_date_helper') }}
                    </p>
            </div>

            <div class="form-group {{ $errors->has('due_amount') ? 'has-error' : '' }}">
                <label for="due_amount">Due amount *</label>
                <input type="number" id="due_amount" name="due_amount" class="form-control" value="" required readonly>
                @if($errors->has('due_amount'))
                    <em class="invalid-feedback">
                        {{ $errors->first('due_amount') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.expense.fields.entry_date_helper') }}
                </p>
            </div>
{{--            <div class="form-group {{ $errors->has('payment_process') ? 'has-error' : '' }}">--}}
{{--                    <label for="payment_process">Payment Process *</label>--}}
{{--                    <select name="payment_process" id="payment_process" class="form-control" required>--}}
{{--                        <option value="">---</option>--}}
{{--                        <option value="bank">Bank</option>--}}
{{--                        <option value="bkash">Bkash</option>--}}
{{--                        <option value="cash">Cash</option>--}}
{{--                    </select>--}}
{{--                    @if($errors->has('payment_process'))--}}
{{--                        <em class="invalid-feedback">--}}
{{--                            {{ $errors->first('payment_process') }}--}}
{{--                        </em>--}}
{{--                    @endif--}}
{{--                    <p class="helper-block">--}}
{{--                        {{ trans('cruds.expense.fields.entry_date_helper') }}--}}
{{--                    </p>--}}
{{--            </div>--}}

{{--            <div class="form-group {{ $errors->has('payment_info') ? 'has-error' : '' }}">--}}
{{--                <label for="payment_info">Payment Info </label>--}}
{{--                <input type="text" id="payment_info" name="payment_info" class="form-control" required>--}}
{{--                @if($errors->has('payment_info'))--}}
{{--                    <em class="invalid-feedback">--}}
{{--                        {{ $errors->first('payment_info') }}--}}
{{--                    </em>--}}
{{--                @endif--}}
{{--                <p class="helper-block">--}}
{{--                    {{ trans('cruds.expense.fields.entry_date_helper') }}--}}
{{--                </p>--}}
{{--            </div>--}}
            <button id="return_stock_to_main" type="submit" class="btn btn-primary pull-right">Paid</button>
        </form>
    </div>
    <script>
        $(document).ready(function () {
        $('#paid_amount').on('keyup',function(e){
                let paid_amount = $('#paid_amount').val();
                let total_amount = $("#total_amount").val();
                let due_amount = total_amount - paid_amount;
                $("#due_amount").val(due_amount);
        })
        $('form#payment-form').on('submit', function (e) {
            e.preventDefault();
                searchStockSet();
        });

        function searchStockSet(){
            $.ajax({
                url: '/admin/customer/payment/store',
                type: 'POST',
                cache: false,
                data: $('form#payment-form').serialize(),
                datatype: 'html',
                // datatype: 'application/json',

                beforeSend: function() {
                    // show waiting dialog
                    // waitingDialog.show('Loading...');
                },

                success:function(data){
                    console.log(data);
                    // if(data) {
                    //     if(data.status == 103){
                    //         Swal.fire({
                    //             icon: 'error',
                    //             title: 'Oops...',
                    //             text: data.message,
                    //             footer: 'Check your Stock'
                    //         })
                    //     }
                    //     else{
                    //         Swal.fire({
                    //             position: 'top-end',
                    //             icon: 'success',
                    //             title: 'Payment Successfully Done',
                    //             showConfirmButton: false,
                    //             timer: 1500
                    //         })
                    //         $('#mediumModal').modal('hide');
                    //         window.location.reload();
                    //
                    //     }
                    // }
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
    });

    </script>
