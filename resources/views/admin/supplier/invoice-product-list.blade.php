@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        Product Details
    </div>

    <div class="card-body">
        <div class="mb-2">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th> ID </th>
                        <th>Date</th>
                        <th>inv number</th>
                        <th>Product Name</th>
                        <th>Quantity</th>
                        <th>Unit Price</th>
                        <th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($supplier_product as $product)
                    <tr>

                        <td>
                            {{ $product->id }}
                        </td>

                        <td>
                            {{ $product->buying_date ?? '' }}
                        </td>
                        <td>
                            {{ $product->inv_number ?? '' }}
                        </td>
                        <td>
                            {{$product->material->name ?? 'N/A'}}
                        </td>
                        <td>
                            {{ $product->quantity }}
                        </td>
                        <td>
                            {{ $product->unit_price }}
                        </td>
                        <td>
                            {{ $product->total_price }}
                        </td>

                    </tr>
                    @endforeach
                </tbody>
            </table>
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <th>Total Price</th>
                        <td>{{$supplier_product->sum('total_price')}}</td>
                    </tr>
                    <tr>
                        <th>Total Quantity</th>
                        <td>{{$supplier_product->sum('quantity')}}</td>
                    </tr>
                </tbody>
            </table>
            <a style="margin-top:20px;" class="btn btn-default" href="{{ url()->previous() }}">
                {{ trans('global.back_to_list') }}
            </a>
        </div>


    </div>
</div>
@endsection
