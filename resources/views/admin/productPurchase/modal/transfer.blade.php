<div>
    <form action="">
        <div class="form-group {{ $errors->has('material_id') ? 'has-error' : '' }}">
            <label for="name">Department</label>
            <select name="department_id" id="department_id" class="form-control select2">
                @foreach($departments as $id => $departments)
                    <option value="{{ $id }}" >{{ $departments }}</option>
                @endforeach
            </select>
            @if($errors->has('department_id'))
                <em class="invalid-feedback">
                    {{ $errors->first('department_id') }}
                </em>
            @endif
        </div>
    </form>
    @isset($materials)
        {{$materials}}
    @endisset
</div>