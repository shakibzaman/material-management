@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} Materials
    </div>

    <div class="card-body">
        <div class="mb-2">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th> ID </th>
                        <th>Material Name</th>
                        <th>Material Quantity</th>
                        <th>Unit</th>
                        <th>Buying Date</th>
                        <th>Unit Price</th>
                        <th>Total price</th>
                        <th>Rest Quantity</th>
                        <th>Supplied By</th>
                        <th>Invoice Number</th>
                        <th>Purchased By</th>
                        <th>Entry By</th>
                        <th>Created at</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($materials as $material)
                    <tr>
                        
                        <td>
                            {{ $material->id }}
                        </td>
                    
                        <td>
                            {{ $material->material->name ?? '' }}
                        </td>
                        <td>
                            {{ $material->quantity ?? '' }}
                        </td>
                        <td>
                            {{$material->units->name ?? 'N/A'}}
                        </td>
                        <td>
                            Tk. {{ $material->buying_date }}
                        </td>
                        <td>
                            {{ $material->unit_price }}
                        </td>
                        <td>
                            {{ $material->total_price }}
                        </td>
                        <td>
                            {{ $material->rest }}
                        </td>
                    
                        <td>
                            {{ $material->supplied_by }}
                        </td>
                
                        <td>
                            {{ $material->inv_number }}
                        </td>
                    
                        <td>
                            {{$material->employee->name}}
                        </td>
                    
                        <td>
                           {{$material->user->name}}
                        </td>   
                    
                        <td>
                            {{ $material->created_at }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <a style="margin-top:20px;" class="btn btn-default" href="{{ url()->previous() }}">
                {{ trans('global.back_to_list') }}
            </a>
        </div>


    </div>
</div>
@endsection