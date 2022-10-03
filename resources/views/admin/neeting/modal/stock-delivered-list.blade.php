<div class="card">
    <div class="card-title">
        <h3>Delivered Report</h3>
    </div>
    <div class="card-body">
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Product Name</th>
                    <th>Qty</th>
                    <th>Total Process Fee</th>
                    <th>Total Bill</th>
                    <th>Sub Total</th>
                    <th>Total</th>
                    <th>Discount</th>
                    <th>Paid</th>
                    <th>Due</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            @foreach($delivered_list as $list)
                <tr>
                    <td>{{$list->date}}</td>
                    <td>{{$list->product->name}}</td>
                    <td>{{$list->quantity}}</td>
                    <td>{{$list->process_fee}}</td>
                    <td>{{$list->bill_fee}}</td>
                    <td>{{$list->sub_total}}</td>
                    <td>{{$list->total}}</td>
                    <td>{{$list->discount}}</td>
                    <td>{{$list->paid}}</td>
                    <td>{{$list->due}}</td>
                    <td>
{{--                        <a class="btn btn-success text-light btn-xs" data-toggle="modal" id="mediumButton" data-target="#mediumModal"--}}
{{--                           data-attr="{{ route('admin.netting.company.delivered.list', 2) }}"> Delivered List--}}
{{--                        </a>--}}
                        <a href="" class="btn btn-primary">Delivered List</a>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

