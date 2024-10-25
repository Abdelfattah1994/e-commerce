<?php

namespace App\Security\Voter;

use App\Entity\Product;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Bundle\SecurityBundle\Security;

class ProductVoter extends Voter
{
    const VIEW = 'view';
    const EDIT = 'edit';
    const DELETE = 'delete';

    private $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
    }

    protected function supports(string $attribute, mixed $subject): bool
    {
        return in_array($attribute, [self::VIEW, self::EDIT, self::DELETE])
            && $subject instanceof Product;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        /** @var Product $product */
        $product = $subject;

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($product, $user);
            case self::EDIT:
                return $this->canEdit($product, $user);
            case self::DELETE:
                return $this->canDelete($product, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canView(Product $product, User $user): bool
    {
        // Si le produit est actif, tout le monde peut le voir
        if ($product->isActive()) {
            return true;
        }

        // Sinon, seuls les admins peuvent voir les produits inactifs
        return $this->security->isGranted('ROLE_ADMIN');
    }

    private function canEdit(Product $product, User $user): bool
    {
        return $this->security->isGranted('ROLE_ADMIN');
    }

    private function canDelete(Product $product, User $user): bool
    {
        return $this->security->isGranted('ROLE_ADMIN');
    }
}
