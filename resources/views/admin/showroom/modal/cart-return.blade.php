@extends('layouts.admin')
@section('content')
<div class="card container-fluid">
    <div class="card-title bg-primary text-center">
        <div class="container">
            <h2>Return Order - {{$orders->invoice_id}}</h2>
        </div>
    </div>
    <form id="showroom-pos">
        <input type="hidden" name="order_id" id="order_id" value="{{$orders->id}}">
        <input type="hidden" name="department_id" id="department_id" value="{{$orders->department_id}}">

        @csrf
    <div class="row">
        <div class="col-md-12">
            <div class="cart-body" style="background: #ffe8e8">
                <table class="table table-bordered" id="myTable">
                    <thead>
                    <tr>
                        <th>Name</th>
                        <th>Order Quantity</th>
                        <th>Unit</th>
                        <th class="bg-danger">Return Quantity</th>
                    </tr>
                    </thead>
                    <tbody>
                        @foreach($orders->details as $detail)
                        <tr>
                            <td>
                                <input type="text" class="form-control" value="{{$detail->color->name}}" name="material_name[]">
                                <input type="hidden" class="form-control order_detail_id" value="{{$detail->id}}" name="order_detail_id[]">
                            </td>
                            <td>
                                <input type="text" class="form-control" value="{{$detail->unit == 2 ? ($detail->qty * 2.20462262) : $detail->qty}}" readonly>
                            </td>
                            <td>{{$detail->unit == 1 ? 'KG' : 'Pound'}}</td>
                            <td class="bg-danger">
                                <input type="text" class="form-control quantity" value="" name="quantity[]">
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
        <button type="submit" class="btn btn-primary submit-btn">Return</button>
    </form>
</div>
@endsection
@parent
@section('scripts')
<script>
    $(document).ready(function(){

        $("#showroom-pos").on('submit',function (e){
            e.preventDefault();

            showroomOrder();
        })
        function showroomOrder(){
            $.ajax({
                url:'/admin/showroom/return/order',
                type:'POST',
                data:$("form#showroom-pos").serialize(),

                success(data){
                    console.log(data);
                    if(data.status == 200){
                        Swal.fire({
                            position: 'top-end',
                            icon: 'success',
                            title: 'Order Done successfully',
                            timer: 1500
                        })
                        window.location.href = "/admin/showroom/orders/"+data.department_id;
                        // window.location.reload();
                    }
                    if(data.status == 103){
                        Swal.fire({
                            icon: 'error',
                            title: data.message,
                            footer: 'Please Recheck your stock',
                        })
                    }
                },
                error:function(data){
                    if(data.status == 422 ){
                        Swal.fire({
                            icon: 'error',
                            title: "Invoice number already used",
                            text: "Unable to load data form server",
                            footer: 'Please Recheck your details'
                        })
                    }
                }
            })
        }

    })
</script>
@endsection
