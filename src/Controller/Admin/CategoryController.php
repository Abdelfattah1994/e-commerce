<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use App\Service\CategoryManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/categories')]
class CategoryController extends AbstractController
{
    public function __construct(private CategoryManager $categoryManager) {}

    #[Route('', name: 'app_admin_category_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $search = $request->query->get('q', '');
        return $this->render('admin/categories/index.html.twig', [
            'categories' => $this->categoryManager->findAll($search),
        ]);
    }

    #[Route('/new', name: 'app_admin_category_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->categoryManager->create($category);
            $this->addFlash('success', 'La catégorie a été créée avec succès.');

            return $this->redirectToRoute('app_admin_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/categories/new.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_admin_category_show', methods: ['GET'])]
    public function show(Category $category): Response
    {
        return $this->render('admin/categories/show.html.twig', [
            'category' => $category,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_category_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Category $category): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // TODO: il faut appeler la bonne methode ici pour mettre à jour la catégorie
            $this->categoryManager->update($category);
            $this->addFlash('success', 'La catégorie a été mise à jour avec succès.');

            return $this->redirectToRoute('app_admin_category_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/categories/edit.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_admin_category_delete', methods: ['POST'])]
    public function delete(Request $request, Category $category): Response
    {
        if ($this->isCsrfTokenValid('delete'.$category->getId(), $request->request->get('_token'))) {
            $this->categoryManager->delete($category);
            $this->addFlash('success', 'La catégorie a été supprimée avec succès.');
        }

        return $this->redirectToRoute('app_admin_category_index', [], Response::HTTP_SEE_OTHER);
    }
}