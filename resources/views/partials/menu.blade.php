<div class="sidebar">
    <nav class="sidebar-nav">

        <ul class="nav">
            @can('user_management_access')
                <li class="nav-item nav-dropdown">
                    <a class="nav-link  nav-dropdown-toggle" href="#">
                        <i class="fa-fw fas fa-users nav-icon">

                        </i>
                        {{ trans('cruds.userManagement.title') }}
                    </a>
                    <ul class="nav-dropdown-items">
                        @can('permission_access')
                            <li class="nav-item">
                                <a href="{{ route("admin.permissions.index") }}" class="nav-link {{ request()->is('admin/permissions') || request()->is('admin/permissions/*') ? 'active' : '' }}">
                                    <i class="fa-fw fas fa-unlock-alt nav-icon">

                                    </i>
                                    {{ trans('cruds.permission.title') }}
                                </a>
                            </li>
                        @endcan
                        @can('role_access')
                            <li class="nav-item">
                                <a href="{{ route("admin.roles.index") }}" class="nav-link {{ request()->is('admin/roles') || request()->is('admin/roles/*') ? 'active' : '' }}">
                                    <i class="fa-fw fas fa-briefcase nav-icon">

                                    </i>
                                    {{ trans('cruds.role.title') }}
                                </a>
                            </li>
                        @endcan
                        @can('user_access')
                            <li class="nav-item">
                                <a href="{{ route("admin.users.index") }}" class="nav-link {{ request()->is('admin/users') || request()->is('admin/users/*') ? 'active' : '' }}">
                                    <i class="fa-fw fas fa-user nav-icon">

                                    </i>
                                    {{ trans('cruds.user.title') }}
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan
            @can('neeting_management_access')
                <li class="nav-item nav-dropdown">
                    <a class="nav-link  nav-dropdown-toggle" href="#">
                        <i class="fa-fw fas fa-users nav-icon">

                        </i>
                        Knitting
                    </a>
                    <ul class="nav-dropdown-items">
                        @can('neeting_stock_access')
                            <li class="nav-item">
                                <a href="{{ route("admin.neeting.index") }}" class="nav-link {{ request()->is('admin/neeting') || request()->is('admin/neeting/index') ? 'active' : '' }}">
                                    <i class="fa-fw fas fa-list nav-icon">

                                    </i>
                                    Product Stock
                                </a>
                            </li>
                        @endcan
                        @can('neeting_expense_list')
                            <li class="nav-item">
                                <a href="{{ route("admin.netting.all.expense") }}" class="nav-link {{ request()->is('admin/neeting/all/expense') || request()->is('admin/neeting/all/expense/*') ? 'active' : '' }}">
                                    <i class="fa-fw fas fa-list nav-icon">

                                    </i>
                                    Expense List
                                </a>
                            </li>
                                <li class="nav-item">
                                    <a href="{{ route("admin.netting.all.income") }}" class="nav-link {{ request()->is('admin/netting/all/income') || request()->is('admin/netting/all/income/*') ? 'active' : '' }}">
                                        <i class="fa-fw fas fa-list nav-icon">

                                        </i>
                                        Income List
                                    </a>
                                </li>
                        @endcan
{{--                            <a href="{{ route("admin.knitting.orders",1) }}" class="nav-link {{ request()->is('admin/knitting/orders') || request()->is('admin/knitting/orders') ? 'active' : '' }}">--}}
{{--                                <i class="fa-fw fas fa-list nav-icon">--}}

{{--                                </i>--}}
{{--                                Knitting Orders--}}
{{--                            </a>--}}
                    </ul>
                </li>
            @endcan
            @can('dyeing_management_access')
                <li class="nav-item nav-dropdown">
                    <a class="nav-link  nav-dropdown-toggle" href="#">
                        <i class="fa-fw fas fa-users nav-icon">

                        </i>
                        Dyeing
                    </a>
                    <ul class="nav-dropdown-items">
                        @can('stock_set_access')
                            <li class="nav-item">
                                <a href="{{ route("admin.stock.set") }}" class="nav-link {{ request()->is('admin/stock') || request()->is('admin/stock/set') ? 'active' : '' }}">
                                    <i class="fa-fw fas fa-list nav-icon">

                                    </i>
                                    Stock Set
                                </a>
                            </li>
                        @endcan
                            @can('dyeing_stock_access')
                            <li class="nav-item">
                                <a href="{{ route("admin.dyeing.index") }}" class="nav-link {{ request()->is('admin/dyeing') || request()->is('admin/dyeing/index') ? 'active' : '' }}">
                                    <i class="fa-fw fas fa-list nav-icon">

                                    </i>
                                    Product Stock
                                </a>
                            </li>
                        @endcan
                        @can('dyeing_expense_list')
                            <li class="nav-item">
                                <a href="{{ route("admin.dyeing.all.expense") }}" class="nav-link {{ request()->is('admin/dyeing/all/expense') || request()->is('admin/dyeing/all/expense/*') ? 'active' : '' }}">
                                    <i class="fa-fw fas fa-list nav-icon">

                                    </i>
                                    Expense List
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan
            @can('dyeing_management_access')
                <li class="nav-item nav-dropdown">
                    <a class="nav-link  nav-dropdown-toggle" href="#">
                        <i class="fa-fw fas fa-users nav-icon">

                        </i>
                        SHOWROOM NGONJ
                    </a>
                    <ul class="nav-dropdown-items">

                        @can('stock_set_access')
                            <li class="nav-item">
                                <a href="{{ route("admin.showroom.cart",3) }}" class="nav-link {{ request()->is('admin/showroom/stock') || request()->is('admin/showroom/stock') ? 'active' : '' }}">
                                    <i class="fa-fw fas fa-list nav-icon">

                                    </i>
                                    Showroom POS
                                </a>
                            </li>
                            <li class="nav-item">
                                <a href="{{ route("admin.showroom.product.list",3) }}" class="nav-link {{ request()->is('admin/showroom/stock') || request()->is('admin/showroom/stock') ? 'active' : '' }}">
                                    <i class="fa-fw fas fa-list nav-icon">

                                    </i>
                                    Showroom Product
                                </a>
                            </li>
                        @endcan
{{--                            @can('stock_set_access')--}}
{{--                            <li class="nav-item">--}}
{{--                                <a href="{{ route("admin.showroom.stock",3) }}" class="nav-link {{ request()->is('admin/showroom/stock') || request()->is('admin/showroom/stock') ? 'active' : '' }}">--}}
{{--                                    <i class="fa-fw fas fa-list nav-icon">--}}

{{--                                    </i>--}}
{{--                                    Showroom Stock--}}
{{--                                </a>--}}
{{--                            </li>--}}
{{--                        @endcan--}}
                            <li class="nav-item">
                                <a href="{{ route("admin.showroom.orders",3) }}" class="nav-link {{ request()->is('admin/showroom/stock') || request()->is('admin/showroom/stock') ? 'active' : '' }}">
                                    <i class="fa-fw fas fa-list nav-icon">

                                    </i>
                                    Showroom Orders
                                </a>
                            </li>
                    </ul>
                </li>
            @endcan
            @can('dyeing_management_access')
                <li class="nav-item nav-dropdown">
                    <a class="nav-link  nav-dropdown-toggle" href="#">
                        <i class="fa-fw fas fa-users nav-icon">

                        </i>
                        SHOWROOM MIRPUR
                    </a>
                    <ul class="nav-dropdown-items">
                        @can('stock_set_access')
                            <li class="nav-item">
                                <a href="{{ route("admin.showroom.cart",4) }}" class="nav-link {{ request()->is('admin/showroom/stock') || request()->is('admin/showroom/stock') ? 'active' : '' }}">
                                    <i class="fa-fw fas fa-list nav-icon">

                                    </i>
                                    Showroom POS
                                </a>
                            </li>
                        @endcan
                        @can('stock_set_access')
                            <li class="nav-item">
                                <a href="{{ route("admin.showroom.product.list",4) }}" class="nav-link {{ request()->is('admin/showroom/stock') || request()->is('admin/showroom/stock') ? 'active' : '' }}">
                                    <i class="fa-fw fas fa-list nav-icon">

                                    </i>
                                    Showroom Product
                                </a>
                            </li>
{{--                            <li class="nav-item">--}}
{{--                                <a href="{{ route("admin.showroom.stock",4) }}" class="nav-link {{ request()->is('admin/showroom/stock') || request()->is('admin/showroom/stock') ? 'active' : '' }}">--}}
{{--                                    <i class="fa-fw fas fa-list nav-icon">--}}

{{--                                    </i>--}}
{{--                                    Finish Product--}}
{{--                                </a>--}}
{{--                            </li>--}}
                        @endcan
                            <li class="nav-item">
                                <a href="{{ route("admin.showroom.orders",4) }}" class="nav-link {{ request()->is('admin/showroom/stock') || request()->is('admin/showroom/stock') ? 'active' : '' }}">
                                    <i class="fa-fw fas fa-list nav-icon">

                                    </i>
                                    Showroom Orders
                                </a>
                            </li>
                    </ul>
                </li>
            @endcan
            @can('dyeing_management_access')
                    <li class="nav-item nav-dropdown">
                        <a class="nav-link  nav-dropdown-toggle" href="#">
                            <i class="fa-fw fas fa-users nav-icon">

                            </i>
                            Accounts
                        </a>
                        <ul class="nav-dropdown-items">
                            @can('stock_set_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.bank.index") }}" class="nav-link {{ request()->is('admin/bank/index') || request()->is('admin/bank/index') ? 'active' : '' }}">
                                        <i class="fa-fw fas fa-list nav-icon">

                                        </i>
                                        Bank Info
                                    </a>
                                </li>
                                <li class="nav-item">
                                    <a href="{{ route("admin.fund.index") }}" class="nav-link {{ request()->is('admin/fund/index') || request()->is('admin/fund/index') ? 'active' : '' }}">
                                        <i class="fa-fw fas fa-list nav-icon">

                                        </i>
                                        Main Account
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan
            @can('employee_management_access')
                <li class="nav-item nav-dropdown">
                    <a class="nav-link  nav-dropdown-toggle" href="#">
                        <i class="fa-fw fas fa-users nav-icon">

                        </i>
                        HR Management
                    </a>
                    <ul class="nav-dropdown-items">
                        @can('employee_access')
                            <li class="nav-item">
                                <a href="{{ route("admin.employee.index") }}" class="nav-link {{ request()->is('admin/employee') || request()->is('admin/employee/*') ? 'active' : '' }}">
                                    <i class="fa-fw fas fa-list nav-icon">

                                    </i>
                                    Employee
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan
            @can('employee_management_access')
                <li class="nav-item nav-dropdown">
                    <a class="nav-link  nav-dropdown-toggle" href="#">
                        <i class="fa-fw fas fa-users nav-icon">

                        </i>
                        Suppliers
                    </a>
                    <ul class="nav-dropdown-items">
                        @can('employee_access')
                            <li class="nav-item">
                                <a href="{{ route("admin.supplier.index") }}" class="nav-link {{ request()->is('admin/supplier') || request()->is('admin/supplier/*') ? 'active' : '' }}">
                                    <i class="fa-fw fas fa-list nav-icon">

                                    </i>
                                    Supplier
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan
            @can('employee_management_access')
                <li class="nav-item nav-dropdown">
                    <a class="nav-link  nav-dropdown-toggle" href="#">
                        <i class="fa-fw fas fa-users nav-icon">

                        </i>
                        Customers
                    </a>
                    <ul class="nav-dropdown-items">
                        @can('employee_access')
                            <li class="nav-item">
                                <a href="{{ route("admin.customer.index") }}" class="nav-link {{ request()->is('admin/customer') || request()->is('admin/customer/*') ? 'active' : '' }}">
                                    <i class="fa-fw fas fa-list nav-icon">

                                    </i>
                                    Customer
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan
            @can('material_management_access')
                <li class="nav-item nav-dropdown">
                    <a class="nav-link  nav-dropdown-toggle" href="#">
                        <i class="fa-fw fas fa-users nav-icon">

                        </i>
                        Material Management
                    </a>
                    <ul class="nav-dropdown-items">
                        @can('material_config_access')
                            <li class="nav-item">
                                <a href="{{ route("admin.material-config.index") }}" class="nav-link {{ request()->is('admin/material-config') || request()->is('admin/material-config/*') ? 'active' : '' }}">
                                    <i class="fa-fw fas fa-list nav-icon">

                                    </i>
                                    Create Material
                                </a>
                            </li>
                        @endcan
                    </ul>
                    <ul class="nav-dropdown-items">
                        @can('material_access')
                            <li class="nav-item">
                                <a href="{{ route("admin.material-in.index") }}" class="nav-link {{ request()->is('admin/material-in') || request()->is('admin/material-in/*') ? 'active' : '' }}">
                                    <i class="fa-fw fas fa-list nav-icon">

                                    </i>
                                    Material Purchased
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan
            @can('product_management_access')
                <li class="nav-item nav-dropdown">
                    <a class="nav-link  nav-dropdown-toggle" href="#">
                        <i class="fa-fw fas fa-users nav-icon">

                        </i>
                        Product Management
                    </a>
                    <ul class="nav-dropdown-items">
                        @can('product_config_access')
                            <li class="nav-item">
                                <a href="{{ route("admin.product.index") }}" class="nav-link {{ request()->is('admin/product') || request()->is('admin/product/*') ? 'active' : '' }}">
                                    <i class="fa-fw fas fa-list nav-icon">

                                    </i>
                                    Create Product
                                </a>
                            </li>
                        @endcan
                    </ul>
                    <ul class="nav-dropdown-items">
                        @can('product_access')
                            <li class="nav-item">
                                <a href="{{ route("admin.product.purchase") }}" class="nav-link {{ request()->is('admin/product') || request()->is('admin/product/*') ? 'active' : '' }}">
                                    <i class="fa-fw fas fa-list nav-icon">

                                    </i>
                                    Product Purchased
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan
            @can('expense_management_access')
                <li class="nav-item nav-dropdown">
                    <a class="nav-link  nav-dropdown-toggle" href="#">
                        <i class="fa-fw fas fa-users nav-icon">

                        </i>
                        Expense
                    </a>
                    <ul class="nav-dropdown-items">
                        @can('expense_category_access')
                            <li class="nav-item">
                                <a href="{{ route("admin.expense-categories.index") }}" class="nav-link {{ request()->is('admin/expense-categories') || request()->is('admin/expense-categories/*') ? 'active' : '' }}">
                                    <i class="fa-fw fas fa-list nav-icon">

                                    </i>
                                    {{ trans('cruds.expenseCategory.title') }}
                                </a>
                            </li>
                        @endcan
                        @can('expense_access')
                            <li class="nav-item">
                                <a href="{{ route("admin.expenses.index") }}" class="nav-link {{ request()->is('admin/expenses') || request()->is('admin/expenses/*') ? 'active' : '' }}">
                                    <i class="fa-fw fas fa-arrow-circle-right nav-icon">

                                    </i>
                                    {{ trans('cruds.expense.title') }}
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan
            @can('income_management_access')
                <li class="nav-item nav-dropdown">
                    <a class="nav-link  nav-dropdown-toggle" href="#">
                        <i class="fa-fw fas fa-users nav-icon">

                        </i>
                        Income
                    </a>
                    <ul class="nav-dropdown-items">
                        @can('income_category_access')
                            <li class="nav-item">
                                <a href="{{ route("admin.income-categories.index") }}" class="nav-link {{ request()->is('admin/income-categories') || request()->is('admin/income-categories/*') ? 'active' : '' }}">
                                    <i class="fa-fw fas fa-list nav-icon">

                                    </i>
                                    {{ trans('cruds.incomeCategory.title') }}
                                </a>
                            </li>
                        @endcan
                        @can('income_access')
                            <li class="nav-item">
                                <a href="{{ route("admin.incomes.index") }}" class="nav-link {{ request()->is('admin/incomes') || request()->is('admin/incomes/*') ? 'active' : '' }}">
                                    <i class="fa-fw fas fa-arrow-circle-right nav-icon">

                                    </i>
                                    {{ trans('cruds.income.title') }}
                                </a>
                            </li>
                        @endcan
                    </ul>
                </li>
            @endcan
{{--            @can('expense_report_access')--}}
{{--                <li class="nav-item">--}}
{{--                    <a href="{{ route("admin.expense-reports.index") }}" class="nav-link {{ request()->is('admin/expense-reports') || request()->is('admin/expense-reports/*') ? 'active' : '' }}">--}}
{{--                        <i class="fa-fw fas fa-chart-line nav-icon">--}}

{{--                        </i>--}}
{{--                        {{ trans('cruds.expenseReport.title') }}--}}
{{--                    </a>--}}
{{--                </li>--}}
{{--            @endcan--}}
            @can('dyeing_management_access')
                <li class="nav-item nav-dropdown">
                    <a class="nav-link  nav-dropdown-toggle" href="#">
                        <i class="fa-fw fas fa-users nav-icon">

                        </i>
                        Configaration
                    </a>
                    <ul class="nav-dropdown-items">
                    @can('department_category_access')
                        <li class="nav-item">
                            <a href="{{ route("admin.department.index") }}" class="nav-link {{ request()->is('admin/department') || request()->is('admin/department/*') ? 'active' : '' }}">
                                <i class="fa-fw fas fa-list nav-icon">

                                </i>
                                Department
                            </a>
                        </li>
                    @endcan
                    @can('color_access')
                        <li class="nav-item">
                            <a href="{{ route("admin.color.index") }}" class="nav-link {{ request()->is('admin/color') || request()->is('admin/color/*') ? 'active' : '' }}">
                                <i class="fa-fw fas fa-list nav-icon">

                                </i>
                                Color
                            </a>
                        </li>
                    @endcan
                    @can('company_access')
                        <li class="nav-item">
                            <a href="{{ route("admin.company.index") }}" class="nav-link {{ request()->is('admin/company') || request()->is('admin/company/*') ? 'active' : '' }}">
                                <i class="fa-fw fas fa-list nav-icon">

                                </i>
                                Company
                            </a>
                        </li>
                    @endcan
                    </ul>
                </li>
            @endcan
            @can('dyeing_management_access')
                    <li class="nav-item nav-dropdown">
                        <a class="nav-link  nav-dropdown-toggle" href="#">
                            <i class="fa-fw fas fa-users nav-icon">

                            </i>
                            Report
                        </a>
                        <ul class="nav-dropdown-items">
                            @can('department_category_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.customer.order.report") }}" class="nav-link {{ request()->is('admin/report/customer/report') || request()->is('admin/report/customer/report/*') ? 'active' : '' }}">
                                        <i class="fa-fw fas fa-list nav-icon">

                                        </i>
                                        Customer Order
                                    </a>
                                </li>
                            @endcan
                                @can('department_category_access')
                                    <li class="nav-item">
                                        <a href="{{ route("admin.profit.order.report") }}" class="nav-link {{ request()->is('admin/report/customer/report') || request()->is('admin/report/customer/report/*') ? 'active' : '' }}">
                                            <i class="fa-fw fas fa-list nav-icon">

                                            </i>
                                            Profit Order
                                        </a>
                                    </li>
                                @endcan
                            @can('color_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.expense.report") }}" class="nav-link {{ request()->is('admin/report/expense-report') || request()->is('admin/report/expense-report/*') ? 'active' : '' }}">
                                        <i class="fa-fw fas fa-list nav-icon">

                                        </i>
                                        Expense Report
                                    </a>
                                </li>
                            @endcan
                            @can('company_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.knitting.in.report") }}" class="nav-link {{ request()->is('admin/report/knitting-report') || request()->is('admin/report/knitting-report/*') ? 'active' : '' }}">
                                        <i class="fa-fw fas fa-list nav-icon">

                                        </i>
                                        Knitting Report
                                    </a>
                                </li>
                            @endcan
                            @can('company_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.dyeing.report") }}" class="nav-link {{ request()->is('admin/report/dyeing-report') || request()->is('admin/report/dyeing-report/*') ? 'active' : '' }}">
                                        <i class="fa-fw fas fa-list nav-icon">

                                        </i>
                                        Dyeing Report
                                    </a>
                                </li>
                            @endcan
                            @can('company_access')
                                <li class="nav-item">
                                    <a href="{{ route("admin.supplier.product.report") }}" class="nav-link {{ request()->is('admin/report/supplier-product-report') || request()->is('admin/report/supplier-product-report/*') ? 'active' : '' }}">
                                        <i class="fa-fw fas fa-list nav-icon">

                                        </i>
                                        Supplier Invoice Report
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endcan


                <li class="nav-item">
                <a href="#" class="nav-link" onclick="event.preventDefault(); document.getElementById('logoutform').submit();">
                    <i class="nav-icon fas fa-fw fa-sign-out-alt">

                    </i>
                    {{ trans('global.logout') }}
                </a>
            </li>
        </ul>

    </nav>
    <button class="sidebar-minimizer brand-minimizer" type="button"></button>
</div>
