@extends('layouts.admin')
@section('content')
@can('product_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route("admin.product.purchase.create") }}">
                {{ trans('global.add') }} Product Purchased
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        Product {{ trans('global.list') }}
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
                        <th>Quantity</th>
                        <th>Unit</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($materials as $key => $material)
                        @isset($materialsPurchased[$material->id])

                        @php
                        $total_quantity = $materialsPurchased[$material->id]->sum('rest')
                        @endphp

                            <tr data-entry-id="{{ $material->id }}">
                                <td>

                                </td>
                                <td>
                                    {{$material->id}}
                                    <!-- {{$materialsPurchased[$material->id][0]->id}} -->
                                </td>
                                <td>
                                    {{$materialsPurchased[$material->id][0]->material->name}}
                                </td>
                                <td>
                                    {{ $total_quantity ?? '' }}
                                </td>
                                <td>
                                    {{$materialsPurchased[$material->id][0]->units->name}}
                                </td>

                                <td>
                                    @can('material_show')
                                        <a class="btn btn-xs btn-primary" href="{{ route('admin.material-in.show', $material->id) }}">
                                            {{ trans('global.view') }}
                                        </a>
                                    @endcan

                                    <a class="btn btn-xs btn-danger" href="{{ route('admin.material-in.show', $material->id) }}">
                                            Return
                                        </a>
                                    <a class="btn btn-xs btn-success text-light" data-toggle="modal" id="mediumButton" data-target="#mediumModal"
                                            data-attr="{{ route('admin.material-in.price',$material->id) }}" title="Price"> Add Price
                                    </a>

                                    @can('expense_edit')
                                        <a class="btn btn-xs btn-info" href="{{ route('admin.material-in.edit', $material->id) }}">
                                            {{ trans('global.edit') }}
                                        </a>
                                    @endcan

                                    @can('expense_delete')
                                        <form action="{{ route('admin.material-in.destroy', $material->id) }}" method="POST" onsubmit="return confirm('{{ trans('global.areYouSure') }}');" style="display: inline-block;">
                                            <input type="hidden" name="_method" value="DELETE">
                                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                                            <input type="submit" class="btn btn-xs btn-danger" value="{{ trans('global.delete') }}">
                                        </form>
                                    @endcan

                                </td>

                            </tr>
                        @endisset
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
