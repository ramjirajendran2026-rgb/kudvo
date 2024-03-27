<?php

namespace App\Http\Controllers;

use App\Enums\InvoiceStatus;
use App\Filament\User\Resources\ElectionResource\Pages\Dashboard;
use App\Models\Election;
use Illuminate\Http\Request;
use Illuminate\Support\Number;
use Laravel\Cashier\Cashier;
use Stripe\Exception\ApiErrorException;

class CheckoutController extends Controller
{
    /**
     * @throws ApiErrorException
     */
    public function success(Request $request)
    {
        $sessionId = $request->query(key: 'session_id');

        $session = Cashier::stripe()->checkout->sessions->retrieve(id: $sessionId, params: ['expand' => ['invoice']]);

        $related = filled($session->metadata) ? ($session->metadata['related_type'] ?? null) : null;

        switch ($related) {
            case 'election':
                $election = Election::find(id: $session->metadata['related_id'] ?? null);
                $invoice = $session->invoice;

                $election->touch(attribute: 'paid_at');
                if (filled($invoice)) {
                    $election->update(attributes: [
                        'invoice_status' => InvoiceStatus::tryFrom($invoice->status),
                        'stripe_invoice_id' => $invoice->id,
                        'stripe_invoice_data' => $invoice->toArray(),
                    ]);
                }

                if (Dashboard::canAccess(parameters: ['tenant' => $election->organisation, 'record' => $election])) {
                    return redirect(to: Dashboard::getUrl(parameters: ['tenant' => $election->organisation, 'record' => $election]));
                }
                return Number::currency($session->amount_total, $session->currency).' '.$session->payment_status;
            default:
                return Number::currency($session->amount_total, $session->currency).' '.$session->payment_status;
        }
    }
}
