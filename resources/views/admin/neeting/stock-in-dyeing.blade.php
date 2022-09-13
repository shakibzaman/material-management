@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        <b>Transfer to Dyeing</b>
    </div>
    @php
        $total = $knittingStock->sum('rest_quantity');
    @endphp
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
        <form id = "search_stock_form">
            <input type="hidden" name="_token" value="{{csrf_token()}}">
            <input type="hidden" name="company_id" value="{{$company_id}}">

            <div class="row">
                <div class="col-md-2">
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
                <div class="col-md-2">
                    <div class="form-group {{ $errors->has('quantity') ? 'has-error' : '' }}">
                        <label for="name"> Quantity *</label>
                        <input type="number" id="quantity" name="quantity" class="form-control" required>
                            @if($errors->has('quantity'))
                                <em class="invalid-feedback">
                                    {{ $errors->first('quantity') }}
                                </em>
                            @endif
                        <p class="helper-block">
                            {{ trans('cruds.expense.fields.entry_date_helper') }}
                        </p>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group {{ $errors->has('process_fee') ? 'has-error' : '' }}">
                        <label for="name"> Process Cost/KG *</label>
                        <input type="number" id="process_fee" name="process_fee" class="form-control" required>
                        <input type="hidden" id="type" name="type" value="2">
                        <input type="hidden" id="company_id" name="company_id" value="{{$company_id}}">
                            @if($errors->has('process_fee'))
                                <em class="invalid-feedback">
                                    {{ $errors->first('process_fee') }}
                                </em>
                            @endif
                        <p class="helper-block">
                            {{ trans('cruds.expense.fields.entry_date_helper') }}
                        </p>
                    </div>
                </div>
                <div class="col-md-2">
                    <label for=""></label>
                    <button id="teacher_list_search_btn" type="submit" class="btn btn-primary pull-right">Stock In</button>
                </div>
            </div>
        </form>
        <div id="stock_list_container"></div>

    </div>
</div>
@stop
@section('scripts')
<script>
        jQuery(document).ready(function () {
            $('form#search_stock_form').on('submit', function (e) {
                e.preventDefault();
                    // ajax request
                searchStockSet();
            });

            function searchStockSet(){
                $.ajax({
                    url: '/admin/neeting/dyeing/transfer/company/product',
                    type: 'POST',
                    cache: false,
                    data: $('form#search_stock_form').serialize(),
                    datatype: 'html',
                    // datatype: 'application/json',

                    beforeSend: function() {
                        // show waiting dialog
                        // waitingDialog.show('Loading...');
                    },

                    success:function(data){
                        console.log(data);
                        // if(data) {
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
                                title: 'Data Transferred to Dyeing Successfully ',
                                showConfirmButton: false,
                                timer: 1500
                            })
                            window.location = '{{ route('admin.dyeing.index') }}'
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
@stop
