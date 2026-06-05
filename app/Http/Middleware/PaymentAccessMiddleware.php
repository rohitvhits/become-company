<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Model\InvoicePayment;
use Illuminate\Support\Facades\Auth;

class PaymentAccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        $payment = $request->route('payment');

        // If payment is not found, let the controller handle it
        if (!$payment instanceof InvoicePayment) {
            $payment = InvoicePayment::with('invoice')->find($payment);
            if (!$payment) {
                abort(404, 'Payment not found.');
            }
        }

        // Check if the authenticated user has access to this payment
        $user = Auth::user();

        // Check if user is authenticated
        if (!$user) {
            return redirect()->route('login');
        }

        // Super Admin users (user_type_fk = 184) can access all payments
        if ($user->user_type_fk == 184 || $user->role_access == 1) {
            return $next($request);
        }

        // Agency users can only access payments for their own invoices
        if ($user->agency_fk && $payment->agency_id === $user->agency_fk) {
            return $next($request);
        }

        // If user doesn't have permission, deny access
        abort(403, 'You do not have permission to access this payment.');
    }
}