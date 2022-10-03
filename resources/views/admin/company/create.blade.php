@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.create') }} Company
    </div>

    <div class="card-body">
        <form action="{{ route("admin.company.store") }}" method="POST" enctype="multipart/form-data">
            @csrf
            <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                <label for="name">{{ trans('cruds.user.fields.name') }}*</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name', isset($user) ? $user->name : '') }}" required>
                @if($errors->has('name'))
                    <em class="invalid-feedback">
                        {{ $errors->first('name') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.user.fields.name_helper') }}
                </p>
            </div>
            <div class="form-group {{ $errors->has('phone') ? 'has-error' : '' }}">
                <label for="phone">Phone *</label>
                <input type="text" id="phone" name="phone" class="form-control" value="{{ old('phone', isset($employee) ? $employee->phone : '') }}" required>
                @if($errors->has('phone'))
                    <em class="invalid-feedback">
                        {{ $errors->first('phone') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.user.fields.email_helper') }}
                </p>
            </div>
            <div class="form-group {{ $errors->has('address') ? 'has-error' : '' }}">
                <label for="address">address *</label>
                <input type="text" id="address" name="address" class="form-control" value="{{ old('address', isset($employee) ? $employee->address : '') }}" required>
                @if($errors->has('address'))
                    <em class="invalid-feedback">
                        {{ $errors->first('address') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.user.fields.email_helper') }}
                </p>
            </div>
            <div class="form-group {{ $errors->has('type') ? 'has-error' : '' }}">
                <label for="type">Type *
                </label>
                <select name="type" id="type" class="form-control select2"  required>
                        <option value="1">Self</option>
                        <option value="2">Others</option>
                </select>
                @if($errors->has('type'))
                    <em class="invalid-feedback">
                        {{ $errors->first('type') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.user.fields.roles_helper') }}
                </p>
            </div>

            <div class="form-group">
                <label>Opening Balance</label>
                <input type="number" name="opening_balance" class="form-control" >
            </div>

            <div>
                <input class="btn btn-danger" type="submit" value="{{ trans('global.save') }}">
            </div>
        </form>


    </div>
</div>
@endsection
