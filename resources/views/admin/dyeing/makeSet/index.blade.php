@extends('layouts.admin')
@section('content')
@can('expense_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route("admin.add.set") }}">
               Add Set
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        Stock Set List
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-Expense">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            ID
                        </th>
                        <th>
                            Product
                        </th>
                        <th>
                            Color
                        </th>
                        <th>
                            Start Quantity
                        </th>
                        <th>
                            End Quantity
                        </th>
                        <th>
                            &nbsp;Action
                        </th>
                    </tr>
                </thead>
                <tbody>
                @foreach($sets as $key =>$set)
                <tr>
                    <td>
                    </td>
                    <td>
                        {{$set->id}}
                    </td>
                    <td>
                        {{$set->product->name}}
                    </td>
                    <td>
                        {{$set->color->name}}
                    </td>
                    <td>
                        {{$set->start_quantity}}
                    </td>
                    <td>
                        {{$set->end_quantity}}
                    </td>
                    <td>
                        <a class="btn btn-xs btn-primary" href="{{ route('admin.show.set', $set->id) }}">
                            {{ trans('global.view') }}
                        </a>
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
    <div class="modal-dialog modal-xl" role="document">
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
