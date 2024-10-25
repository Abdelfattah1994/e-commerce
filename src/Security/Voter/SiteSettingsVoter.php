<?php

namespace App\Security\Voter;

use App\Entity\SiteSettings;
use App\Entity\User;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Bundle\SecurityBundle\Security;

class SiteSettingsVoter extends Voter
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
            && $subject instanceof SiteSettings;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token): bool
    {
        $user = $token->getUser();
        if (!$user instanceof User) {
            return false;
        }

        /** @var SiteSettings $siteSettings */
        $siteSettings = $subject;

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($siteSettings, $user);
            case self::EDIT:
                return $this->canEdit($siteSettings, $user);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canView(SiteSettings $siteSettings, User $user): bool
    {
        // All authenticated users can view site settings
        return true;
    }

    private function canEdit(SiteSettings $siteSettings, User $user): bool
    {
        // Only admins can edit site settings
        return $this->security->isGranted('ROLE_ADMIN');
    }
}
