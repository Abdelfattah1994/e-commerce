<?php

namespace App\Controller\Admin;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Service\ProductManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/products')]
class ProductController extends AbstractController
{
    public function __construct(private readonly ProductManager $productManagery) {}

    #[Route('', name: 'app_admin_product_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $search = $request->query->get('q');
        return $this->render('admin/products/index.html.twig', [
            'products' => $this->productManagery->findAll($search),
        ]);
    }

    #[Route('/new', name: 'app_admin_product_new', methods: ['GET', 'POST'])]
    public function new(Request $request): Response
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->productManagery->create($product, true);
            $this->addFlash('success', 'Le produit a été créé avec succès.');

            return $this->redirectToRoute('app_admin_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/products/new.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_admin_product_show', methods: ['GET'])]
    public function show(Product $product): Response
    {
        return $this->render('admin/products/show.html.twig', [
            'product' => $product,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_admin_product_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Product $product): Response
    {
        $form = $this->createForm(ProductType::class, $product);
        // TODO: Ajouter la ligne qui permet d'analyser le formulaire
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // TODO: Appeler la bonne méthode pour mettre à jour le produit
            $this->productManagery->update($product);
            $this->addFlash('success', 'Le produit a été mis à jour avec succès.');

            return $this->redirectToRoute('app_admin_product_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('admin/products/edit.html.twig', [
            'product' => $product,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_admin_product_delete', methods: ['POST'])]
    public function delete(Request $request, Product $product): Response
    {
        if ($this->isCsrfTokenValid('delete'.$product->getId(), $request->request->get('_token'))) {
            $this->productManagery->delete($product, true);
            $this->addFlash('success', 'Le produit a été supprimé avec succès.');
        }

        return $this->redirectToRoute('app_admin_product_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/toggle-featured', name: 'app_admin_product_toggle_featured', methods: ['POST'])]
    public function toggleFeatured(Request $request, Product $product): Response
    {
        $featured = $request->request->get('featured') === 'on';
        $product->setFeatured($featured);
        $this->productManagery->update($product);

        $this->addFlash('success', $featured ? 'Le produit a été mis en avant.' : 'Le produit n\'est plus mis en avant.');

        return $this->redirectToRoute('app_admin_product_index');
    }
}
