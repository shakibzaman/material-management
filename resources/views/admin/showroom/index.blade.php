@extends('layouts.admin')
@section('content')
    @can('expense_create')
        <div style="margin-bottom: 10px;" class="row">
            <div class="col-lg-12">
                @if($department_id == 1)
                    <a class="btn btn-primary" href="{{ route("admin.knitting.cart",$department_id) }}">
                        Cart
                    </a>
                @else
                    <a class="btn btn-success" href="{{ route("admin.showroom.cart",$department_id) }}">
                        Cart
                    </a>
                @endif
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
                            Color Name
                        </th>
                        <th>
                            Color & Quantity
                        </th>
                        <th>
                            Product Quantity
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($colors as $key =>$color)
                        @php
                            if(isset($transfer_products[$color->id])) {
                                    $productTotal = $transfer_products[$color->id]->sum('rest_quantity');
                                    $product_detail = $transfer_products[$color->id]->groupBy('color.id');
                                    $test = $transfer_products[$color->id];
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
                                    {{$color->name}}
                                </td>
{{--                                <td>--}}
{{--                                    @if($product_detail != null)--}}
{{--                                        @foreach($product_detail as $color_id => $detail)--}}
{{--                                            @php--}}
{{--                                                $qty = $detail->sum('rest_quantity')--}}
{{--                                            @endphp--}}

{{--                                            <table class="table table-hover">--}}
{{--                                                <tbody>--}}
{{--                                                <tr>--}}
{{--                                                    <th>{{($color_id!=null)?$colors[$color_id]->name:'N/A'}}</th>--}}
{{--                                                    <td>{{$qty}}</td>--}}
{{--                                                    --}}
{{--                                                </tr>--}}
{{--                                                </tbody>--}}
{{--                                            </table>--}}

{{--                                        @endforeach--}}
{{--                                    @endif--}}

{{--                                </td>--}}
                                <td>
                                    {{$productTotal}}
                                </td>
                                <td>
                                    <a class="btn btn-success text-light"
                                       href="{{route('admin.showroom.product.details',[$department_id,$color->id])}}">Details</a>

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
        $(document).on('click', '#mediumButton', function (event) {
            event.preventDefault();
            let href = $(this).attr('data-attr');
            $.ajax({
                url: href,
                beforeSend: function () {
                    $('#loader').show();
                },
                // return the result
                success: function (result) {
                    $('#mediumModal').modal("show");
                    $('#mediumBody').html(result).show();
                },
                complete: function () {
                    $('#loader').hide();
                },
                error: function (jqXHR, testStatus, error) {
                    console.log(error);
                    alert("Page " + href + " cannot open. Error:" + error);
                    $('#loader').hide();
                },
                timeout: 8000
            })
        });
    </script>
@endsection
