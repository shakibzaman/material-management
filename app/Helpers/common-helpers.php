<?php

use App\invoice;
use App\Payment;
use Illuminate\Support\Facades\Auth;

function makeInvoice($request){
    $invoice = new invoice();
    $invoice->inv_number = $request->inv_number;
    $invoice->type = $request->type;
    $invoice->supplier_id = $request->supplier_id;
    $invoice->date = now();
    $invoice->sub_total = $request->sub_total;
    $invoice->total = $request->total_price;
    $invoice->paid = $request->paid_amount ?? 0;
    $invoice->discount = $request->discount ?? 0;
    $invoice->due = $request->due_amount;
    $invoice->created_by = Auth::user()->id;
    $invoice_store = $invoice->save();
    return $invoice->id;
}

function payment($payment){
    $payment                  = new Payment();
    $payment->amount          = $payment->paid;
    $payment->payment_process = $payment->payment_process;
    $payment->payment_info    = $payment->payment_info;
    $payment->user_account_id = $payment->id;
    $payment->releted_department_id = $payment->department_id;
    $payment->releted_id = $payment->$order->id;
    $payment->releted_id_type = 1;
    $payment->created_by = Auth::user()->id;
    $payment->save();
}


