<div class="card-title">
    <h3>Add Fund Detail</h3>
</div>
    <div class="card-body">
        <form action="{{route("admin.fund.store")}}" method="post">
            @csrf
            <div class="form-group">
                <level>Fund Name</level>
                <input type="text" class="form-control" name="name" required>
            </div>
            <div class="form-group">
                <level>Current Balance</level>
                <input type="number" class="form-control" name="current_balance" required>
            </div>
            <button type="submit" class="btn btn-primary">Save</button>
        </form>
    </div>
