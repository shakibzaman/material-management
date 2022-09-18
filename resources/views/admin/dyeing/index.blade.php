@extends('layouts.admin')
@section('content')
@can('expense_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <!-- <a class="btn btn-success" href="{{ route("admin.neeting.stock.in") }}">
                Stock In
            </a> -->
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        Dyeing Product List
    </div>

    <div class="card-body">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-Expense">
                <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            ID
                        </th>
                        <th>
                            Company Name
                        </th>
                        <th>
                            Product Quantity
                        </th>
                        <!-- <th>
                            material Quantity
                        </th> -->
                        <th>
                            &nbsp;Action
                        </th>
                    </tr>
                </thead>
                <tbody>
                @foreach($nettingsData as $key =>$nettingData)
                    @php
                        if(isset($transfer_products[$key])) {
                                $productTotalSum = $transfer_products[$key]->sum('rest_quantity');
                            }
                        if(isset($transfer_materials[$key])) {
                                $materialTotalSum = $transfer_materials[$key]->sum('quantity');
                            }

                    @endphp
                <tr>
                    <td>
                    </td>
                    <td>
                        {{$companyList[$key]->id}}
                    </td>
                    <td>
                        {{$companyList[$key]->name}}
                    </td>
                    <td>
                        {{$productTotalSum}}
                    </td>
                    <!-- <td>
                        {{$materialTotalSum}}
                    </td> -->
                    <td>
                        <a class="btn btn-xs btn-primary" href="{{ route('admin.dyeing.company.transfer', $companyList[$key]->id) }}">
                            {{ trans('global.view') }}
                        </a>
                        @if($companyList[$key]->id == 1)
                            <a class="btn btn-xs btn-success" href="{{ route('admin.dyeing.process.company.product', $companyList[$key]->id) }}">
                                Transfer to Showroom
                            </a>
                        @endif

                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
        </div>


    </div>
</div>
<div class="modal fade" id="mediumModal" tabindex="-1" role="dialog" aria-labelledby="mediumModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body" id="mediumBody">
                <div>
                    <!-- the result to be displayed apply here -->
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
@section('scripts')
@parent
<script>
    $(document).on('click', '#mediumButton', function(event) {
        event.preventDefault();
        let href = $(this).attr('data-attr');
        $.ajax({
            url: href,
            beforeSend: function() {
                $('#loader').show();
            },
            // return the result
            success: function(result) {
                $('#mediumModal').modal("show");
                $('#mediumBody').html(result).show();
            },
            complete: function() {
                $('#loader').hide();
            },
            error: function(jqXHR, testStatus, error) {
                console.log(error);
                alert("Page " + href + " cannot open. Error:" + error);
                $('#loader').hide();
            },
            timeout: 8000
        })
    });
</script>
@endsection
