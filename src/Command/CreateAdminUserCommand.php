<?php

namespace App\Command;

use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[AsCommand(
    name: 'app:create-admin-user',
    description: 'Creates a new admin user',
)]
class CreateAdminUserCommand extends Command
{
    private $userRepository;
    private $passwordHasher;

    public function __construct(UserRepository $userRepository, UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct();
        $this->userRepository = $userRepository;
        $this->passwordHasher = $passwordHasher;
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $email = $io->ask('Enter the admin email');
        $firstName = $io->ask('Enter the admin first name');
        $lastName = $io->ask('Enter the admin last name');
        $password = $io->askHidden('Enter the admin password (input will be hidden)');

        $user = new User();
        $user->setEmail($email);
        $user->setFirstName($firstName);
        $user->setLastName($lastName);
        $user->setRoles(['ROLE_ADMIN']);

        $hashedPassword = $this->passwordHasher->hashPassword($user, $password);
        $user->setPassword($hashedPassword);

        try {
            $this->userRepository->save($user, true);
            $io->success('Admin user created successfully.');
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $io->error('An error occurred while creating the admin user: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
