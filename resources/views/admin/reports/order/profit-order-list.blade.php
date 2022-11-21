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
                Invoice
            </th>
            <th>
                Showroom
            </th>
            <th>
                Customer
            </th>
            <th>
                Sub Total
            </th>
            <th>
                Total
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
            <th>
                Processing Charge
            </th>
            <th>Profit</th>
        </tr>
        </thead>
        <tbody>
        @foreach($orders as $order)
            @php
                $sell_price_total = 0;
                    $total_sell_price = $order_details[$order->id];
                    foreach ($total_sell_price as $sell_price){
                        $sell_price_total += ($sell_price->process_costing * $sell_price->qty);
                    }

            @endphp
            <tr>
                <td>{{$order->id}}</td>
                <td>{{$order->date}}</td>
                <td> {{$order->invoice_id}} </td>
                <td>{{$order->showroom->name}}  </td>
                <td>{{$order->customer->name}}  </td>
                <td>{{$order->sub_total}}  </td>
                <td>{{$order->total}}  </td>
                <td>{{$order->discount}}  </td>
                <td>{{$order->paid}}  </td>
                <td>{{$order->due}}  </td>
                <td> {{$sell_price_total}} </td>
                <td>{{$order->total - $sell_price_total}}</td>
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
