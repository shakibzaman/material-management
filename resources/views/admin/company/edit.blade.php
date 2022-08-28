@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.edit') }} {{ trans('cruds.user.title_singular') }}
    </div>

    <div class="card-body">
        <form action="{{ route("admin.employee.update", [$employee->id]) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            <div class="form-group {{ $errors->has('name') ? 'has-error' : '' }}">
                <label for="name">{{ trans('cruds.user.fields.name') }}*</label>
                <input type="text" id="name" name="name" class="form-control" value="{{ old('name', isset($employee) ? $employee->name : '') }}" required>
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
            <div class="form-group {{ $errors->has('nid') ? 'has-error' : '' }}">
                <label for="nid">NID *</label>
                <input type="text" id="nid" name="nid" class="form-control" value="{{ old('nid', isset($employee) ? $employee->nid : '') }}" required>
                @if($errors->has('nid'))
                    <em class="invalid-feedback">
                        {{ $errors->first('nid') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.user.fields.email_helper') }}
                </p>
            </div>
            <div class="form-group {{ $errors->has('nid') ? 'has-error' : '' }}">
                <label for="reference">Reference *</label>
                <input type="text" id="reference" name="reference" class="form-control" value="{{ old('reference', isset($employee) ? $employee->reference : '') }}" required>
                @if($errors->has('reference'))
                    <em class="invalid-feedback">
                        {{ $errors->first('reference') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.user.fields.email_helper') }}
                </p>
            </div>
            <div class="form-group {{ $errors->has('roles') ? 'has-error' : '' }}">
                <label for="department">Department *
                    <!-- <span class="btn btn-info btn-xs select-all">{{ trans('global.select_all') }}</span>
                    <span class="btn btn-info btn-xs deselect-all">{{ trans('global.deselect_all') }}</span> -->
                </label>
                <select name="department_id" id="department_id" class="form-control select2"  required>
                    @foreach($departments as $id => $department)
                        <option value="{{ $id }}" {{ (isset($employee) && $employee->department ? $employee->department->id : old('department_id')) == $id ? 'selected' : ''}} >{{ $department }}</option>
                    @endforeach
                </select>
                @if($errors->has('department_id'))
                    <em class="invalid-feedback">
                        {{ $errors->first('department_id') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.user.fields.roles_helper') }}
                </p>
            </div>
            <div class="form-group {{ $errors->has('designation') ? 'has-error' : '' }}">
                <label for="designation">Designation *</label>
                <input type="text" id="designation" name="designation" class="form-control" value="{{ old('designation', isset($employee) ? $employee->designation : '') }}" required>
                @if($errors->has('designation'))
                    <em class="invalid-feedback">
                        {{ $errors->first('designation') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.user.fields.email_helper') }}
                </p>
            </div>
            <div class="form-group {{ $errors->has('education') ? 'has-error' : '' }}">
                <label for="education">Education *</label>
                <input type="text" id="education" name="education" class="form-control" value="{{ old('education', isset($employee) ? $employee->education : '') }}" required>
                @if($errors->has('education'))
                    <em class="invalid-feedback">
                        {{ $errors->first('education') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.user.fields.email_helper') }}
                </p>
            </div>
            <div class="form-group {{ $errors->has('salary') ? 'has-error' : '' }}">
                <label for="salary">Salary *</label>
                <input type="number" id="salary" name="salary" class="form-control" value="{{ old('salary', isset($employee) ? $employee->salary : '') }}" required>
                @if($errors->has('salary'))
                    <em class="invalid-feedback">
                        {{ $errors->first('salary') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.user.fields.email_helper') }}
                </p>
            </div>
            <div class="form-group {{ $errors->has('joining_date') ? 'has-error' : '' }}">
                <label for="joining_date">Joining Date *</label>
                <input type="date" id="joining_date" name="joining_date" class="form-control" value="{{ old('joining_date', isset($employee) ? $employee->joining_date : '') }}" required>
                @if($errors->has('joining_date'))
                    <em class="invalid-feedback">
                        {{ $errors->first('joining_date') }}
                    </em>
                @endif
                <p class="helper-block">
                    {{ trans('cruds.user.fields.email_helper') }}
                </p>
            </div>
            <div>
                <input class="btn btn-danger" type="submit" value="{{ trans('global.save') }}">
            </div>
        </form>


    </div>
</div>
@endsection