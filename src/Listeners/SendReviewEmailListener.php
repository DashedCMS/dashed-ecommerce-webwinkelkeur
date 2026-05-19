<?php

namespace Dashed\DashedEcommerceWebwinkelkeur\Listeners;

use Illuminate\Bus\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Dashed\DashedEcommerceWebwinkelkeur\Classes\Webwinkelkeur;
use Dashed\DashedEcommerceCore\Events\Orders\OrderMarkedAsPaidEvent;

class SendReviewEmailListener implements ShouldQueue
{
    use InteractsWithQueue;
    use Queueable;

    public function handle(OrderMarkedAsPaidEvent $event): void
    {
        Webwinkelkeur::sendReviewEmail($event->order);
    }
}
