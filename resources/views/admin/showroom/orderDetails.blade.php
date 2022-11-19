<div class="card-title">
    <h3>Order Details</h3>
</div>
<div class="card-body">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Color</th>
                <th>Selling Price</th>
                <th>Qty</th>
                <th>Unit</th>
                <th>Line Total</th>
            </tr>
        </thead>
        <tbody>
        @foreach($orderDetails as $detail)
            <tr>
                <td>{{$detail->color ?? '' ? $detail->color->name : ''}}</td>
                <td>{{$detail->selling_price}}</td>
                <td>{{$detail->unit == 2 ? ($detail->qty * 2.20462262) : $detail->qty}}</td>
                <td>{{$detail->unit == 1 ? 'KG' : 'Pound'}}</td>
                <td>{{$detail->line_total}}</td>
            </tr>
        @endforeach
        </tbody>
    </table>
</div>
