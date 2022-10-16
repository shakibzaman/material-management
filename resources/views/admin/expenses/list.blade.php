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
        <tbody>
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
