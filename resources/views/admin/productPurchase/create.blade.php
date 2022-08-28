@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        Purchase Products
    </div>

    <div class="card-body">
        <form action="{{ route("admin.material-in.store") }}" method="POST" enctype="multipart/form-data">
            @csrf

            <div class="form-group {{ $errors->has('material_id') ? 'has-error' : '' }}">
                <label for="name">Material Name</label>
                <select name="material_id" id="material_id" class="form-control select2">
                    @foreach($materials as $id => $material)
                        <option value="{{ $id }}" >{{ $material }}</option>
                    @endforeach
                </select>
                @if($errors->has('material_id'))
                    <em class="invalid-feedback">
                        {{ $errors->first('material_id') }}
                    </em>
                @endif
            </div>

            <div class="form-group {{ $errors->has('quantity') ? 'has-error' : '' }}">
                <label for="name"> Quantity *</label>
                <input type="text" id="quantity" name="quantity" class="form-control" required>
                <input type="hidden" id="type" name="type" value="2">
                @if($errors->has('quantity'))
                    <em class="invalid-feedback">
                        {{ $errors->first('quantity') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.expense.fields.entry_date_helper') }}
                </p>
            </div>

            <div class="form-group {{ $errors->has('unit') ? 'has-error' : '' }}">
                <label for="employee_id">Unit</label>
                <select name="unit" id="unit" class="form-control select2">
                    @foreach($units as $id=>$unit)
                        <option value="{{$id}}" >{{$unit}}</option>
                    @endforeach
                </select>
                @if($errors->has('unit'))
                    <em class="invalid-feedback">
                        {{ $errors->first('unit') }}
                    </em>
                @endif
            </div>

            <div class="form-group {{ $errors->has('buying_date') ? 'has-error' : '' }}">
                <label for="buying_date">Buying Date *</label>
                <input type="text" id="buying_date" name="buying_date" class="form-control date" required>
                @if($errors->has('buying_date'))
                    <em class="invalid-feedback">
                        {{ $errors->first('buying_date') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.expense.fields.entry_date_helper') }}
                </p>
            </div>

            <div class="form-group {{ $errors->has('unit_price') ? 'has-error' : '' }}">
                <label for="unit_price">Unit Price *</label>
                <input type="text" id="unit_price" name="unit_price" class="form-control" required>
                @if($errors->has('unit_price'))
                    <em class="invalid-feedback">
                        {{ $errors->first('unit_price') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.expense.fields.entry_date_helper') }}
                </p>
            </div>

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
            <div class="form-group {{ $errors->has('supplied_by') ? 'has-error' : '' }}">
                <label for="supplied_by">Supplied by *</label>
                <input type="text" id="supplied_by" name="supplied_by" class="form-control" required>
                @if($errors->has('supplied_by'))
                    <em class="invalid-feedback">
                        {{ $errors->first('supplied_by') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.expense.fields.entry_date_helper') }}
                </p>
            </div>
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

            <div class="form-group {{ $errors->has('supplied_by') ? 'has-error' : '' }}">
                <label for="purchased_by">Buying By</label>
                <select name="purchased_by" id="purchased_by" class="form-control select2">
                    @foreach($employees as $id => $employee)
                        <option value="{{ $id }}" >{{ $employee }}</option>
                    @endforeach
                </select>
                @if($errors->has('purchased_by'))
                    <em class="invalid-feedback">
                        {{ $errors->first('purchased_by') }}
                    </em>
                @endif
            </div>

            <div>
                <input class="btn btn-danger" type="submit" value="{{ trans('global.save') }}">
            </div>
        </form>


    </div>
</div>
@endsection