@extends('layouts.admin')
@section('content')
@can('expense_create')
    <div style="margin-bottom: 10px;" class="row">
    </div>
@endcan
<div class="card">
    <div class="card-header">
        Knitting List
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
                        <div class="form-group {{ $errors->has('department_id') ? 'has-error' : '' }}">
                            <label for="company_id">Company</label>
                            <select name="company_id" id="company_id" class="form-control select2">
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
                        <div class="form-group {{ $errors->has('type') ? 'has-error' : '' }}">
                            <label for="type">Type</label>
                            <select name="type" id="type" class="form-control select2">
                                <option>--- Select ---</option>
                                <option value="1" >In</option>
                                <option value="2" >Out</option>
                                <option value="3" >Delivered</option>
                            </select>
                            @if($errors->has('type'))
                                <em class="invalid-feedback">
                                    {{ $errors->first('type') }}
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
                        Id
                    </th>
                    <th>
                        Date
                    </th>
                    <th>
                        Name
                    </th>
                    <th>
                        Company
                    </th>
                    <th>
                        Quantity
                    </th>
                    <th>
                        Process Fee
                    </th>
                </tr>
                </thead>
                <tbody id="table-body">
                @foreach($knitting_in as $key => $knitting)
                    <tr data-entry-id="{{ $knitting->id }}">
                        <td>

                        </td>
                        <td>
                            {{$knitting->id}}
                        </td>
                        <td>
                            {{ $knitting->created_at ?? '' }}
                        </td>
                        <td>
                            {{ $knitting->product->name ?? '' }}
                        </td>
                        <td>
                            {{ $knitting->transfer->company->name ?? '' }}
                        </td>
                        <td>
                            {{ $knitting->quantity ?? '' }}
                        </td>
                        <td>
                            {{ $knitting->process_fee*$knitting->quantity ?? '' }}
                        </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>

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
                url: '/admin/report/knitting-report/search',
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
                    if(data) {
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
                                title: 'Successfully data found',
                                showConfirmButton: false,
                                timer: 2500
                            })
                            // $('#mediumModal').modal('hide');
                            // window.location.reload();

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
