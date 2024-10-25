<?php

namespace App\Security\Voter;

use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Bundle\SecurityBundle\Security;

class UserVoter extends Voter
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
            && $subject instanceof User;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        /** @var User $subjectUser */
        $subjectUser = $subject;

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($subjectUser, $user);
            case self::EDIT:
                return $this->canEdit($subjectUser, $user);
            case self::DELETE:
                return $this->canDelete($subjectUser, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canView(User $subjectUser, User $user): bool
    {
        // Users can view their own profile
        if ($user === $subjectUser) {
            return true;
        }

        // Admins can view any user
        return $this->security->isGranted('ROLE_ADMIN');
    }

    private function canEdit(User $subjectUser, User $user): bool
    {
        // Users can edit their own profile
        if ($user === $subjectUser) {
            return true;
        }

        // Admins can edit any user
        return $this->security->isGranted('ROLE_ADMIN');
    }

    private function canDelete(User $subjectUser, User $user): bool
    {
        // Only admins can delete users
        return $this->security->isGranted('ROLE_ADMIN');
    }
}
