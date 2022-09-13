<div class="card-title">
    <h3>Create a Supplier</h3>
</div>
<div class="card-body">
    <form id="supplier-add">
        @csrf
        <div class="form-group">
            <label>Supplier Name</label>
            <input type="text" name="name" class="form-control"" required>
        </div>
        <div class="form-group">
            <label>Supplier Phone</label>
            <input type="text" name="phone" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Supplier Address</label>
            <input type="text" name="address" class="form-control" >
        </div>
        <div class="form-group">
            <label>Opening Balance</label>
            <input type="number" name="opening_balance" class="form-control" >
        </div>
        <button type="submit" class="btn btn-secondary">Add</button>
    </form>
</div>
<script>
    jQuery(document).ready(function () {
        $('form#supplier-add').on('submit', function (e) {
            e.preventDefault();
            searchStockSet();
        });

        function searchStockSet(){
            $.ajax({
                url: '/admin/supplier',
                type: 'POST',
                cache: false,
                data: $('form#supplier-add').serialize(),
                datatype: 'html',
                // datatype: 'application/json',

                beforeSend: function() {
                    // show waiting dialog
                    // waitingDialog.show('Loading...');
                },

                success:function(data){
                    console.log(data);
                    if(data) {
                        if(data.status == 200){
                            Swal.fire({
                                position: 'top-end',
                                icon: 'success',
                                title: data.message,
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
