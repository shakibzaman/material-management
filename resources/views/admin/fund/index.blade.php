@extends('layouts.admin')
@section('content')
@can('material_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
        <a class="btn btn-success text-light" data-toggle="modal" id="mediumButton" data-target="#mediumModal"
                                                data-attr="{{ route('admin.fund.create') }}" title="Return"> Add Fund Detail
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        Fund List
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-Expense">
                <thead>
                    <tr>
                        <th width="10">
                        </th>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Current Balance</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($funds as $key => $fund)
                        <tr data-entry-id="{{ $fund->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $fund->id ?? '' }}
                            </td>
                            <td>
                                {{ $fund->name ?? '' }}
                            </td>
                            <td>
                                {{ $fund->current_balance ?? '' }}
                            </td>

                            <td>
                                @can('material_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.fund.show', $fund->id) }}">
                                        {{ trans('global.view') }}
                                    </a>
                                @endcan
                                @if($fund->id == 1)
                                    <a class="btn btn-xs btn-primary text-white" data-toggle="modal" id="mediumButton" data-target="#mediumModal"
                                       data-attr="{{ route('admin.fund.deposit', $fund->id) }}"> Deposit
                                    </a>
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.fund.deposit.list', $fund->id) }}">
                                        Deposit List
                                    </a>
                                @endif

                                @can('expense_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.fund.edit', $fund->id) }}">
                                        {{ trans('global.edit') }}
                                    </a>
                                @endcan

                                @can('expense_delete')
                                    <form action="{{ route('admin.fund.destroy', $fund->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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
@parent
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
@endsection
