@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} Materials
    </div>

    <div class="card-body">
        <div class="mb-2">
            <table class="table table-bordered table-striped">
                <tbody>
                    <tr>
                        <th>
                            ID
                        </th>
                        <td>
                            {{ $material->id }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Material Name
                        </th>
                        <td>
                            {{ $material->name ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Material Quantity
                        </th>
                        <td>
                            {{ $material->quantity ?? '' }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Unit
                        </th>
                        <td>
                            {{$material->units->name ?? 'N/A'}}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Buying Date
                        </th>
                        <td>
                            Tk. {{ $material->buying_date }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Unit Price
                        </th>
                        <td>
                            {{ $material->unit_price }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Total price
                        </th>
                        <td>
                            {{ $material->total_price }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Rest Quantity
                        </th>
                        <td>
                            {{ $material->rest }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Supplied By
                        </th>
                        <td>
                            {{ $material->supplied_by }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Invoice Number
                        </th>
                        <td>
                            {{ $material->inv_number }}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Purchased By
                        </th>
                        <td>
                            {{$material->employee->name}}
                        </td>
                    </tr>
                    <tr>
                        <th>
                            Entry By
                        </th>
                        <td>
                           {{$material->user->name}}
                        </td>   
                    </tr>
                    <tr>
                        <th>
                            Created at
                        </th>
                        <td>
                            {{ $material->created_at }}
                        </td>
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