<?php

namespace App\EventListener;

use App\Event\OrderCreatedEvent;
use App\Event\OrderStatusChangedEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Psr\Log\LoggerInterface;

class OrderEventListener
{
    public function __construct(private LoggerInterface $logger)
    {}

    #[AsEventListener(event: OrderCreatedEvent::class)]
    public function onOrderCreated(OrderCreatedEvent $event): void
    {
        $order = $event->getOrder();
        $this->logger->info('New order created', ['id' => $order->getId()]);
        // Vous pouvez ajouter ici d'autres logiques, comme l'envoi d'un email de confirmation
    }

    #[AsEventListener(event: OrderStatusChangedEvent::class)]
    public function onOrderStatusChanged(OrderStatusChangedEvent $event): void
    {
        $order = $event->getOrder();
        $this->logger->info('Order status changed', [
            'id' => $order->getId(),
            'status' => $order->getStatus()
        ]);
        // Vous pouvez ajouter ici d'autres logiques, comme l'envoi d'un email de mise à jour de statut
    }
}
