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
                Invoice
            </th>
            <th>
                Name
            </th>
            <th>
                Type
            </th>
            <th>
                Sub Total
            </th>
            <th>
                Total
            </th>
            <th>
                Paid
            </th>
            <th>
                Discount
            </th>
            <th>
                Due
            </th>
        </tr>
        </thead>
        <tbody id="table-body">
        @foreach($invoices as $key => $invoice)
            <tr data-entry-id="{{ $invoice->id }}">
                <td>

                </td>
                <td>
                    {{$invoice->id}}
                </td>
                <td>
                    {{ $invoice->date ?? '' }}
                </td>
                <td>
                    {{ $invoice->inv_number ?? '' }}
                </td>
                <td>
                    {{ $invoice->supplier_name ?? '' }}
                </td>
                <td>
                    {{ $invoice->type == 1 ?'Material':($invoice->type==2 ?'Product':'FInish Product') ?? '' }}
                </td>
                <td>
                    {{ $invoice->sub_total ?? '' }}
                </td>
                <td>
                    {{ $invoice->total ?? '' }}
                </td>
                <td>
                    {{ $invoice->paid ?? '' }}
                </td>
                <td>
                    {{ $invoice->discount ?? '' }}
                </td>
                <td>
                    {{ $invoice->due ?? '' }}
                </td>
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
