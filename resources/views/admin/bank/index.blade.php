@extends('layouts.admin')
@section('content')
@can('material_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
        <a class="btn btn-success text-light" data-toggle="modal" id="mediumButton" data-target="#mediumModal"
                                                data-attr="{{ route('admin.bank.create') }}" title="Return"> Add Bank Detail
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        Bank List
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
                        <th>A/C No</th>
                        <th>limit</th>
                        <th>Current Balance</th>
                        <th>Interest</th>
                        <th>Interest Type</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($banks as $key => $bank)
                        <tr data-entry-id="{{ $bank->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $bank->id ?? '' }}
                            </td>
                            <td>
                                {{ $bank->name ?? '' }}
                            </td>
                            <td>
                                {{ $bank->ac_no ?? '' }}
                            </td>
                            <td>
                                {{ $bank->limit ?? '' }}
                            </td>
                            <td>
                                {{ $bank->current_balance ?? '' }}
                            </td>
                            <td>
                                {{ $bank->rate ?? '' }}
                            </td>
                            <td>
                                {{ $bank->rate_type ?? '' }}
                            </td>
                            <td>
                                @can('material_show')
                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.bank.show', $bank->id) }}">
                                        {{ trans('global.view') }}
                                    </a>
                                @endcan
                                <a class="btn btn-xs btn-primary text-white" data-toggle="modal" id="mediumButton" data-target="#mediumModal"
                                   data-attr="{{ route('admin.bank.deposit', $bank->id) }}"> Deposit
                                </a>
                                <a class="btn btn-xs btn-info" href="{{ route('admin.bank.deposit.list', $bank->id) }}">
                                    Deposit List
                                </a>

                                <a class="btn btn-xs btn-success" href="{{ route('admin.bank.widthrow.list', $bank->id) }}">
                                    Widthrow List
                                </a>

                                @can('expense_edit')
                                    <a class="btn btn-xs btn-info" href="{{ route('admin.bank.edit', $bank->id) }}">
                                        {{ trans('global.edit') }}
                                    </a>
                                @endcan

                                @can('expense_delete')
                                    <form action="{{ route('admin.bank.destroy', $bank->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
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
