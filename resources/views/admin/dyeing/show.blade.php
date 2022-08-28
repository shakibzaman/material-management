@extends('layouts.admin')
@section('content')
@can('expense_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">

        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        Transfer Details
    </div>
    {{$transfer_products}}
    @php
        $transfer_total_product = $transfer_products->sum('quantity') ?? '';
        $process_cost = $transfer_products[0]->process_fee ? $transfer_products[0]->process_fee * $transfer_total_product : 0;
        $transfer_total_material = $transfer_materials->sum('quantity') ?? '';
        $unit_price = ($transfer_total_product + $transfer_total_material + $process_cost) / $transfer_total_product;
    @endphp
    <div class="card-body">
        <table class="table table-bordered table-striped">
                <tbody>
                <tr>
                    <th>
                        ID
                    </th>
                    <td>
                        {{$transfer->id}}
                    </td>
                </tr>
                <tr>
                    <th>
                        Company Name
                    </th>
                    <td>
                        {{$transfer->company->name}} ( {{$transfer->company->type==1?'SELF':'OTHERS'}} )
                    </td>
                </tr>
                <tr>
                    <th>
                        Total Product Quantity

                    </th>
                    <td>
                        {{$transfer_total_product}}
                    </td>
                </tr>
                <tr>
                    <th>
                        Total material Quantity

                    </th>
                    <td>
                        {{$transfer_total_material}}
                    </td>
                </tr>
                <tr>
                    <th>
                        Date

                    </th>
                    <td>
                        {{$transfer->date}}
                    </td>
                </tr>
                </tbody>
            </table>
        <div class="row">
            <div class="col-md-6">
                <h3>Product Details</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>Name</th>
                            <th>Qty</th>
                            <th>Rest Qty</th>
                            <th>Unit Price</th>
                            <th>Process Fee/Unit</th>
                            <th>Total Price</th>
                        </tr>
                    </thead>
                    <tbody>
                    @php $total_product = 0; @endphp
                    @foreach($transfer_products as $product)
                        <tr>
                            <th>{{$product->id}}</th>
                            <th>{{$product->product->name}}</th>
                            <th>{{$product->quantity}}</th>
                            <th>{{$product->rest_quantity}}</th>
                            <th>{{$unit_price}}</th>
{{--                            {{$product->detail->unit_price}} </th>--}}
                            <th> {{$product->process_fee}} </th>
{{--                            <th>{{$product->detail->unit_price * $product->quantity}}</th>--}}
{{--                            @php  $total_product+= ($product->detail->unit_price * $product->quantity) @endphp--}}
                            @php $total_product =0; @endphp
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="col-md-6">
                <h3>Material Details</h3>
                <table class="table">
                    <thead>
                    <tr>
                        <th>SL</th>
                        <th>Name</th>
                        <th>Qty</th>
                        <th>Unit Price</th>
                        <th>Total Price</th>
                    </tr>
                    </thead>
                    <tbody>
                    @php $total_material = 0; @endphp
                    @foreach($transfer_materials as $material)
                        <tr>
                            <th>{{$material->id}}</th>
                            <th>{{$material->material->name}}</th>
                            <th>{{$material->quantity}}</th>
                            <th>{{$material->detail->unit_price}}</th>
                            <th>{{$material->detail->unit_price * $material->quantity}}</th>
                            @php  $total_material+= ($material->detail->unit_price * $material->quantity) @endphp
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <h5>Total (Product+Material) Amount = <span>{{$total_material+$total_product}}</span></h5>
            </div>
            <div class="col-md-4">
                <h5>Cost Per KG = {{($total_material+$total_product+$process_cost)/$transfer_total_product}} </h5>
            </div>
            <div class="col-md-4">
                <h5>Process Cost = {{$process_cost}}</h5>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
@parent

@endsection
