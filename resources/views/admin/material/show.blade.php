@extends('layouts.admin')
@section('content')

<div class="card">
    <div class="card-header">
        {{ trans('global.show') }} Materials
    </div>

    <div class="card-body">
        <div class="mb-2">
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th> ID </th>
                        <th>Material Name</th>
                        <th>Material Quantity</th>
                        <th>Unit</th>
                        <th>Buying Date</th>
                        <th>Unit Price</th>
                        <th>Total price</th>
                        <th>Rest Quantity</th>
                        <th>Supplied By</th>
                        <th>Invoice Number</th>
                        <th>Purchased By</th>
                        <th>Entry By</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($materials as $material)
                    <tr>

                        <td>
                            {{ $material->id }}
                        </td>

                        <td>
                            {{ $material->material->name ?? '' }}
                        </td>
                        <td>
                            {{ $material->quantity ?? '' }}
                        </td>
                        <td>
                            {{$material->units->name ?? 'N/A'}}
                        </td>
                        <td>
                            Tk. {{ $material->buying_date }}
                        </td>
                        <td>
                            {{ $material->unit_price }}
                        </td>
                        <td>
                            {{ $material->total_price }}
                        </td>
                        <td>
                            {{ $material->rest }}
                        </td>

                        <td>
                            {{ $material->supplier->name }}
                        </td>

                        <td>
                            {{ $material->inv_number }}
                        </td>

                        <td>
                            {{$material->employee->name}}
                        </td>

                        <td>
                           {{$material->user->name}}
                        </td>
                        <td>
                            @if($material->rest>0)
                            <a class="btn btn-danger text-light btn-xs" data-toggle="modal" id="mediumButton" data-target="#mediumModal"
                                                data-attr="{{ route('admin.product.company.return', $material->id) }}" title="Return"> Return
                            </a>
                            @endif
                            <a class="btn btn-info text-light btn-xs" data-toggle="modal" id="mediumButton" data-target="#mediumModal"
                               data-attr="{{ route('admin.product.company.detail', $material->id) }}" title="Details"> Detail
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
            <a style="margin-top:20px;" class="btn btn-default" href="{{ url()->previous() }}">
                {{ trans('global.back_to_list') }}
            </a>
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
