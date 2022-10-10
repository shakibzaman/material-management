@extends('layouts.admin')
@section('content')
@can('expense_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            @if($department_id == 1)
                <a class="btn btn-primary" href="{{ route("admin.knitting.cart",$department_id) }}">
                    Cart
                </a>
            @else
                <a class="btn btn-success" href="{{ route("admin.showroom.cart",$department_id) }}">
                    Cart
                </a>
            @endif
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        <b>Knitting Order List</b>
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-Expense">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            Invoice ID
                        </th>
                        <th>
                            Customer
                        </th>
                        <th>
                            Sub Total
                        </th>
                        <th>
                            Discount
                        </th>
                        <th>
                            Total
                        </th>
                        <th>
                            Paid
                        </th>
                        <th>
                            Due
                        </th>

                        <th>
                            &nbsp;Action
                        </th>
                    </tr>
                </thead>
                <tbody>
                @foreach($orders as $order)
                <tr>
                    <td>
                        {{$order->id}}
                    </td>
                    <td>
                        {{$order->invoice_id}}
                    </td>
                    <td>
                        {{$order->customer->name}}
                    </td>
                    <td>
                        {{$order->sub_total}}
                    </td>
                    <td>
                        {{$order->discount}}
                    </td>
                    <td>
                        {{$order->total}}
                    </td>
                    <td>
                        {{$order->paid}}
                    </td>
                    <td>
                        {{$order->due}}
                    </td>
                    <td>
                        <a class="btn btn-success text-light btn-xs" data-toggle="modal" id="mediumButton" data-target="#mediumModal"
                           data-attr="{{ route('admin.knitting.order.details',$order->id) }}" title="Return"> Details
                        </a>
                        <a class="btn btn-success text-light btn-xs" data-toggle="modal" id="mediumButton" data-target="#mediumModal"
                           data-attr="{{ route('admin.knitting.order.invoice',$order->id) }}" title="Return"> Invoice
                        </a>
                        @if($order->due >0)
                            <a class="btn btn-success btn-xs text-light" data-toggle="modal" id="mediumButton" data-target="#mediumModal"
                               data-attr="{{ route('admin.order.payment',$order->id) }}" title="Return"> Payment
                            </a>
                        @endif
                        <a class="btn btn-info btn-xs text-light" data-toggle="modal" id="mediumButton" data-target="#mediumModal"
                           data-attr="{{ route('admin.order.payment.history',$order->id) }}" title="Return"> Payment History
                        </a>

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
