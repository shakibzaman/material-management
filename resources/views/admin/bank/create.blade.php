<div class="card-title">
    <h3>Add Bank Detail</h3>
</div>
    <div class="card-body">
        <form action="{{route("admin.bank.store")}}" method="post">
            @csrf
            <div class="form-group">
                <level>Bank Name</level>
                <input type="text" class="form-control" name="name" required>
            </div>
            <div class="form-group">
                <level>A/C Number</level>
                <input type="text" class="form-control" name="ac_no" required>
            </div>
            <div class="form-group">
                <level>Limit</level>
                <input type="number" class="form-control" name="limit" required>
            </div>
            <div class="form-group">
                <level>Current Balance</level>
                <input type="number" class="form-control" name="current_balance" required>
            </div>
            <div class="form-group">
                <level>Interest % </level>
                <input type="number" class="form-control" name="rate" required step="0.01">
            </div>
            <div class="form-group">
                <level>Interest Type</level>
                <select name="rate_type" id="" class="form-control" required>
                    <option value="daily">daily</option>
                    <option value="monthly">monthly</option>
                    <option value="yearly">yearly</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Save</button>
        </form>
    </div>
