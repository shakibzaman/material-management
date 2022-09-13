@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        Stock Set
    </div>
    <div class="card-body">
        <h4>Add Set</h4>
        <form id = "material_stock_in">
            <input type="hidden" name="_token" value="{{csrf_token()}}">
            <div class="row">
                <div class="col-md-2">
                    <div class="form-group {{ $errors->has('material_id') ? 'has-error' : '' }}">
                        <label for="name">Product Name *</label>
                        <select name="product_id" class="form-control select2" required>
                            @foreach($products as $key => $product)
                                <option value="{{ $key }}" >{{ $product }}</option>
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
                    <div class="form-group {{ $errors->has('color_id') ? 'has-error' : '' }}">
                        <label for="name">Color Name *</label>
                        <select name="color_id" class="form-control select2" required>
                            @foreach($colors as $key => $color)
                                <option value="{{ $key }}" >{{ $color }}</option>
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
                        <label for="name"> Start Quantity *</label>
                        <input type="number" id="quantity" name="start_quantity" class="form-control" required>
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
                    <div class="form-group {{ $errors->has('quantity') ? 'has-error' : '' }}">
                        <label for="name">End Quantity *</label>
                        <input type="number" id="quantity" name="end_quantity" class="form-control" required>
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
            </div>
            @foreach($materials as $id=> $material)
            <div class="form-group {{ $errors->has('quantity') ? 'has-error' : '' }}">
                <label for="">{{$material->name}}</label>
                <input type="number" name="material_qty[{{$material->id}}]" class="form-control">
            </div>
            @endforeach
            <div>
                <button type="submit" class="btn btn-primary submit" data-btn-name="generate">Make Set</button>
            </div>
        </form>
    </div>
</div>
@stop
@section('scripts')
<script>
    jQuery(document).ready(function () {
            $('form#material_stock_in').on('submit', function (e) {
                e.preventDefault();
                    // ajax request
                searchStockSet();
            });

            function searchStockSet(){
                $.ajax({
                    url: '/admin/store/set',
                    type: 'POST',
                    cache: false,
                    data: $('form#material_stock_in').serialize(),
                    datatype: 'html',
                    // datatype: 'application/json',

                    beforeSend: function() {

                    },

                    success:function(data){
                        console.log(data);
                        if(data.status == 200){
                            Swal.fire({
                                position: 'top-end',
                                icon: 'success',
                                title: 'Data Transferred Successfully ',
                                showConfirmButton: false,
                                timer: 1500
                            })
                            window.location = '{{ route('admin.stock.set') }}'
                        }

                    },
                    error:function(data){
                        console.log(data);
                    }
                });
            }
        })
</script>
@stop
