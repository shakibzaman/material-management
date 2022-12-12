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
