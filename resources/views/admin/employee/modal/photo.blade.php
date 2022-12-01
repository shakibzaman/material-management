<div class="card-title">
    <h3>Profile Photo</h3>
</div>
<div class="card-body">
    @if($employee_detail->image != Null)
        <div class="image-box">
            <h5> Your Uploaded Photo</h5>
            <img style="border:2px solid" src="/images/{{$employee_detail->image}}" alt="" width="300">
        </div>
    @endif

    <form action="/admin/employee/photo/{{$employee_detail->id}}" method="post" enctype="multipart/form-data">
        @csrf
        <div class="form-group">
            <label for="phone">Upload Now *</label>
            <input type="hidden" name="employee_id" value="{{}}">
            <input type="file" name="image" class="form-control" required>
        </div>
        <input class="btn btn-danger" type="submit" value="Upload">
    </form>

</div>
