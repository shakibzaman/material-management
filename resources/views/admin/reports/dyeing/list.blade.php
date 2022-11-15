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
                Type
            </th>
            <th>
                Name
            </th>
            <th>
                Showroom
            </th>
            <th>
                Quantity
            </th>
            <th>
                Process Charge
            </th>
            <th>
                Dyeing Charge
            </th>
            <th>
                Dry Charge
            </th>
            <th>
                Compact Charge
            </th>
        </tr>
        </thead>
        <tbody id="table-body">
        @foreach($transfer_products as $key => $product)
            <tr data-entry-id="{{ $product->id }}">
                <td>

                </td>
                <td>
                    {{$product->id}}
                </td>
                <td>
                    {{ $product->created_at ?? '' }}
                </td>
                <td>
                    {{ $product->process_type == 1 ? 'Processed':'Process Loss' ?? '' }}
                </td>
                <td>
                    {{ $product->color_name ?? '' }}
                </td>
                <td>
                    {{ $product->department_name ?? '' }}
                </td>
                <td>
                    {{ $product->quantity ?? '' }}
                </td>
                <td>
                    {{ $product->process_fee ?? '' }}
                </td>
                <td>
                    {{ $product->dyeing_charge ?? '' }}
                </td>
                <td>
                    {{ $product->dry_charge ?? '' }}
                </td>
                <td>
                    {{ $product->compacting_charge ?? '' }}
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
