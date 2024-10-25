<?php
	
	namespace App\Event;
	
	use App\Entity\SupportTicket;
	use Symfony\Contracts\EventDispatcher\Event;
	
	class TicketCreatedEvent extends Event
	{
		public const NAME = 'shop.support.ticket.created';
		
		public function __construct(private readonly SupportTicket $ticket)
		{
		}
		
		public function getTicket(): SupportTicket
		{
			return $this->ticket;
		}
	}
