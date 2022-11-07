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
                            <a class="btn btn-success text-light"
                               href="{{route('admin.showroom.product.costing',[$department,$key])}}">Product Costing</a>
                            <a class="btn btn-danger text-light" data-toggle="modal" id="mediumButton" data-target="#mediumModal"
                               data-attr="{{route('admin.showroom.product.return',[$department,$key])}}" title="Return"> Return
                            </a>
                            <a class="btn btn-success text-light"
                               href="{{route('admin.showroom.product.return.list',[$department,$key])}}">Return List</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="modal fade" id="mediumModal" tabindex="-1" role="dialog" aria-labelledby="mediumModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
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
