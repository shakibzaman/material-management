
<div class="card-title">
    <h5>Return to Supplier</h5>
</div>
<div class="card-body">
    <form id="return-material">
        @csrf
        <table class="table table-bordered">
            <tbody>
                <tr>
                    <th>Available Stock</th>
                    <td>{{$materialStock->rest}}</td>
                </tr>
                <tr>
                    <th>Return Qty *</th>
                    <td>
                        <input type="hidden" name="id" value="{{$materialStock->id}}">
                        <input type="number" name="quantity" required>
                    </td>
                </tr>
                <tr>
                    <th>Reason *</th>
                    <td>
                        <input type="text" name="reason" required>
                    </td>
                </tr>
            </tbody>
        </table>
        <button type="submit">Return</button>
    </form>
</div>

<script>
    jQuery(document).ready(function () {
        $('form#return-material').on('submit', function (e) {
            e.preventDefault();
            searchStockSet();
        });

        function searchStockSet(){
            $.ajax({
                url: '/admin/return/material/stock',
                type: 'POST',
                cache: false,
                data: $('form#return-material').serialize(),
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