<div class="row">
    <div class="col-md-6">
        <table class="table table-bordered">
            <tbody>
                <th>Deposit List</th>
                <td class="text-success">{{$bank_deposit->sum('amount')}} Tk.</td>
            </tbody>
        </table>
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
                        Amount
                    </th>
                    <th>
                        Reason
                    </th>
                    <th>
                        Deposit By
                    </th>
                </tr>
                </thead>
                <tbody>
                @foreach($bank_deposit as $key=>$bank)
                    <tr>
                        <td>1</td>
                        <td>{{$bank->date}}</td>
                        <td>{{$bank->amount}}</td>
                        <td>{{$bank->reason}}  </td>
                        <td>{{$bank->user->name}}  </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="col-md-6">
        <table class="table table-bordered">
            <tbody>
                <th>Withdraw List</th>
                <td class="text-danger">{{$bank_withdraw->sum('amount')}} Tk.</td>
            </tbody>
        </table>
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
                        Amount
                    </th>
                    <th>
                        Reason
                    </th>
                    <th>
                        Withdraw By
                    </th>
                </tr>
                </thead>
                <tbody>
                @foreach($bank_withdraw as $key=>$bank)
                    <tr>
                        <td>1</td>
                        <td>{{$bank->date}}</td>
                        <td>{{$bank->amount}}</td>
                        <td>{{$bank->reason}}  </td>
                        <td>{{$bank->user->name}}  </td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>

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
