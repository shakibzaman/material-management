<h3>Material's Info</h3>
<form id="material_stock_in" method="post">
    @csrf
    @foreach($materials as $material)
    <label for="">{{$material->name}}</label>
    <input type="number" name="material_qty[{{$material->id}}]" id="material_id"  class="form-control material_id">
    @endforeach
    <input type="hidden" name="product_id" value="{{$product_id}}">
    <input type="hidden" name="quantity" value="{{$quantity}}">
    <input type="hidden" name="company_id" value="{{$company_id}}">
    <input type="hidden" name="process_fee" value="{{$process_fee}}">

    <button type="submit" class="btn btn-primary submit" data-btn-name="generate">Stock Transfer</button>
</form>

<script>
    jQuery(document).ready(function () {
            $('form#material_stock_in').on('submit', function (e) {
                e.preventDefault();
                let material_value = 0;
                let material_id = $(".material_id");
                for(let i = 0; i < material_id.length; i++){
                    material_value+=Number($(material_id[i]).val());
                }
                // console.log(material_value);
                if(material_value>0){
                    // ajax request
                    searchStockSet();
                }
                else{
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: "Please Enter material quantity First",
                        // footer: 'Contact with Your Admin'
                    })
                }

            });

            function searchStockSet(){
                $.ajax({
                    url: '/admin/neeting/stock/in',
                    type: 'POST',
                    cache: false,
                    data: $('form#material_stock_in').serialize(),
                    datatype: 'html',
                    // datatype: 'application/json',

                    beforeSend: function() {

                    },

                    success:function(data) {
                        console.log(data);
                        if (data.status == 104) {
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
                                title: 'Data Transferred Successfully ',
                                showConfirmButton: false,
                                timer: 1500
                            })
                            window.location = '{{ route('admin.neeting.index') }}'
                        }
                    },
                    error:function(data){
                        console.log(data);
                    }
                });
            }
        })
</script>
