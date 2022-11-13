@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} {{ trans('cruds.expense.title_singular') }}
    </div>

    <div class="card-body">
        <form id="expense-form">
            @csrf
            <div class="form-group {{ $errors->has('expense_category_id') ? 'has-error' : '' }}">
                <label for="expense_category">{{ trans('cruds.expense.fields.expense_category') }}</label>
                <select name="expense_category_id" id="expense_category" class="form-control select2">
                    @foreach($expense_categories as $id => $expense_category)
                        <option value="{{ $id }}" {{ (isset($expense) && $expense->expense_category ? $expense->expense_category->id : old('expense_category_id')) == $id ? 'selected' : '' }}>{{ $expense_category }}</option>
                    @endforeach
                </select>
                @if($errors->has('expense_category_id'))
                    <em class="invalid-feedback">
                        {{ $errors->first('expense_category_id') }}
                    </em>
                @endif
            </div>
            <div class="form-group {{ $errors->has('expense_category_id') ? 'has-error' : '' }}">
                <label for="expense_category">Department</label>
                <select name="department_id" id="expense_category" class="form-control select2">
                    @foreach($departments as $id => $department)
                        <option value="{{ $id }}" >{{ $department }}</option>
                    @endforeach
                </select>
                @if($errors->has('department_id'))
                    <em class="invalid-feedback">
                        {{ $errors->first('department_id') }}
                    </em>
                @endif
            </div>
            <div class="form-group {{ $errors->has('entry_date') ? 'has-error' : '' }}">
                <label for="entry_date">{{ trans('cruds.expense.fields.entry_date') }}*</label>
                <input type="text" id="entry_date" name="entry_date" class="form-control date" value="{{ old('entry_date', isset($expense) ? $expense->entry_date : '') }}" required>
                @if($errors->has('entry_date'))
                    <em class="invalid-feedback">
                        {{ $errors->first('entry_date') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.expense.fields.entry_date_helper') }}
                </p>
            </div>
            <div class="form-group {{ $errors->has('amount') ? 'has-error' : '' }}">
                <label for="amount">{{ trans('cruds.expense.fields.amount') }}*</label>
                <input type="number" id="amount" name="amount" class="form-control" value="{{ old('amount', isset($expense) ? $expense->amount : '') }}" step="0.01" required>
                @if($errors->has('amount'))
                    <em class="invalid-feedback">
                        {{ $errors->first('amount') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.expense.fields.amount_helper') }}
                </p>
            </div>
            <div class="form-group {{ $errors->has('description') ? 'has-error' : '' }}">
                <label for="description">{{ trans('cruds.expense.fields.description') }}</label>
                <input type="text" id="description" name="description" class="form-control" value="{{ old('description', isset($expense) ? $expense->description : '') }}">
                @if($errors->has('description'))
                    <em class="invalid-feedback">
                        {{ $errors->first('description') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.expense.fields.description_helper') }}
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
            <div>
                <input class="btn btn-danger" type="submit" value="{{ trans('global.save') }}">
            </div>
        </form>
    </div>
</div>
@endsection
@section('scripts')
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


        $('form#expense-form').on('submit', function (e) {
            e.preventDefault();
            searchStockSet();
        });

        function searchStockSet(){
            $.ajax({
                url: '/admin/expenses',
                type: 'POST',
                cache: false,
                data: $('form#expense-form').serialize(),
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
                        else if(data.status == 200){
                            Swal.fire({
                                position: 'top-end',
                                icon: 'success',
                                title: data.message,
                                showConfirmButton: false,
                                timer: 1500
                            })
                            // $('#mediumModal').modal('hide');
                            window.location.href='/admin/expenses';

                        }
                        else{
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: "Unable to load data form server",
                                footer: 'Contact with Your Admin'
                            })
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
    </script>
@endsection
