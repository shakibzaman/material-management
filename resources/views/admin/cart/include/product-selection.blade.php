
<tr>
    <td>
        <input type="text" class="form-control" value="{{$material->color->name}}" name="material_name[]">
        <input type="hidden" class="form-control material_id" value="{{$material->id}}" name="material_id[]">
    </td>
    <td>
        <input type="text" class="form-control quantity" value="" name="quantity[]">
    </td>
    <td>
        <input type="text" class="form-control price" value="" name="price[]">
    </td>
    <td>
        <input type="text" class="form-control line_total" value="" name="line_total[]">
    </td>
    <td class="actions" data-th="">
        <button class="btn btn-danger btn-sm remove-from-cart"><i class="fa fa-trash-o"></i></button>
    </td>
</tr>

<script>
    let sub_total = 0;



    $(".remove-from-cart").click(function (e) {
        var ele = $(this);
        var row_id = ele.parents("tr").remove();
    });
</script>
