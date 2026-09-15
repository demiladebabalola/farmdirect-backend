<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function initialize(Request $request, Order $order)
    {
        if ($order->customer_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $reference = 'FD-PAY-'.strtoupper(Str::random(10));

        $payment = Payment::create([
            'order_id' => $order->id,
            'paystack_reference' => $reference,
            'amount' => $order->total,
            'status' => 'pending',
        ]);

        return response()->json([
            'reference' => $payment->paystack_reference,
            'amount' => $payment->amount,
            'email' => $request->user()->email,
            'public_key' => config('services.paystack.public_key'),
        ]);
    }

    public function verify(Request $request, string $reference)
    {
        $payment = Payment::where('paystack_reference', $reference)->firstOrFail();
        $order = $payment->order;

        if ($order->customer_id !== $request->user()->id) {
            return response()->json(['message' => 'Forbidden'], 403);
        }

        $response = Http::withToken(config('services.paystack.secret_key'))
            ->get("https://api.paystack.co/transaction/verify/{$reference}");

        if (! $response->successful()) {
            return response()->json(['message' => 'Could not reach Paystack to verify payment.'], 502);
        }

        $data = $response->json('data');

        $paidCorrectAmount = $data['amount'] === $payment->amount * 100;
        $isSuccess = ($data['status'] ?? null) === 'success' && $paidCorrectAmount;

        $payment->update(['status' => $isSuccess ? 'success' : 'failed']);

        return response()->json([
            'payment_status' => $payment->status,
            'order' => $order->fresh('product')->toFrontendArray(),
        ]);
    }
}