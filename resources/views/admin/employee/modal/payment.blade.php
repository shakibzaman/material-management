
    <div class="card-title">
       <h3>Make Payment</h3>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <tbody>
                <tr>
                    <th>Employee Name</th>
                    <td>{{$employee->name}}</td>
                </tr>
            </tbody>
        </table>
        <form id="payment-form">
            @csrf
            <div class="form-group {{ $errors->has('total_amount') ? 'has-error' : '' }}">
                    <label for="total_amount">Salary Amount </label>
                    <input type="hidden" name="employee_id" value="{{$employee->id}}">
                    <input type="number" id="total_amount" name="total_amount" class="form-control" value="{{$employee->salary}}" readonly>
            </div>
            <div class="form-group {{ $errors->has('paid_amount') ? 'has-error' : '' }}">
                    <label for="paid_amount">Paid Amount *</label>
                    <input type="number" id="paid_amount" name="paid_amount" class="form-control" value="{{$employee->salary}}">
                    @if($errors->has('paid_amount'))
                        <em class="invalid-feedback">
                            {{ $errors->first('paid_amount') }}
                        </em>
                    @endif
                    <p class="helper-block">
                        {{ trans('cruds.expense.fields.entry_date_helper') }}
                    </p>
            </div>
            <div class="form-group {{ $errors->has('date') ? 'has-error' : '' }}">
                <label for="due_amount">Date *</label>
                <input type="date" id="date" name="date" class="form-control" value="">
                @if($errors->has('date'))
                    <em class="invalid-feedback">
                        {{ $errors->first('date') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.expense.fields.entry_date_helper') }}
                </p>
            </div>
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
                url: '/admin/employee/payment/store',
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
                                title: 'Payment Successfully Done',
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
    });

    </script>
