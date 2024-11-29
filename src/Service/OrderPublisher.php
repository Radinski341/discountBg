<?php

namespace App\Service;

use Symfony\Component\Mercure\HubInterface;
use Symfony\Component\Mercure\Update;

class OrderPublisher
{
    private HubInterface $hub;

    public function __construct(HubInterface $hub)
    {
        $this->hub = $hub;
    }

    public function publishNewPendingOrder(int $orderId): void
    {
        // Create an update for the "pending orders" topic
        $update = new Update(
            'orders/pending', // Topic to subscribe to
            json_encode(['id' => $orderId, 'status' => 'PENDING'])
        );
        $this->hub->publish($update);
    }
}