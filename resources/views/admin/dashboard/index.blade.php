@extends('layouts.admin')
@section('content')

    <div class="card text-center">
        <h2>Welcome <span class="text-success"> {{$user->name}} </span></h2>
    </div>

    <div class="row">
        @php
            $today = now()->format('Y-m-d');
            $date=date_create($today);
            date_sub($date,date_interval_create_from_date_string("30 days"));
            $days_30 = date_format($date,"Y-m-d");

            $knitting_today_order = $knitting_order->where('date',$today);
            $knitting_30_days_order = $knitting_order->whereBetween('date', [$days_30,$today]);

            $knitting_today_delivered_total_qty = $knitting_delivery->where('date',$today)->sum('quantity');
            $knitting_30_days_delivered_total_qty = $knitting_delivery->whereBetween('date', [$days_30,$today])->sum('quantity');
        @endphp
        <div class="col-md-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fa fa-building" aria-hidden="true"></i> Total Knitting</h3>
                    </div>
                    <div class="card-body">
                        <a href="{{route('admin.neeting.index')}}">
                            <h5 class="card-title"><i class="fa fa-external-link-square" aria-hidden="true"></i> Stock
                                Qty : {{$knitting_product_qty}} </h5>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fa fa-building" aria-hidden="true"></i> Knitting Today Order</h3>
                    </div>
                    <div class="card-body">
                        <a href="{{route('admin.knitting.orders',1)}}">
                            <h5 class="card-title"><i class="fa fa-external-link-square" aria-hidden="true"></i> Order
                                Count : {{$knitting_today_order->count()}} </h5>
                        </a>
                        <a href="{{route('admin.knitting.orders',1)}}">
                            <h5 class="card-title"><i class="fa fa-money" aria-hidden="true"></i> Order
                                Amount : {{$knitting_today_order->sum('total')}} Tk.</h5>
                        </a>
                        <a href="{{route('admin.knitting.orders',1)}}">
                            <h5 class="card-title"><i class="fa fa-money" aria-hidden="true"></i> Order
                                Paid : {{$knitting_today_order->sum('paid')}} Tk.</h5>
                        </a>
                        <a href="{{route('admin.knitting.orders',1)}}">
                            <h5 class="card-title"><i class="fa fa-money" aria-hidden="true"></i> Order
                                Due : {{$knitting_today_order->sum('due')}} Tk.</h5>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fa fa-building" aria-hidden="true"></i> Knitting Monthly Order</h3>
                    </div>
                    <div class="card-body">
                        <a href="{{route('admin.knitting.orders',1)}}">
                            <h5 class="card-title"><i class="fa fa-external-link-square" aria-hidden="true"></i> Order
                                Count : {{$knitting_30_days_order->count()}} </h5>
                        </a>
                        <a href="{{route('admin.knitting.orders',1)}}">
                            <h5 class="card-title"><i class="fa fa-money" aria-hidden="true"></i> Order
                                Amount : {{$knitting_30_days_order->sum('total')}} Tk.</h5>
                        </a>
                        <a href="{{route('admin.knitting.orders',1)}}">
                            <h5 class="card-title"><i class="fa fa-money" aria-hidden="true"></i> Order
                                Paid : {{$knitting_30_days_order->sum('paid')}} Tk.</h5>
                        </a>
                        <a href="{{route('admin.knitting.orders',1)}}">
                            <h5 class="card-title"><i class="fa fa-money" aria-hidden="true"></i> Order
                                Due : {{$knitting_30_days_order->sum('due')}} Tk.</h5>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fa fa-building" aria-hidden="true"></i> Knitting Delivered</h3>
                    </div>
                    <div class="card-body">
                        <a href="{{route('admin.neeting.index')}}">
                            <h5 class="card-title"><i class="fa fa-external-link-square" aria-hidden="true"></i> Today's
                                Delivered Quantity : {{$knitting_today_delivered_total_qty}} </h5>
                        </a>
                        <a href="{{route('admin.neeting.index')}}">
                            <h5 class="card-title"><i class="fa fa-external-link-square" aria-hidden="true"></i> Month
                                Delivered Quantity : {{$knitting_30_days_delivered_total_qty}} </h5>
                        </a>
                    </div>
                </div>
            </div>

        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-header">
                    <h3><i class="fa fa-building" aria-hidden="true"></i> Total Dyeing</h3>
                </div>
                <div class="card-body">
                    <a href="{{route('admin.dyeing.index')}}">
                        <h5 class="card-title"><i class="fa fa-external-link-square" aria-hidden="true"></i> Stock Qty
                            : {{$dyeing_product_qty}} </h5>
                    </a>
                </div>
            </div>
        </div>
        @php
            $n_gonj_today_order = $n_gonj_order->where('date',$today);
            $n_gonj_30_days_order = $n_gonj_order->whereBetween('date', [$days_30,$today]);
        @endphp
        <div class="col-md-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fa fa-building" aria-hidden="true"></i> Total N.gonj Showroom </h3>
                    </div>
                    <div class="card-body">
                        <a href="{{route("admin.showroom.stock",3)}}">
                            <h5 class="card-title"><i class="fa fa-external-link-square" aria-hidden="true"></i> Stock
                                Qty : {{$showroom_n_gonj_product_qty}} </h5>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fa fa-building" aria-hidden="true"></i> Today Order</h3>
                    </div>
                    <div class="card-body">
                        <a href="{{route('admin.knitting.orders',3)}}">
                            <h5 class="card-title"><i class="fa fa-external-link-square" aria-hidden="true"></i> Order
                                Count : {{$n_gonj_today_order->count()}} </h5>
                        </a>
                        <a href="{{route('admin.knitting.orders',3)}}">
                            <h5 class="card-title"><i class="fa fa-external-link-square" aria-hidden="true"></i> Order
                                Amount : {{$n_gonj_today_order->sum('total')}} </h5>
                        </a>
                        <a href="{{route('admin.knitting.orders',3)}}">
                            <h5 class="card-title"><i class="fa fa-external-link-square" aria-hidden="true"></i> Order
                                Paid : {{$n_gonj_today_order->sum('paid')}} </h5>
                        </a>
                        <a href="{{route('admin.knitting.orders',3)}}">
                            <h5 class="card-title"><i class="fa fa-external-link-square" aria-hidden="true"></i> Order
                                Due : {{$n_gonj_today_order->sum('due')}} </h5>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fa fa-building" aria-hidden="true"></i> Monthly Order</h3>
                    </div>
                    <div class="card-body">
                        <a href="{{route('admin.knitting.orders',3)}}">
                            <h5 class="card-title"><i class="fa fa-external-link-square" aria-hidden="true"></i> Order
                                Count : {{$n_gonj_30_days_order->count()}} </h5>
                        </a>
                        <a href="{{route('admin.knitting.orders',3)}}">
                            <h5 class="card-title"><i class="fa fa-external-link-square" aria-hidden="true"></i> Order
                                Amount : {{$n_gonj_30_days_order->sum('total')}} </h5>
                        </a>
                        <a href="{{route('admin.knitting.orders',3)}}">
                            <h5 class="card-title"><i class="fa fa-external-link-square" aria-hidden="true"></i> Order
                                Paid : {{$n_gonj_30_days_order->sum('paid')}} </h5>
                        </a>
                        <a href="{{route('admin.knitting.orders',3)}}">
                            <h5 class="card-title"><i class="fa fa-external-link-square" aria-hidden="true"></i> Order
                                Due : {{$n_gonj_30_days_order->sum('due')}} </h5>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        @php
            $mirpur_today_order = $mirpur_order->where('date',$today);
            $mirpur_30_days_order = $mirpur_order->whereBetween('date', [$days_30,$today]);
        @endphp
        <div class="col-md-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fa fa-building" aria-hidden="true"></i> Total Mirpur Showroom </h3>
                    </div>
                    <div class="card-body">
                        <a href="{{route("admin.showroom.stock",4)}}">
                            <h5 class="card-title"><i class="fa fa-external-link-square" aria-hidden="true"></i> Stock
                                Qty : {{$showroom_mirpur_product_qty}} </h5>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fa fa-building" aria-hidden="true"></i> Today Order</h3>
                    </div>
                    <div class="card-body">
                        <a href="{{route('admin.knitting.orders',4)}}">
                            <h5 class="card-title"><i class="fa fa-external-link-square" aria-hidden="true"></i> Order
                                Count : {{$mirpur_today_order->count()}} </h5>
                        </a>
                        <a href="{{route('admin.knitting.orders',4)}}">
                            <h5 class="card-title"><i class="fa fa-external-link-square" aria-hidden="true"></i> Order
                                Amount : {{$mirpur_today_order->sum('total')}} </h5>
                        </a>
                        <a href="{{route('admin.knitting.orders',4)}}">
                            <h5 class="card-title"><i class="fa fa-external-link-square" aria-hidden="true"></i> Order
                                Paid : {{$mirpur_today_order->sum('paid')}} </h5>
                        </a>
                        <a href="{{route('admin.knitting.orders',4)}}">
                            <h5 class="card-title"><i class="fa fa-external-link-square" aria-hidden="true"></i> Order
                                Due : {{$mirpur_today_order->sum('due')}} </h5>
                        </a>
                    </div>
                </div>
            </div>

            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3><i class="fa fa-building" aria-hidden="true"></i> Monthly Order</h3>
                    </div>
                    <div class="card-body">
                        <a href="{{route('admin.knitting.orders',4)}}">
                            <h5 class="card-title"><i class="fa fa-external-link-square" aria-hidden="true"></i> Order
                                Count : {{$mirpur_30_days_order->count()}} </h5>
                        </a>
                        <a href="{{route('admin.knitting.orders',4)}}">
                            <h5 class="card-title"><i class="fa fa-external-link-square" aria-hidden="true"></i> Order
                                Amount : {{$mirpur_30_days_order->sum('total')}} </h5>
                        </a>
                        <a href="{{route('admin.knitting.orders',4)}}">
                            <h5 class="card-title"><i class="fa fa-external-link-square" aria-hidden="true"></i> Order
                                Paid : {{$mirpur_30_days_order->sum('paid')}} </h5>
                        </a>
                        <a href="{{route('admin.knitting.orders',4)}}">
                            <h5 class="card-title"><i class="fa fa-external-link-square" aria-hidden="true"></i> Order
                                Due : {{$mirpur_30_days_order->sum('due')}} </h5>
                        </a>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <div class="row">

        @php
            $todays_expense = $expenses->where('entry_date',$today)->sum('amount');
            $days_30_expense = $expenses->whereBetween('entry_date', [$days_30,$today])->sum('amount');
            $todays_income = $incomes->where('entry_date',$today)->sum('amount');
            $days_30_income = $incomes->whereBetween('entry_date', [$days_30,$today])->sum('amount');
            @endphp
        <div class="col-md-3">
            <div class="col-md-12">
                <div class="card bg-danger">
                    <div class="card-header">
                        <h3><i class="fa fa-building" aria-hidden="true"></i> Expense</h3>
                    </div>
                    <div class="card-body">
                        <a href="{{route('admin.expenses.index')}}" class=" text-light">
                            <h5 class="card-title"><i class="fa fa-money" aria-hidden="true"></i> Today's Expense :
                                {{$todays_expense}} Tk.</h5>
                        </a>
                        <a href="{{route('admin.expenses.index')}}" class=" text-light">
                            <h5 class="card-title"><i class="fa fa-money" aria-hidden="true"></i> Monthly Expense :
                                {{$days_30_expense}} Tk. </h5>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="col-md-12">
                <div class="card bg-success">
                    <div class="card-header">
                        <h3><i class="fa fa-building" aria-hidden="true"></i> Income</h3>
                    </div>
                    <div class="card-body">
                        <a href="{{route('admin.incomes.index')}}" class=" text-light">
                            <h5 class="card-title"><i class="fa fa-money" aria-hidden="true"></i> Today's Income :
                                {{$todays_income}} Tk.</h5>
                        </a>
                        <a href="{{route('admin.incomes.index')}}" class=" text-light">
                            <h5 class="card-title"><i class="fa fa-money" aria-hidden="true"></i> Monthly Income :
                                {{$days_30_income}} Tk. </h5>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        @php
            $total_amount = $bank_info->sum('current_balance');
            $todays_deposit = $bank_transactions->where('type',2)->where('destination_type',1)->where('date',$today)->sum('amount');
            $todays_widthrow = $bank_transactions->where('type',1)->where('date',$today)->sum('amount');
            @endphp
        <div class="col-md-3">
            <div class="col-md-12">
                <div class="card bg-primary">
                    <div class="card-header">
                        <h3><i class="fa fa-building" aria-hidden="true"></i> Bank</h3>
                    </div>
                    <div class="card-body">
                        <a href="{{route('admin.expenses.index')}}" class=" text-light">
                            <h5 class="card-title"><i class="fa fa-money" aria-hidden="true"></i> Current :
                                {{$bank_info->sum('current_balance')}} Tk.</h5>
                        </a>
                        <a href="{{route('admin.expenses.index')}}" class=" text-light">
                            <h5 class="card-title"><i class="fa fa-money" aria-hidden="true"></i> Today's Deposit :
                                {{$todays_deposit}} Tk. </h5>
                        </a>
                        <a href="{{route('admin.expenses.index')}}" class=" text-light">
                            <h5 class="card-title"><i class="fa fa-money" aria-hidden="true"></i> Today's Widthrow :
                                {{$todays_widthrow}} Tk. </h5>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        @php
            $total_amount = $fund_transactions->sum('current_balance');
            $todays_fund_deposit = $fund_transactions->where('source_type',2)->where('type',2)->where('date',$today)->sum('amount');
            $todays_fund_widthrow = $fund_transactions->where('source_type',2)->where('type',1)->where('date',$today)->sum('amount');
        @endphp
        <div class="col-md-3">
            <div class="col-md-12">
                <div class="card bg-info">
                    <div class="card-header">
                        <h3><i class="fa fa-building" aria-hidden="true"></i> Funds</h3>
                    </div>
                    <div class="card-body">
                        <a href="{{route('admin.expenses.index')}}" class=" text-light">
                            <h5 class="card-title"><i class="fa fa-money" aria-hidden="true"></i> Current :
                                {{$fund_info->sum('current_balance')}} Tk.</h5>
                        </a>
                        <a href="{{route('admin.expenses.index')}}" class=" text-light">
                            <h5 class="card-title"><i class="fa fa-money" aria-hidden="true"></i> Today's Deposit :
                                {{$todays_fund_deposit}} Tk. </h5>
                        </a>
                        <a href="{{route('admin.expenses.index')}}" class=" text-light">
                            <h5 class="card-title"><i class="fa fa-money" aria-hidden="true"></i> Today's Widthrow :
                                {{$todays_fund_widthrow}} Tk. </h5>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
