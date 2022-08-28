<h4>Transfer to Dyeing</h4>
@php
    $rest_qty = $rest_quantity->sum('rest_quantity');

    @endphp
<table class="table table-bordered">
    <tbody>
    <tr>
        <th>Total Quantity</th>
        <td>{{$rest_qty}}</td>
    </tr>
    <tr>
        <th>Transfer to</th>
        <td>Dyeing</td>
    </tr>
    <tr>
        <th>Transfer Quantity</th>
        <td><input type="number"></td>
    </tr>
    </tbody>
</table>
<h6>Total Quantity {{$rest_qty}}</h6>


<div class="card">
    <div class="card-header">
        Stock In
    </div>
    <div class="card-body">
        <form id = "search_stock_form">
            <input type="hidden" name="_token" value="{{csrf_token()}}">

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
                    <div class="form-group {{ $errors->has('company_id') ? 'has-error' : '' }}">
                        <label for="name">Company Name *</label>
                        <select name="company_id" id="company_id" class="form-control select2" required>
                            @foreach($companies as $id => $company)
                                <option value="{{ $id }}" >{{ $company }}</option>
                            @endforeach
                        </select>
                        @if($errors->has('company_id'))
                            <em class="invalid-feedback">
                                {{ $errors->first('company_id') }}
                            </em>
                        @endif
                    </div>
                </div>

                <div class="col-md-2">
                    <div class="form-group {{ $errors->has('quantity') ? 'has-error' : '' }}">
                        <label for="name"> Quantity *</label>
                        <input type="text" id="quantity" name="quantity" class="form-control" required>
                        <input type="hidden" id="type" name="type" value="1">
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
                    <label for=""></label>
                    <button id="teacher_list_search_btn" type="submit" class="btn btn-primary pull-right">Search</button>
                </div>
            </div>
        </form>
        <div id="stock_list_container"></div>
    </div>
</div>

