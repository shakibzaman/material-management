@extends('layouts.admin')
@section('content')
@can('expense_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route("admin.expenses.create") }}">
                {{ trans('global.add') }} {{ trans('cruds.expense.title_singular') }}
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        {{ trans('cruds.expense.title_singular') }} {{ trans('global.list') }}
    </div>
    <div class="filter-box">
        <form id="search-filter">
            @csrf
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="start_date">Start Date*</label>
                            <input type="date" id="start_date" name="start_date" class="form-control" required>
                            @if($errors->has('start_date'))
                                <em class="invalid-feedback">
                                    {{ $errors->first('start_date') }}
                                </em>
                            @endif
                            <p class="helper-block">
                                {{ trans('cruds.expense.fields.amount_helper') }}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="start_date">End Date*</label>
                            <input type="date" id="end_date" name="end_date" class="form-control" required>
                            @if($errors->has('start_date'))
                                <em class="invalid-feedback">
                                    {{ $errors->first('start_date') }}
                                </em>
                            @endif
                            <p class="helper-block">
                                {{ trans('cruds.expense.fields.amount_helper') }}
                            </p>
                        </div>
                    </div>
                    <div class="col-md-2">
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
                    </div>
                    <div class="col-md-2">
                        <button style="margin-top:25px" type="submit" class="btn btn-info">Search</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="card-body" id="card-table">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-Expense">
                <thead>
                <tr>
                    <th width="10">

                    </th>
                    <th>
                        {{ trans('cruds.expense.fields.id') }}
                    </th>
                    <th>
                        {{ trans('cruds.expense.fields.expense_category') }}
                    </th>
                    <th>
                        {{ trans('cruds.expense.fields.entry_date') }}
                    </th>
                    <th>
                        {{ trans('cruds.expense.fields.amount') }}
                    </th>
                    <th>
                        {{ trans('cruds.expense.fields.description') }}
                    </th>
                    <th>
                        &nbsp;
                    </th>
                </tr>
                </thead>
                <tbody id="table-body">
                @foreach($expenses as $key => $expense)
                    <tr data-entry-id="{{ $expense->id }}">
                        <td>

                        </td>
                        <td>
                            {{ $expense->id ?? '' }}
                        </td>
                        <td>
                            {{ $expense->expense_category->name ?? '' }}
                        </td>
                        <td>
                            {{ $expense->entry_date ?? '' }}
                        </td>
                        <td>
                            {{ $expense->amount ?? '' }}
                        </td>
                        <td>
                            {{ $expense->description ?? '' }}
                        </td>
                        <td>
                            @can('expense_show')
                                <a class="btn btn-xs btn-primary" href="{{ route('admin.expenses.show', $expense->id) }}">
                                    {{ trans('global.view') }}
                                </a>
                            @endcan

                            @can('expense_edit')
                                <a class="btn btn-xs btn-info" href="{{ route('admin.expenses.edit', $expense->id) }}">
                                    {{ trans('global.edit') }}
                                </a>
                            @endcan

                            @can('expense_delete')
                                <form action="{{ route('admin.expenses.destroy', $expense->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                    <input type="hidden" name="_method" value="DELETE">
                                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                    <input type="submit" class="btn btn-xs btn-danger" value="{{ trans('global.delete') }}">
                                </form>
                            @endcan

                        </td>

                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        {{--        @include('$admin.expenses.list')--}}
    </div>
</div>
@endsection
@section('scripts')
@parent

<script>
    jQuery(document).ready(function () {
        $('form#search-filter').on('submit', function (e) {
            e.preventDefault();
            searchStockSet();
        });

        function searchStockSet(){
            $.ajax({
                url: '/admin/expense/search',
                type: 'POST',
                cache: false,
                data: $('form#search-filter').serialize(),
                datatype: 'html',
                // datatype: 'application/json',

                beforeSend: function() {
                    // show waiting dialog
                    // waitingDialog.show('Loading...');
                },

                success:function(data){
                    console.log(data);
                    $("#card-table").html('');
                    $("#card-table").append(data);
                    // if(data) {
                    //     if(data.status == 103){
                    //         Swal.fire({
                    //             icon: 'error',
                    //             title: 'Oops...',
                    //             text: data.message,
                    //             footer: 'Check your Stock'
                    //         })
                    //     }
                    //     else{
                    //         Swal.fire({
                    //             position: 'top-end',
                    //             icon: 'success',
                    //             title: 'Product Successfully Return',
                    //             showConfirmButton: false,
                    //             timer: 1500
                    //         })
                    //         $('#mediumModal').modal('hide');
                    //         window.location.reload();
                    //
                    //     }
                    // }
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
<script>
    $(function () {
  let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
@can('expense_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.expenses.massDestroy') }}",
    className: 'btn-danger',
    action: function (e, dt, node, config) {
      var ids = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
          return $(entry).data('entry-id')
      });

      if (ids.length === 0) {
        alert('{{ trans('global.datatables.zero_selected') }}')

        return
      }

      if (confirm('{{ trans('global.areYouSure') }}')) {
        $.ajax({
          headers: {'x-csrf-token': _token},
          method: 'POST',
          url: config.url,
          data: { ids: ids, _method: 'DELETE' }})
          .done(function () { location.reload() })
      }
    }
  }
  dtButtons.push(deleteButton)
@endcan

  $.extend(true, $.fn.dataTable.defaults, {
    order: [[ 1, 'desc' ]],
    pageLength: 100,
  });
  $('.datatable-Expense:not(.ajaxTable)').DataTable({ buttons: dtButtons })
    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
        $($.fn.dataTable.tables(true)).DataTable()
            .columns.adjust();
    });
})

</script>
@endsection
