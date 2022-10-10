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
            <div class="form-group">
                <level>Connected Shop</level>
                <select name="department_id" class="form-control">
                    <option>----</option>
                    <option value="3">Ngonj Showroom</option>
                    <option value="4">Mirpur Showroom</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary">Save</button>
        </form>
    </div>
