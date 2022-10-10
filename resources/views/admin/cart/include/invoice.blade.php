
<div class="container bootstrap snippets bootdeys">
    <div class="row">
        <div class="col-sm-12">
            <div class="panel panel-default invoice" id="invoice">
                <div class="panel-body">
                    @if($order->due == $order->total)
                    <div class="invoice-ribbon"><div class="ribbon-inner">DUE</div></div>
                    @elseif($order->due > 0)
                        <div class="invoice-ribbon"><div class="ribbon-inner">PARTIAL</div></div>
                        @else
                        <div class="invoice-ribbon"><div class="ribbon-inner">PAID</div></div>
                    @endif
                    <div class="row">

                        <div class="col-sm-6 top-left">
                            <i class="fa fa-rocket"></i>
                        </div>

                        <div class="col-sm-4 top-right">
                            <h3 class="marginright">INVOICE - {{$order->invoice_id}}</h3>
                            <span class="marginright">DATE -{{$order->date}}</span>
                        </div>

                    </div>
                    <hr>
                    <div class="row">

                        <div class="col-md-4 from">
                            <p class="lead marginbottom font-weight-bold">From : Fatema Knitting</p>
                            <p>350 Rhode Island Street</p>
                            <p>Phone: 415-767-3600</p>
                            <p>Email: contact@dynofy.com</p>
                        </div>

                        <div class="col-md-4 to">
                            <p class="lead marginbottom font-weight-bold">To : {{$order->customer->name}}</p>
                            <p>Address: {{$order->customer->address}}</p>
                            <p>Phone: {{$order->customer->phone}}</p>
                            <p>Email: {{$order->customer->email}}</p>

                        </div>

                        <div class="col-md-4 text-right payment-details">
                            <p class="lead marginbottom payment-info font-weight-bold">Payment details</p>
                            <p>Date: {{$order->date}}</p>
                            <p>VAT: DK888-777 </p>
                            <p>Total Amount: <b><strong>৳</strong></b> {{$order->total}}</p>
                        </div>

                    </div>

                    <div class="row table-row">
                        <table class="table table-striped">
                            <thead>
                            <tr>
                                <th class="text-center" style="width:5%">#</th>
                                <th style="width:50%">Item</th>
                                <th class="text-right" style="width:15%">Quantity</th>
                                <th class="text-right" style="width:15%">Unit Price</th>
                                <th class="text-right" style="width:15%">Total Price</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($order->details as $item)
                            <tr>
                                <td class="text-center">1</td>
                                <td>{{$item->product->name}}</td>
                                <td class="text-right">{{$item->qty}}</td>
                                <td class="text-right"><b><strong>৳</strong></b> {{$item->selling_price}}</td>
                                <td class="text-right"><b><strong>৳</strong></b> {{$item->line_total}}0</td>
                            </tr>
                            @endforeach
                            </tbody>
                        </table>

                    </div>

                    <div class="row">
                        <div class="col-md-6 margintop">
                            <p class="lead marginbottom">THANK YOU!</p>

                            <button class="btn btn-success" id="invoice-print"><i class="fa fa-print"></i> Print Invoice</button>
                            <button class="btn btn-danger"><i class="fa fa-envelope-o"></i> Mail Invoice</button>
                        </div>
                        <div class="col-md-6 text-right pull-right invoice-total">
                            <p>Subtotal : <b><strong>৳</strong></b> {{$order->sub_total}}</p>
                            <p>Discount  : <b><strong>৳</strong></b> {{$order->discount}} </p>
                            <p>Total : <b><strong>৳</strong></b> {{$order->total}} </p>
                            <p>Paid : <b><strong>৳</strong></b> {{$order->paid}} </p>
                            <p>Due : <b><strong>৳</strong></b> {{$order->due}} </p>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
