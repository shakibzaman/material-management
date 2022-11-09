@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        Supplied Details
    </div>

    <div class="card-body">
        <div class="mb-2">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th> ID </th>
                        <th>Date</th>
                        <th>inv number</th>
                        <th>Sub total</th>
                        <th>Total</th>
                        <th>Paid</th>
                        <th>Discount</th>
                        <th>Due</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($invoices as $invoice)
                    <tr>

                        <td>
                            {{ $invoice->id }}
                        </td>

                        <td>
                            {{ $invoice->date ?? '' }}
                        </td>
                        <td>
                            {{ $invoice->inv_number ?? '' }}
                        </td>
                        <td>
                            {{$invoice->sub_total ?? 'N/A'}}
                        </td>
                        <td>
                            Tk. {{ $invoice->total }}
                        </td>
                        <td>
                            {{ $invoice->paid }}
                        </td>
                        <td>
                            {{ $invoice->discount }}
                        </td>
                        <td>
                            {{ $invoice->due }}
                        </td>
                        <td>
                            {{$invoice->type==1?'Material':($invoice->type==2?'Raw Product':'Finish Product')}}
                        </td>
                        <td>
                            <a class="btn btn-xs btn-info" href="{{ route('admin.supplier.invoice.show', $invoice->id) }}">
                                Details
                            </a>
                        </td>

                    </tr>
                    @endforeach
                </tbody>
            </table>
            <table class="table table-bordered">
                <tbody>
                    <tr>
                        <th>Total Due</th>
                        <td>{{$invoices->sum('due')}}</td>
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
