<?php

namespace App\Security\Voter;

use App\Entity\SupportTicket;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Bundle\SecurityBundle\Security;

class SupportTicketVoter extends Voter
{
    const VIEW = 'view';
    const EDIT = 'edit';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::EDIT])
            && $subject instanceof SupportTicket;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        /** @var SupportTicket $ticket */
        $ticket = $subject;

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($ticket, $user);
            case self::EDIT:
                return $this->canEdit($ticket, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canView(SupportTicket $ticket, User $user): bool
    {
        // L'utilisateur peut voir son propre ticket
        if ($ticket->getUser() === $user) {
            return true;
        }

        // Les admins peuvent voir tous les tickets
        return $this->security->isGranted('ROLE_ADMIN');
    }

    private function canEdit(SupportTicket $ticket, User $user): bool
    {
        // Seuls les admins peuvent modifier les tickets
        return $this->security->isGranted('ROLE_ADMIN');
    }
}
