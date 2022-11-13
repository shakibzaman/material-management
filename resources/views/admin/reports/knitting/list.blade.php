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
                    {{ $knitting->transfer->company->name ?? $knitting->company->name }}
                </td>
                <td>
                    {{ $knitting->quantity ?? '' }}
                </td>
                <td>
                    {{ ($type == 3 ? ($knitting->process_fee) : ( $knitting->process_fee*$knitting->quantity ))  ?? '' }}
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
