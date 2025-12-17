<?php

namespace App\Listeners;

use App\Models\User;
use Laravel\Cashier\Events\WebhookReceived;

class StripeEventListener
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(WebhookReceived $event): void
    {
        if ($event->payload['type'] === 'customer.subscription.created' || $event->payload['type'] === 'customer.subscription.updated') {
            $stripeId = $event->payload['data']['object']['customer'];
            // $product = $event->payload['data']['object']['plan']['product'];

            // $user = User::where('stripe_id', $stripeId)->firstOrFail();

            // Write your logic here
        }
    }
}
