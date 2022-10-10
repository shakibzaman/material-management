@extends('layouts.admin')
@section('content')
@can('material_create')
    <div style="margin-bottom: 10px;" class="row">
        <a style="margin-top:20px;" class="btn btn-default" href="<?php echo e(url()->previous()); ?>">
            <?php echo e(trans('global.back_to_list')); ?>

        </a>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        Deposit List
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-Expense">
                <thead>
                    <tr>
                        <th width="10">
                        </th>
                        <th>ID</th>
                        <th>Date</th>
                        <th>Balance</th>
                        <th>Source</th>
                        <th>Reason</th>
                        <th>Deposit By</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transactions as $key => $transaction)
                        <tr data-entry-id="{{ $transaction->id }}">
                            <td>

                            </td>
                            <td>
                                {{ $transaction->id ?? '' }}
                            </td>
                            <td>
                                {{ $transaction->created_at ?? '' }}
                            </td>
                            <td>
                                {{ $transaction->amount ?? '' }}
                            </td>
                            <td>
                                {{ $transaction->fund->name ?? 'Others' }}
                            </td>
                            <td>
                                {{ $transaction->reason ?? '' }}
                            </td>

                            <td>
                                {{ $transaction->user->name ?? '' }}
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
