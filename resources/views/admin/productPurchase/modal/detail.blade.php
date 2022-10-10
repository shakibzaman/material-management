<h3>Purchase Details</h3>

<table class="table table-bordered table-striped">
    <thead>
    <tr>
        <th> ID </th>
        <th>Material Quantity</th>
        <th>Total price</th>
        <th>Paid price</th>
        <th>Due price</th>
    </tr>
    </thead>
    <tbody>
        <tr>

            <td>
                {{ $material_purchase_info->id }}
            </td>
            <td>
                {{ $material_purchase_info->quantity }}
            </td>
            <td>
                {{ $material_purchase_info->total_price }}
            </td>
            <td>
                {{ $material_purchase_info->paid_amount }}
            </td>
            <td>
                {{ $material_purchase_info->due_amount }}
            </td>
        </tr>
    </tbody>
</table>
<h4>Payment Info</h4>

<table class="table table-bordered table-striped">
    <thead>
    <tr>
        <th> ID </th>
        <th>Amount</th>
        <th>Source</th>
        <th>Source Name</th>
        <th>Reason</th>
    </tr>
    </thead>
    <tbody>
    @foreach($payment_transaction as $payment)
        @php
            if($payment->bank_id == 1){
                $source = $bank_info[$payment->source_type];
            }
            @endphp
    <tr>
        <td>
            {{ $payment->id }}
        </td>
        <td>
            {{ $payment->amount }}
        </td>
        <td>
            {{ $payment->bank_id ==1 ?'Bank':'Shop Account'}}
        </td>
        <td>
            {{ $source->name }}
        </td>
        <td>
            {{ $payment->reason }}
        </td>
    </tr>
    @endforeach
    </tbody>
</table>


