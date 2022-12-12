@extends('layouts.admin')
@section('content')
    <div class="card">
        <div class="card-header">
            Product Sell Report
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
                                @if($errors->has('end_date'))
                                    <em class="invalid-feedback">
                                        {{ $errors->first('end_date') }}
                                    </em>
                                @endif
                                <p class="helper-block">
                                    {{ trans('cruds.expense.fields.amount_helper') }}
                                </p>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <label for="expense_category">Showroom Name</label>
                                <select name="showroom_id" id="" class="form-control select2">
                                        <option value="0">--- Select ---</option>
                                        <option value="3">Nganj</option>
                                        <option value="4">Mirpur</option>
                                </select>
                                @if($errors->has('showroom_id'))
                                    <em class="invalid-feedback">
                                        {{ $errors->first('showroom_id') }}
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
                        <th>
                            ID
                        </th>
                        <th>
                            Date
                        </th>
                        <th>
                            Quantity
                        </th>
                        <th>
                            Total Price
                        </th>
                        <th>
                            Discount
                        </th>
                        <th>
                            Paid
                        </th>
                        <th>
                            Due
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($orders as $key=>$order)
                        <tr>
                            <td>1</td>
                            <td>{{$key}}</td>
                            <td>{{$order->sum('qty')}}</td>
                            <td>{{$order->sum('total')}}  </td>
                            <td>{{$order->sum('discount')}}  </td>
                            <td>{{$order->sum('paid')}}  </td>
                            <td>{{$order->sum('due')}}  </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>


        </div>
    </div>
    <div class="modal fade" id="mediumModal" tabindex="-1" role="dialog" aria-labelledby="mediumModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body" id="mediumBody">
                    <div>
                        <!-- the result to be displayed apply here -->
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        jQuery(document).ready(function () {
            $('form#search-filter').on('submit', function (e) {
                e.preventDefault();
                searchStockSet();
            });

            function searchStockSet(){
                $.ajax({
                    url: '/admin/report/product/search',
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
                        console.log('Report got');
                        console.log(data);
                        $("#card-table").html('');
                        $("#card-table").append(data);
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
        $(document).on('click', '#mediumButton', function(event) {
            event.preventDefault();
            let href = $(this).attr('data-attr');
            $.ajax({
                url: href,
                beforeSend: function() {
                    $('#loader').show();
                },
                // return the result
                success: function(result) {
                    $('#mediumModal').modal("show");
                    $('#mediumBody').html(result).show();
                },
                complete: function() {
                    $('#loader').hide();
                },
                error: function(jqXHR, testStatus, error) {
                    console.log(error);
                    alert("Page " + href + " cannot open. Error:" + error);
                    $('#loader').hide();
                },
                timeout: 8000
            })
        });
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
