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
        Expense List
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <h3>Expense Details</h3>
                <table class="table">
                    <thead>
                        <tr>
                            <th>SL</th>
                            <th>Date</th>
                            <th>Amount</th>
                            <th>Category</th>
                            <th>Material Name</th>
                            <th>Description</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php $total = $expenses->sum('amount'); @endphp
                    @foreach($expenses as $expense)
                        <tr>
                            <th>{{$expense->id}}</th>
                            <th>{{$expense->entry_date}}</th>
                            <th>{{$expense->amount}}</th>
                            <th>{{$expense->expense_category->name}}</th>
                            <th>{{$expense->material->name}}</th>
                            <th>{{$expense->description}}</th>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
            <div class="col-md-3">
                <h5>Total : {{$total}}</h5>
            </div>
            
        </div>
    </div>
</div>
@endsection
@section('scripts')
@parent

@endsection
