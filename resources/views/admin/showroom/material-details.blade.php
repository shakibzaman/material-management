@extends('layouts.admin')
@section('content')
@can('expense_create')
    <div style="margin-bottom: 10px;" class="row">
        <a style="margin-top:20px;" class="btn btn-info" href="{{ url()->previous() }}">
            {{ trans('global.back_to_list') }}
        </a>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        <b>Showroom Product Stock List</b>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-Expense">
                <thead>
                    <tr>
                        <th>
                            Date
                        </th>
                        <th>
                            Product Name
                        </th>
                        <th>
                            Material & Quantity
                        </th>
                        <th>
                            Costing
                        </th>

                    </tr>
                </thead>
                <tbody>
                @foreach($transfer_products as $key =>$product)

                <tr>
                    <td>
                        {{$product->created_at}}
                    </td>
                    <td>
                        {{$product->product->name}} Qty ( {{$product->quantity}} )
                    </td>
                    <td>
                        @foreach($product->material as $material)
                            <ul>
                                <li> Name {{$material->material->name}} -> Qty {{$material->quantity}}</li>
                            </ul>

                        @endforeach

                    </td>
                    <td>
                        @foreach($product->expense as $expense)

                           <table class="table table-bordered">
                               <thead>
                                    <tr>
                                        <th>Amount</th>
                                        <th>Name</th>
                                        <th>Description</th>
                                    </tr>
                               </thead>
                               <tbody>
                                <tbody>
                                    <tr>
                                        <td>{{$expense->amount}}</td>
                                        <td>{{$expense->material->name}}</td>
                                        <td>{{$expense->description}}</td>
                                    </tr>
                               </tbody>
                               </tbody>
                           </table>
                        @endforeach
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>


    </div>
</div>
<div class="modal fade" id="mediumModal" tabindex="-1" role="dialog" aria-labelledby="mediumModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="mediumBody">
                <div>
                    <!-- the result to be displayed apply here -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
@parent
<script>
    $(document).on('click', '#mediumButton', function(event) {
        event.preventDefault();
        let href = $(this).attr('data-attr');
        $.ajax({
            url: href,
            beforeSend: function() {
                $('#loader').show();
            },
            // return the result
            success: function(result) {
                $('#mediumModal').modal("show");
                $('#mediumBody').html(result).show();
            },
            complete: function() {
                $('#loader').hide();
            },
            error: function(jqXHR, testStatus, error) {
                console.log(error);
                alert("Page " + href + " cannot open. Error:" + error);
                $('#loader').hide();
            },
            timeout: 8000
        })
    });
</script>
@endsection
