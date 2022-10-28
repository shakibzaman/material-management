@extends('layouts.admin')
@section('content')
@can('expense_create')
    <div style="margin-bottom: 10px;" class="row">
        <div class="col-lg-12">
            <a class="btn btn-success" href="{{ route("admin.showroom.cart",$department_id) }}">
                    Cart
                </a>
        </div>
    </div>
@endcan
<div class="card">
    <div class="card-header">
        Order List
    </div>
    <div class="card-body" id="card-table">
        <div class="table-responsive">
            <table class=" table table-bordered table-striped table-hover datatable datatable-Expense">
            <thead>
                    <tr>
                        <th width="10">

                        </th>
                        <th>
                            Date
                        </th>
                        <th>
                            Invoice ID
                        </th>
                        <th>
                            Customer
                        </th>
                        <th>
                            Total
                        </th>
                        <th>
                            Paid
                        </th>
                        <th>
                            Due
                        </th>
                        <th>
                            Discount
                        </th>
                        <th>
                            &nbsp;Action
                        </th>
                    </tr>
                </thead>
                <tbody id="table-body">
                @foreach($orders as $order)
                <tr>
                    <td>

                    </td>
                    <td>
                        {{$order->date}}
                    </td>
                    <td>
                        {{$order->invoice_id}}
                    </td>
                    <td>
                        {{$order->customer->name}}
                    </td>
                    <td>
                        {{$order->total}}
                    </td>
                    <td>
                        {{$order->paid}}
                    </td>
                    <td>
                        {{$order->due}}
                    </td>
                    <td>
                        {{$order->discount}}
                    </td>
                    <td>
                        <a class="btn btn-success text-light btn-xs" data-toggle="modal" id="mediumButton" data-target="#mediumModal"
                           data-attr="{{ route('admin.order.details',$order->id) }}" title="Return"> Details
                        </a>
                        @if($order->due >0)
                            <a class="btn btn-success btn-xs text-light" data-toggle="modal" id="mediumButton" data-target="#mediumModal"
                               data-attr="{{ route('admin.order.payment',$order->id) }}" title="Return"> Payment
                            </a>
                        @endif
                        @if($order->paid >0)
                            <a class="btn btn-success btn-xs text-light" data-toggle="modal" id="mediumButton" data-target="#mediumModal"
                               data-attr="{{ route('admin.order.payment.detail',$order->id) }}" title="Return"> Payment Details
                            </a>
                        @endif
                        <a class="btn btn-info btn-xs text-light" data-toggle="modal" id="mediumButton" data-target="#mediumModal"
                           data-attr="{{ route('admin.knitting.order.invoice',$order->id) }}" title="Return"> Invoice
                        </a>
                    </td>
                </tr>
                @endforeach
                </tbody>
            </table>
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
    jQuery(document).ready(function () {
        $('form#search-filter').on('submit', function (e) {
            e.preventDefault();
            searchStockSet();
        });

        function searchStockSet(){
            $.ajax({
                url: '/admin/expense/search',
                type: 'POST',
                cache: false,
                data: $('form#search-filter').serialize(),
                datatype: 'html',
                // datatype: 'application/json',

                beforeSend: function() {
                    // show waiting dialog
                    // waitingDialog.show('Loading...');
                },

                success:function(data){
                    console.log(data);
                    $("#card-table").html('');
                    $("#card-table").append(data);
                    // $('.academicLevel').append(op);
                    // if(data) {
                    //     if(data.status == 103){
                    //         Swal.fire({
                    //             icon: 'error',
                    //             title: 'Oops...',
                    //             text: data.message,
                    //             footer: 'Check your Stock'
                    //         })
                    //     }
                    //     else{
                    //         Swal.fire({
                    //             position: 'top-end',
                    //             icon: 'success',
                    //             title: 'Product Successfully Return',
                    //             showConfirmButton: false,
                    //             timer: 1500
                    //         })
                    //         $('#mediumModal').modal('hide');
                    //         window.location.reload();
                    //
                    //     }
                    // }
                },
                error:function(data){
                    console.log(data);
                    // sweet alert
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: "Unable to load data form server",
                        footer: 'Contact with Your Admin'
                    })
                    // swal("Error", 'Unable to load data form server', "error");
                }
            });
        }
    })
</script>
<script>
    $(function () {
  let dtButtons = $.extend(true, [], $.fn.dataTable.defaults.buttons)
@can('expense_delete')
  let deleteButtonTrans = '{{ trans('global.datatables.delete') }}'
  let deleteButton = {
    text: deleteButtonTrans,
    url: "{{ route('admin.expenses.massDestroy') }}",
    className: 'btn-danger',
    action: function (e, dt, node, config) {
      var ids = $.map(dt.rows({ selected: true }).nodes(), function (entry) {
          return $(entry).data('entry-id')
      });

      if (ids.length === 0) {
        alert('{{ trans('global.datatables.zero_selected') }}')

        return
      }

      if (confirm('{{ trans('global.areYouSure') }}')) {
        $.ajax({
          headers: {'x-csrf-token': _token},
          method: 'POST',
          url: config.url,
          data: { ids: ids, _method: 'DELETE' }})
          .done(function () { location.reload() })
      }
    }
  }
  dtButtons.push(deleteButton)
@endcan

  $.extend(true, $.fn.dataTable.defaults, {
    order: [[ 1, 'desc' ]],
    pageLength: 100,
  });
  $('.datatable-Expense:not(.ajaxTable)').DataTable({ buttons: dtButtons })
    $('a[data-toggle="tab"]').on('shown.bs.tab', function(e){
        $($.fn.dataTable.tables(true)).DataTable()
            .columns.adjust();
    });
})

</script>
@endsection
