<div class="card-title">
    Material Details
</div>
<div class="card-body">
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>id</th>
                <th>Material Name</th>
                <th>Qty</th>
                <th>Product Name</th>
                <th>Product Qty</th>
                <th>Transfer Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($transfer_material_detail as $key => $material_detail)
            <tr>
                
                <td>
                    @foreach($material_detail as $idKey=> $detail)
                    <tr>
                        <td>
                            {{$detail->id}}
                        </td>
                        <th>
                            {{$detail->name}}
                        </th>
                        <td>
                            {{$detail->quantity}}
                        </td>
                        <td>
                            {{$detail->product_name}}
                        </td>
                        <td>
                            {{$detail->product_quantity}}
                        </td>
                        <td>
                            {{$detail->created_at}}
                        </td>
                        
                    </tr>
                    @endforeach
                </td>
                
                
                
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
