@extends('layouts.admin')
@section('content')
    <div class="card">
        <div class="card-header">
            <b>Dyeing Product Return List</b>
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
                            Name
                        </th>
                        <th>
                            Product Quantity
                        </th>
                        <th>
                            Reason
                        </th>
                        <th>
                            Return By
                        </th>
                        <th>
                            Date
                        </th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($return_list as $key =>$list)
                        <tr>
                            <td>

                            </td>
                            <td>
                                {{$list->id}}
                            </td>
                            <td>
                                {{$list->name}}

                            </td>
                            <td>
                                {{$list->quantity}}

                            </td>
                            <td>
                                {{$list->reason}}

                            </td>
                            <td>
                                {{$list->return_by_user}}

                            </td>
                            <td>
                                {{$list->created_at}}

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
        $(document).on('click', '#mediumButton', function (event) {
            event.preventDefault();
            let href = $(this).attr('data-attr');
            $.ajax({
                url: href,
                beforeSend: function () {
                    $('#loader').show();
                },
                // return the result
                success: function (result) {
                    $('#mediumModal').modal("show");
                    $('#mediumBody').html(result).show();
                },
                complete: function () {
                    $('#loader').hide();
                },
                error: function (jqXHR, testStatus, error) {
                    console.log(error);
                    alert("Page " + href + " cannot open. Error:" + error);
                    $('#loader').hide();
                },
                timeout: 8000
            })
        });
    </script>
@endsection
