@extends('layouts.admin')
@section('content')
@can('expense_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <!-- <a class="btn btn-success" href="{{ route("admin.neeting.stock.in") }}">
                Stock In
            </a> -->
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        Set Material List
    </div>

    <div class="card-body">
        <div class="table">
            <table class="table table-striped">
                <tbody>
                <tr>
                    <th>Material Name</th>
                    <td>{{$set->product->name}}</td>
                </tr>
                <tr>
                    <th>Start quantity</th>
                    <td>{{$set->start_quantity}}</td>
                </tr><tr>
                    <th>End Quantity</th>
                    <td>{{$set->end_quantity}}</td>

                </tr>
                </tbody>
            </table>
        </div>
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
                            Material
                        </th>
                        <th>
                            Quantity
                        </th>
                        <th>
                            &nbsp;Action
                        </th>
                    </tr>
                </thead>
                <tbody>
                @foreach($setMaterialDetails as $key =>$material)
                <tr>
                    <td>
                    </td>
                    <td>
                        {{$material->id}}
                    </td>
                    <td>
                        {{$material->material->name}}
                    </td>
                    <td>
                        {{$material->material_quantity}}
                    </td>
                    <td>

                    </td>

                </tr>
                @endforeach
                </tbody>
            </table>
        </div>

        <a style="margin-top:20px;" class="btn btn-default" href="{{ url()->previous() }}">
            {{ trans('global.back_to_list') }}
        </a>
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
