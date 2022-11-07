@php
    $total = $showroomStock->sum('rest_quantity');
    @endphp
<div class="card-title">
    <h5>Return to main stock</h5>
</div>

    <div class="card-body">
        <table class="table table-bordered">
            <tbody>
            <tr>
                <th>Available Stock</th>
                <td>{{$total}}</td>
                <input type="hidden" name="total_stock" value="{{$total}}">

            </tr>
            @foreach($rest_quantity as $key=>$quantity)
                <tr>
                    <th>{{$material_key_by[$key]->name}}</th>
                    <td>{{$quantity->sum('rest_quantity')}}</td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <form id = "return_stock_to_main">
            <input type="hidden" name="_token" value="{{csrf_token()}}">
            <input type="hidden" name="showroom_id" value="{{$showroom_id}}">
            <input type="hidden" name="color_id" value="{{$color_id}}">
            <input type="hidden" name="type" value="{{$type}}">
            <table class="table table-bordered">
            <tr>
                <th>Transfer To</th>
                <td>Main Stock</td>
            </tr>
            <tr>
                <th>Transfer Stock</th>
                <td>
                    <div class="form-group {{ $errors->has('transfer_stock') ? 'has-error' : '' }}">
                        <label for="name"> Enter Return Quantity *</label>
                        <input type="number" id="transfer_stock" name="transfer_stock" class="form-control" placeholder="Enter Quantity" required>
                        @if($errors->has('transfer_stock'))
                            <em class="invalid-feedback">
                                {{ $errors->first('transfer_stock') }}
                            </em>
                        @endif
                        <p class="helper-block">
                            {{ trans('cruds.expense.fields.entry_date_helper') }}
                        </p>
                    </div>
                    <div class="form-group {{ $errors->has('transfer_stock') ? 'has-error' : '' }}">
                        <label for="name"> Enter Reason *</label>
                        <input type="text" id="reason" name="reason" class="form-control" placeholder="Enter Reason" required>
                        @if($errors->has('reason'))
                            <em class="invalid-feedback">
                                {{ $errors->first('reason') }}
                            </em>
                        @endif
                        <p class="helper-block">
                            {{ trans('cruds.expense.fields.entry_date_helper') }}
                        </p>
                    </div>
                </td>
            </tr>
        </table>
            <button id="return_stock_to_main" type="submit" class="btn btn-primary pull-right">Return</button>
        </form>
    </div>
<script>
    jQuery(document).ready(function () {
        $('form#return_stock_to_main').on('submit', function (e) {
            e.preventDefault();
                searchStockSet();
        });

        function searchStockSet(){
            $.ajax({
                url: '/admin/showroom/return/product',
                type: 'POST',
                cache: false,
                data: $('form#return_stock_to_main').serialize(),
                datatype: 'html',
                // datatype: 'application/json',

                beforeSend: function() {
                    // show waiting dialog
                    // waitingDialog.show('Loading...');
                },

                success:function(data){
                    console.log(data);
                    if(data) {
                        if(data.status == 103){
                            Swal.fire({
                                icon: 'error',
                                title: 'Oops...',
                                text: data.message,
                                footer: 'Check your Stock'
                            })
                        }
                        else{
                            Swal.fire({
                                position: 'top-end',
                                icon: 'success',
                                title: 'Product Successfully Return',
                                showConfirmButton: false,
                                timer: 1500
                            })
                            $('#mediumModal').modal('hide');
                            window.location.reload();

                        }
                    }
                },
                error:function(data){
                    console.log(data);
                    // sweet alert
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: "Unable to load data form server",
                        footer: 'Contact with Your Admin'
                    })
                    // swal("Error", 'Unable to load data form server', "error");
                }
            });
        }
    })
</script>

