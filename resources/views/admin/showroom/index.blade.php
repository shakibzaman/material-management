@extends('layouts.admin')
@section('content')
@can('expense_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route("admin.showroom.cart",3) }}">
                Cart
            </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        <b>Showroom Product Stock List</b>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-Expense">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            Product Name
                        </th>
                        <th>
                            Color & Quantity
                        </th>
                        <th>
                            Product Quantity
                        </th>
                        <th>
                            &nbsp;Action
                        </th>
                    </tr>
                </thead>
                <tbody>
                @foreach($products as $key =>$product)
                    @php
                        if(isset($transfer_products[$product->id])) {
                                $productTotal = $transfer_products[$product->id]->sum('rest_quantity');
                                $product_detail = $transfer_products[$product->id]->groupBy('color.id');

                                }
                        else{
                            $productTotal = 0;
                            $product_detail = [];
                        }
                    @endphp
                    @if($productTotal != 0)
                <tr>
                    <td>

                    </td>
                    <td>
                        {{$product->name}}
                    </td>
                    <td>
                        @if($product_detail != null)
                        @foreach($product_detail as $color_id => $detail)
                            @php
                                $qty = $detail->sum('rest_quantity')
                                @endphp

                            <table class="table table-hover">
                                <tbody>
                                <tr>
                                    <th>{{($color_id!=null)?$colors[$color_id]->name:'N/A'}}</th>
                                    <td>{{$qty}}</td>
                                    <td>
                                        <a class="btn btn-success text-light" data-toggle="modal" id="mediumButton" data-target="#mediumModal"
                                           data-attr="{{ route('admin.dyeing.use.material.detail',[$department_id,$product->id,$color_id]) }}" title="Return"> Material
                                        </a>

                                    </td>
                                </tr>
                                </tbody>
                            </table>

                        @endforeach
                        @endif

                    </td>
                    <td>
                        {{$productTotal}}
                    </td>
                    <td>
                    </td>
                    <td>
{{--                        <a class="btn btn-xs btn-primary" href="{{ route('admin.showroom.transfer', $departments[$key]->id) }}">--}}
{{--                            {{ trans('global.view') }}--}}
{{--                        </a>--}}
{{--@if($departments[$key]->id == 1)--}}
{{--<a class="btn btn-xs btn-success" href="{{ route('admin.netting.transfer.company.product', $companyList[$key]->id) }}">--}}
{{--Transfer to Dyeing--}}
{{--</a>--}}
{{--@endif--}}

                    </td>
                </tr>
                @endif
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
