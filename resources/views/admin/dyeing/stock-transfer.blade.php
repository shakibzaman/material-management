@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        Stock Transfer
    </div>
    <div class="card-body">
        <h4>Dyeing Process & Transfer to Showroom</h4>

        <table class="table table-bordered">
            <tbody>
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
                    <div class="form-group {{ $errors->has('material_id') ? 'has-error' : '' }}">
                        <label for="name">Showroom Name *</label>
                        <select name="showroom_id" id="showroom_id" class="form-control select2" required>
                            @foreach($showrooms as $id => $showroom)
                                <option value="{{ $id }}" >{{ $showroom }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('showroom_id'))
                            <em class="invalid-feedback">
                                {{ $errors->first('showroom_id') }}
                            </em>
                        @endif
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group {{ $errors->has('color_id') ? 'has-error' : '' }}">
                        <label for="name">Color Name *</label>
                        <select name="color_id" id="color_id" class="form-control select2" required>
                            @foreach($colors as $id => $color)
                                <option value="{{ $id }}" >{{ $color }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('color_id'))
                            <em class="invalid-feedback">
                                {{ $errors->first('color_id') }}
                            </em>
                        @endif
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group {{ $errors->has('quantity') ? 'has-error' : '' }}">
                        <label for="name"> Quantity *</label>
                        <input type="number" id="quantity" name="quantity" class="form-control" required>
                        <input type="hidden" id="type" name="type" value="3">
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
                    <button id="teacher_list_search_btn" type="submit" class="btn btn-primary pull-right">Search</button>
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
                //     // ajax request
                searchStockSet();
            });

            function searchStockSet(){
                $.ajax({
                    url: '/admin/dyeing/stock/search',
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
                        if(data) {
                            if(data.status == 103){
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: data.message,
                                footer: 'Check your Stock'
                            })
                            $('#stock_list_container').html('');
                        }
                        else{
                            Swal.fire({
                                position: 'top-end',
                                icon: 'success',
                                title: 'Enter Material Quantity',
                                showConfirmButton: false,
                                timer: 1500
                            })
                            $('#stock_list_container').html('');
                            $('#stock_list_container').append(data);
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
@stop
