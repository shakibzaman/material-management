<div class="card-title">
    <h3>Deposit</h3>
</div>
    <div class="card-body">
        <form action="{{route("admin.fund.deposit.store")}}" method="post">
            @csrf
            <input type="hidden" name="fund_id" value="{{$fund_id}}">
            <div class="form-group">
                <level>Add Deposit</level>
                <input type="number" class="form-control" name="deposit" required>
            </div>
            <div class="form-group">
                <level>Source of Fund</level>
                <input type="text" class="form-control" name="reason" required>
            </div>
            <button type="submit" class="btn btn-primary">Save</button>
        </form>
    </div>
