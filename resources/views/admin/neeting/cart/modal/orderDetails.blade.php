<div class="card-title">
    <h3>Order Details</h3>
</div>
<div class="card-body">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Product</th>
                <th>Color</th>
                <th>Selling Price</th>
                <th>Qty</th>
                <th>Line Total</th>
            </tr>
        </thead>
        <tbody>
        @foreach($orderDetails as $detail)
            <tr>
                <td>{{$detail->product->name}}</td>
                <td>{{$detail->color->name}}</td>
                <td>{{$detail->selling_price}}</td>
                <td>{{$detail->qty}}</td>
                <td>{{$detail->line_total}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
