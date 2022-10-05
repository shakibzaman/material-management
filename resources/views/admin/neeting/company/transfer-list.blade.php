@extends('layouts.admin')
@section('content')
{{--    @can('expense_create')--}}
{{--        <div style="margin-bottom: 10px;" class="row">--}}
{{--            <div class="col-lg-12">--}}
{{--                <a class="btn btn-success" href="{{ route("admin.expenses.create") }}">--}}
{{--                    {{ trans('global.add') }} {{ trans('cruds.expense.title_singular') }}--}}
{{--                </a>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    @endcan--}}
    <div class="card">
        <div class="card-header">
            Stock List of Knitting
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class=" table table-bordered table-striped table-hover datatable datatable-Expense">
                    <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            ID
                        </th>
                        <th>
                            Date
                        </th>
                        <th>Product</th>
                        <th>
                            Product Quantity
                        </th>
                        <th>
                            &nbsp;Action
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($nettingsData as $key =>$nettingData)
                                            @php
                                                if(isset($transfer_products[$nettingData->id])) {
                                                        $productTotalSum = $transfer_products[$nettingData->id]->sum('quantity');
                                                        $product_name = $transfer_products[$nettingData->id][0]['product']['name'];
                                                    }
                                            @endphp
                                            <tr data-entry-id="{{ $nettingData->id }}">
                                                <td>

                                                </td>
                                                <td>
                                                    {{ $nettingData->id ?? '' }}
                                                </td>
                                                <td>
                                                    {{ $nettingData->date ?? '' }}
                                                </td>
                                                <td>{{$product_name}}</td>
                                                <td>
                                                    {{$productTotalSum}}
                                                </td>
                                                <td>
                                                    @if($nettingData->company->type == 1)
                                                        <a class="btn btn-xs btn-primary" href="{{ route('admin.transfer.show', $nettingData->id) }}">
                                                            {{ trans('global.view') }}
                                                        </a>
                                                    @else
                                                        <a class="btn btn-xs btn-primary" href="{{ route('admin.transfer.other.show', $nettingData->id) }}">
                                                            {{ trans('global.view') }}
                                                        </a>
                                                    @endif
                                                    <a class="btn btn-xs btn-primary" href="{{ route('admin.netting.transfer.expense', $nettingData->id) }}">
                                                        Expense
                                                    </a>
                                                </td>

                                            </tr>
                                        @endforeach
                    </tbody>
                </table>
            </div>


        </div>
    </div>
@endsection
@section('scripts')
    @parent

@endsection
