<div class="card">
    <div class="card-title">
        Payment History
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>#</th>
                <th>Date</th>
                <th>Order ID</th>
                <th>Amount</th>
                <th>Details</th>
            </tr>

            </thead>
            <tbody>
            @foreach($payments as $payment)
                <tr>
                    <td>#</td>
                    <td>
                        {{$payment->created_at}}
                    </td>
                    <td>
                        {{$payment->releted_id}}
                    </td>
                    <td>
                        {{$payment->amount}}
                    </td>
                    <td>
                        {{$payment->transaction->reason}}
                    </td>

                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>
