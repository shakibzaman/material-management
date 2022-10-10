<div class="card-title">
    <h3>Deposit</h3>
</div>
    <div class="card-body">
        <form action="{{route("admin.bank.deposit.store")}}" method="post">
            @csrf
            <input type="hidden" name="bank_id" value="{{$bank_id}}">
            <div class="form-group">
                <level>Add Deposit</level>
                <input type="number" class="form-control" name="deposit" required>
            </div>
            <div class="form-group">
                <level>Source of Fund</level>
                <select name="fund_id" class="form-control">
                    <option value="">----</option>
                    <option value="1">Main Account</option>
                    <option value="2">Ngonj Account</option>
                    <option value="3">Mirpur Account</option>
                </select>
            </div>
            <div class="form-group">
                <level>Reason</level>
                <input type="text" class="form-control" name="reason" required>
            </div>
            <button type="submit" class="btn btn-primary">Save</button>
        </form>
    </div>
