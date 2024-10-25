<?php

namespace App\Controller\Admin;

use App\Entity\User;
use App\Form\UserType;
use App\Service\UserManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/admin/users')]
class UserController extends AbstractController
{
    public function __construct(
        private readonly UserManager                 $userManager,
        private readonly UserPasswordHasherInterface $passwordHasher
    ) {}

    #[Route('', name: 'app_admin_user_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        return $this->render('admin/users/index.html.twig', [
            'users' => $this->userManager->findAll(
            //TODO: Passez le bon argument pour faire la recherche
                $request->query->get('q')
            ),
        ]);
    }

    #[Route('/new', name: 'app_admin_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword($this->passwordHasher->hashPassword($user, $form->get('plainPassword')->getData()));
            $this->userManager->create($user);

            $this->addFlash('success', 'L\'utilisateur a été créé avec succès.');
            return $this->redirectToRoute('app_admin_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/users/new.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_admin_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('admin/users/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($plainPassword = $form->get('plainPassword')->getData()) {
                $user->setPassword($this->passwordHasher->hashPassword($user, $plainPassword));
            }
            $this->userManager->update($user);

            $this->addFlash('success', 'L\'utilisateur a été mis à jour avec succès.');
            return $this->redirectToRoute('app_admin_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/users/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_admin_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->request->get('_token'))) {
            $this->userManager->delete($user);
            $this->addFlash('success', 'L\'utilisateur a été supprimé avec succès.');
        }

        return $this->redirectToRoute('app_admin_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
