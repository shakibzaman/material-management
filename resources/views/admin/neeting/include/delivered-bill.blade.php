<h3>Cost Preview</h3>
<table class="table">
    <thead>
        <tr>
            <th>Qty</th>
            <th>Process Bill / Unit</th>
            <th>Cost Line Total</th>
        </tr>
    </thead>
    <tbody>
    @php
        $total = 0;
        @endphp
    @foreach($stockDetails as $key=> $detail)
        @php
            if(isset($stocks[$key])){
               $stock = ($stocks[$key]);
               $line_total = $detail * $stock->process_fee;
               $total += $line_total;
            }
            @endphp
        <tr>
            <td>{{$detail}}</td>
            <td>{{$stock->process_fee}}</td>
            <td>{{$detail * $stock->process_fee}} </td>
            <input type="hidden" name="stock_id[]" value="{{$key}}">
            <input type="hidden" name="stock_value[]" value="{{$detail}}">
        </tr>
    @endforeach
    </tbody>
</table>
<table class="table table-bordered">
    <tbody>
    <tr>
        <th>Total Costing</th>
        <td>
            <input type="hidden" name="total_process_fee" value="{{$total}}">
            {{$total}}</td>
    </tr>
    </tbody>
</table>


