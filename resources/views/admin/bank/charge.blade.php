<div class="card-title">
    <h3>Service Charge</h3>
</div>
    <div class="card-body">
        <form action="{{route("admin.bank.store.charge")}}" method="post">
            @csrf
            <input type="hidden" name="bank_id" value="{{$bank_info->id}}">
            <div class="form-group">
                <level>Add Service Charge Amount</level>
                <input type="number" class="form-control" name="charge" required>
            </div>
            <div class="form-group">
                <level>Reason</level>
                <input type="text" class="form-control" name="reason" required>
            </div>
            <button type="submit" class="btn btn-primary">Save</button>
        </form>
    </div>
