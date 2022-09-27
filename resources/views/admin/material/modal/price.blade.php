<div class="card-title">
    Price
</div>
<div class="card-body">
    <form action="/admin/material-in/price" method="POST">
        @csrf
        <input type="hidden" value="{{$price->id}}" name="material_id">
        <div class="form-group ">
            <label for="due_amount">Material Price </label>
            <input type="text" id="material_price" name="material_price" class="form-control" value={{ old('material_price', isset($price) ? $price->material_price : '') }}>
        </div>
        <div class="form-group">
            <label for="due_amount">Knitting Price </label>
            <input type="text" id="knitting_price" name="knitting_price" class="form-control" value={{ old('knitting_price', isset($price) ? $price->knitting_price : '') }}>

        </div>
        <div class="form-group">
            <label for="due_amount">Showroom Price </label>
            <input type="text" id="selling_price" name="selling_price" class="form-control" value={{ old('selling_price', isset($price) ? $price->selling_price : '')}}>
        </div>
    <div>
        <input class="btn btn-danger" type="submit" value="Save">
    </div>
</form>
</div>
