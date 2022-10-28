@extends('layouts.admin')
@section('content')
    <div class="card">
        <div class="card-header">
            <b>Showroom Product List</b>
        </div>
        <div class="card-body">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Color Name</th>
                        <th>Quantity</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transfer_products as $key=> $list)
                        @php
                            $quantity = $list->sum('quantity');
                            @endphp
                    <tr>
                        <td>{{$key}}</td>
                        <td>{{$colors[$key]->name}}</td>
                        <td>{{$quantity}}</td>
                        <td>
                            <a class="btn btn-success text-light"
                               href="{{route('admin.showroom.product.details',[$department,$key])}}">Self Product Details</a>
                            <a class="btn btn-danger text-light"
                               href="{{route('admin.showroom.product.loss.details',[$department,$key])}}">Self Process Loss Details</a>
                            <a class="btn btn-info text-light"
                               href="{{route('admin.showroom.finish.product.details',[$department,$key])}}">Finish Product Details</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
