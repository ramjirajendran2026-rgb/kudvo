<?php

namespace App\Http\Controllers;

use App\Enums\InvoiceStatus;
use App\Enums\MeetingOnboardingStep;
use App\Filament\User\Resources\ElectionResource\Pages\Dashboard;
use App\Filament\User\Resources\MeetingResource\Pages\MeetingDashboard;
use App\Models\Election;
use App\Models\Meeting;
use Filament\Notifications\Notification;
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
        sleep(seconds: 3);

        $sessionId = $request->query(key: 'session_id');

        $session = Cashier::stripe()->checkout->sessions->retrieve(id: $sessionId, params: ['expand' => ['invoice.charge']]);

        $related = filled($session->metadata) ? ($session->metadata['related_type'] ?? null) : null;

        switch ($related) {
            case 'election':
                $meeting = Election::find(id: $session->metadata['related_id'] ?? null);
                $invoice = $session->invoice;

                $meeting->touch(attribute: 'paid_at');
                if (filled($invoice)) {
                    $meeting->update(attributes: [
                        'invoice_status' => InvoiceStatus::tryFrom($invoice->status),
                        'stripe_invoice_id' => $invoice->id,
                        'stripe_invoice_data' => $invoice->toArray(),
                    ]);
                }

                if (Dashboard::canAccess(parameters: ['tenant' => $meeting->organisation, 'record' => $meeting])) {
                    Notification::make()
                        ->title(title: 'Payment Successful')
                        ->body(body: 'The payment for the election has been successful.')
                        ->success()
                        ->send();

                    return redirect(to: Dashboard::getUrl(parameters: ['tenant' => $meeting->organisation, 'record' => $meeting]));
                }

                return Number::currency($session->amount_total, $session->currency) . ' ' . $session->payment_status;
            case 'meeting':
                $meeting = Meeting::find(id: $session->metadata['related_id'] ?? null);
                $invoice = $session->invoice;

                $payment = $meeting->payments()->create(attributes: [
                    'amount' => $session->amount_total,
                    'currency' => $session->currency,
                    'paid_at' => now(),
                    'stripe_invoice_id' => $invoice?->id,
                    'stripe_invoice_data' => $invoice?->toArray(),
                ]);

                if (MeetingDashboard::canAccess(['tenant' => $meeting->organisation, 'record' => $meeting])) {
                    Notification::make()
                        ->title(title: 'Payment Successful')
                        ->body(body: 'The payment for the meeting has been successful.')
                        ->success()
                        ->send();

                    return redirect(to: MeetingDashboard::getUrl(['tenant' => $meeting->organisation, 'record' => $meeting]));
                }

                return Number::currency($session->amount_total, $session->currency) . ' ' . $session->payment_status;
            default:
                return Number::currency($session->amount_total, $session->currency) . ' ' . $session->payment_status;
        }
    }

    /**
     * @throws ApiErrorException
     */
    public function cancel(Request $request)
    {
        $sessionId = $request->query(key: 'session_id');

        $session = Cashier::stripe()->checkout->sessions->retrieve(id: $sessionId);

        $related = filled($session->metadata) ? ($session->metadata['related_type'] ?? null) : null;

        switch ($related) {
            case 'election':
                $meeting = Election::find(id: $session->metadata['related_id'] ?? null);

                if (Dashboard::canAccess(parameters: ['tenant' => $meeting->organisation, 'record' => $meeting])) {
                    Notification::make()
                        ->title(title: 'Payment Cancelled')
                        ->body(body: 'The payment for the election has been cancelled.')
                        ->danger()
                        ->send();

                    return redirect(to: Dashboard::getUrl(parameters: ['tenant' => $meeting->organisation, 'record' => $meeting]));
                }

                return Number::currency($session->amount_total, $session->currency) . ' ' . $session->payment_status;
            case 'meeting':
                $meeting = Meeting::find(id: $session->metadata['related_id'] ?? null);

                if (MeetingDashboard::canAccess(['tenant' => $meeting->organisation, 'record' => $meeting])) {
                    Notification::make()
                        ->title(title: 'Payment Cancelled')
                        ->body(body: 'The payment for the meeting has been cancelled.')
                        ->danger()
                        ->send();

                    return redirect(to: MeetingOnboardingStep::Publish->getUrl(parameters: ['tenant' => $meeting->organisation, 'record' => $meeting]));
                }

                return Number::currency($session->amount_total, $session->currency) . ' ' . $session->payment_status;
            default:
                return Number::currency($session->amount_total, $session->currency) . ' ' . $session->payment_status;
        }
    }
}
