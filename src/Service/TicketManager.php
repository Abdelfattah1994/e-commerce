<?php

namespace App\Service;

use App\Entity\SupportTicket;
use App\Entity\User;
use App\Enums\DatabaseTransaction;
use App\Event\TicketCreatedEvent;
use App\Repository\SupportTicketRepository;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

readonly class TicketManager
{
    public function __construct(
        private SupportTicketRepository $supportTicketRepository,
        private EventDispatcherInterface $eventDispatcher
    ) {
    }
	
    public function createTicket(User $user, string $subject, string $message): SupportTicket
    {
        $ticket = new SupportTicket();
        $ticket->setUser($user);
        $ticket->setSubject($subject);
        $ticket->setMessage($message);
        $ticket->setStatus('OPEN');

        $this->create($ticket);
        $this->eventDispatcher->dispatch(new TicketCreatedEvent($ticket), 'shop.support.ticket.created');

        return $ticket;
    }

    public function updateTicketStatus(SupportTicket $ticket, string $status): void
    {
        $ticket->setStatus($status);
		$this->update($ticket);
    }
	
	public function create(SupportTicket $ticket): void
	{
		$this->supportTicketRepository->save($ticket, DatabaseTransaction::COMMIT);
	}
	public function update(SupportTicket $ticket): void
	{
		$this->supportTicketRepository->save($ticket, DatabaseTransaction::COMMIT);
	}

    public function addResponse(SupportTicket $ticket, string $response, User $respondent): void
    {
    
    }

    public function findAll(?string $search = null, ?int $limit = 50): array
    {
        return $this->supportTicketRepository->search($search, $limit);
    }

    public function getUserTickets(User $user): array
    {
        return $this->supportTicketRepository->findByUser($user);
    }
	
	public function delete(SupportTicket $supportTicket): void
	{
		$this->supportTicketRepository->remove($supportTicket);
	}
	
	public function count(array $criteria = []): int
	{
		return $this->supportTicketRepository->count($criteria);
	}
}
