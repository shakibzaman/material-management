@extends('layouts.admin')
@section('content')
    <div class="card">
        <div class="card-header">
            <b>Showroom Product Costing List</b>
        </div>
        <div class="card-body">
            <div class="col-md-12 shadow-lg p-3 mb-5 bg-white rounded">
                <h4>Product Details</h4>
                <table class="table">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Date</th>
                            <th>Name</th>
                            <th>Qty</th>
                            <th class="bg-danger">Process Cost</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>{{$product->id}}</td>
                            <td>{{$product->created_at}}</td>
                            <td>{{$product->color->name}}</td>
                            <td>{{$product->quantity}}</td>
                            <td class="bg-danger">{{round($product->process_costing,2)}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="col-md-12 shadow-lg p-3 mb-5 bg-white rounded">
                <h4>Dyeing Process Details</h4>
                <table class="table">
                    <thead>
                    <tr>
                        <th>Id</th>
                        <th>Date</th>
                        <th>Type</th>
                        <th class="bg-info">Process Cost</th>
                        <th>Dyeing Cost</th>
                        <th>Dry Cost</th>
                        <th>Compacting Cost</th>
                    </tr>
                    </thead>
                    <tbody>

                    @foreach($transfer_product_for_showroom as $dyeing)
                    <tr>
                        <td>{{$dyeing->id}}</td>
                        <td>{{$dyeing->created_at}}</td>
                        <td>{{$dyeing->process_type == 1 ? 'Processed':'Loss'}}</td>
                        <td class="bg-info">{{$dyeing->process_fee}}</td>
                        <td>{{$dyeing->dyeing_charge}}</td>
                        <td>{{$dyeing->dry_charge}}</td>
                        <td>{{$dyeing->compacting_charge}}</td>
                    </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="col-md-12 shadow-lg p-3 mb-5 bg-white rounded">
                <h4>Knitting Process Details</h4>
                <table class="table">
                    <thead>
                    <tr>
                        <th>Id</th>
                        <th>Date</th>
                        <th class="bg-info">Process Cost</th>

                    </tr>
                    </thead>
                    <tbody>
                    @foreach($transfer_product_for_knitting as $knitting)
                        <tr>
                            <td>{{$knitting->id}}</td>
                            <td>{{$knitting->created_at}}</td>
                            <td class="bg-info">{{$knitting->process_fee}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="col-md-12 shadow-lg p-3 mb-5 bg-white rounded">
                <h4>Product Price</h4>
                <table class="table">
                    <thead>
                    <tr>
                        <th>Id</th>
                        <th>Date</th>
                        <th class="bg-info">Process Cost</th>

                    </tr>
                    </thead>
                    <tbody>
                    @foreach( $product_in as $raw_product)
                        <tr>
                            <td>{{$raw_product->id}}</td>
                            <td>{{$raw_product->created_at}}</td>
                            <td class="bg-info">{{$raw_product->unit_price}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="col-md-12 shadow-lg p-3 mb-5 bg-white rounded">
                <h4>Material Price</h4>
                <table class="table">
                    <thead>
                    <tr>
                        <th>Id</th>
                        <th>Date</th>
                        <th>Name</th>
                        <th>Quantity</th>
                        <th>Price / Unit</th>
                        <th class="bg-success">Costing Price / Unit</th>

                    </tr>
                    </thead>
                    <tbody>
                    @php
                        $total_material_costing = 0;
                        @endphp
                    @foreach( $material_costing as $material)
                        @php
                            $total_price = ($material->quantity * $material->detail->unit_price)/$product->quantity;
                            $total_material_costing+=$total_price;
                            @endphp
                        <tr>
                            <td>{{$material->id}}</td>
                            <td>{{$material->created_at}}</td>
                            <td>{{$material->material->name}}</td>
                            <td>{{$material->quantity}}</td>
                            <td>{{$material->detail->unit_price}}</td>
                            <td class="bg-success">{{round($total_price,2)}}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="col-md-12 shadow-lg p-3 mb-5 bg-white rounded">
                <h4>Costing Summary</h4>
                <table class="table table-stripped">
                    <tbody>
                        <tr>
                            <th>Product Costing</th>
                            <td class="bg-danger">{{$product_in->sum('unit_price')}}</td>
                        </tr>
                        <tr>
                            <th>Knitting Costing</th>
                            <td class="bg-danger">{{$transfer_product_for_knitting->sum('process_fee')}}</td>
                        </tr>
                        <tr>
                            <th>Dyeing Costing</th>
                            <td class="bg-danger">{{$transfer_product_for_showroom->sum('process_fee')}}</td>
                        </tr>
                        <tr>
                            <th>Material Costing</th>
                            <td class="bg-danger">{{round($total_material_costing,2)}}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>
    </div>
@endsection
