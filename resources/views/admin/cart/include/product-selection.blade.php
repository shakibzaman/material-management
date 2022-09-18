
<tr>
    <td>
        <input type="text" class="form-control" value="{{$material->name}}" name="material_name[]">
        <input type="hidden" class="form-control material_id" value="{{$material->id}}" name="material_id[]">
    </td>
    <td>
        <input type="text" class="form-control" value="{{$material->id}}" name="price[]">
    </td>
    <td>
        <input type="text" class="form-control" value="" name="quantity[]">
    </td>
    <td>
        <input type="text" class="form-control" value="" name="line_total[]">
    </td>
</tr>