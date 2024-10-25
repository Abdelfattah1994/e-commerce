<?php

namespace App\Security\Voter;

use App\Entity\Order;
use App\Entity\User;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class OrderVoter extends Voter
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
            && $subject instanceof Order;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        /** @var Order $order */
        $order = $subject;

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($order, $user);
            case self::EDIT:
                return $this->canEdit($order, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canView(Order $order, User $user): bool
    {
        // Si l'utilisateur est l'auteur de la commande, il peut la voir
        if ($order->getUser() === $user) {
            return true;
        }

        // Les admins peuvent voir toutes les commandes
        return $this->security->isGranted('ROLE_ADMIN');
    }

    private function canEdit(Order $order, User $user): bool
    {
        // Seuls les admins peuvent modifier les commandes
        return $this->security->isGranted('ROLE_ADMIN');
    }
}
