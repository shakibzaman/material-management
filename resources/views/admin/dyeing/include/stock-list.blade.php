<h3>Material's Info</h3>
<form id="material_stock_in" method="post">
    @csrf
    @foreach($materials as $material)
    <label for="">{{$material->name}}</label>
    <input type="number" name="material_qty[{{$material->id}}]" class="form-control">
    @endforeach
    <input type="hidden" name="product_id" value="{{$product_id}}">
    <input type="hidden" name="color_id" value="{{$color_id}}">
    <input type="hidden" name="quantity" value="{{$quantity}}">
    <input type="hidden" name="company_id" value="{{$company_id}}">
    <input type="hidden" name="process_fee" value="{{$process_fee}}">
    
    <button type="submit" class="btn btn-primary submit" data-btn-name="generate">Stock Transfer</button>
</form>

<script>
    jQuery(document).ready(function () {
            $('form#material_stock_in').on('submit', function (e) {
                e.preventDefault();
                    // ajax request
                searchStockSet();
            });

            function searchStockSet(){
                $.ajax({
                    url: '/admin/dyeing/stock/in',
                    type: 'POST',
                    cache: false,
                    data: $('form#material_stock_in').serialize(),
                    datatype: 'html',
                    // datatype: 'application/json',

                    beforeSend: function() {
                        
                    },

                    success:function(data){
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
                            window.location = '{{ route('admin.dyeing.index') }}'
                        }
                    },
                    error:function(data){
                        console.log(data);
                    }
                });
            }
        })
</script>
