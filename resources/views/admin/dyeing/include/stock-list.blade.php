<h3>Material's Info</h3>
    @if(empty($sets))
        <h5 class="border border-danger text-danger text-center">No Set is match for your quantity, you can enter quantity</h5>
    @else
        <h5 class="border border-success text-success text-center">Set is match for your quantity,auto filled</h5>
    @endif
<form id="material_stock_in" method="post">
    @csrf
    @foreach($materials as $material)
        @php
            if(!empty($sets)){
             $SetMaterial = \App\StockSetMaterial::where('stock_set_id',$sets->id)->get()->keyBy('material_id')->toArray();
            if(isset($SetMaterial[$material->id])){
                $value = $SetMaterial[$material->id];
            }
            else{
                $value = '';
            }
            }
            $material_quantity = \App\MaterialIn::where('material_id',$material->id)->where('rest','>',0)->count();
            $disabled = $material_quantity == 0 ? "disabled='disabled'" : "";
        @endphp
    <label for="">{{$material->name}}  {{$material_quantity==0?"(No Stock)":''}}</label>
    <input type="number" name="material_qty[{{$material->id}}]" class="form-control material_id" {{$disabled}} value="@isset($value){{ is_array($value)?$value['material_quantity']:''}}@endisset">
    @endforeach
    <input type="hidden" name="product_id" value="{{$product_id}}">
    <input type="hidden" name="color_id" value="{{$color_id}}">
    <input type="hidden" name="quantity" value="{{$quantity}}">
    <input type="hidden" name="company_id" value="{{$company_id}}">
    <input type="hidden" name="process_fee" value="{{$process_fee}}">
    <input type="hidden" name="showroom_id" value="{{$showroom_id}}">

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
                if(material_value>0) {
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
                    url: '/admin/dyeing/stock/in',
                    type: 'POST',
                    cache: false,
                    data: $('form#material_stock_in').serialize(),
                    datatype: 'html',
                    // datatype: 'application/json',

                    beforeSend: function() {

                    },

                    success:function(data){
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
