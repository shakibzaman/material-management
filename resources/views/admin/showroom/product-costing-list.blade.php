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
                        <th>Date</th>
                        <th>Color Name</th>
                        <th>Costing/Unit</th>
                        <th>Type</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($transfer_products as $key=> $list)
                        @php
                            $quantity = $list->sum('quantity');
                            @endphp
                    <tr>
                        <td>{{$list->id}}</td>
                        <td>{{$list->created_at}}</td>
                        <td>{{$colors[$list->color_id]->name}}</td>
                        <td>{{$list->process_costing}}</td>
                        <td class="{{$list->type==1?'bg-success':'bg-info'}}">{{$list->type == 1 ? 'Process Product':'Finish Product'}}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
