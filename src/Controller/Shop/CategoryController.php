<?php

namespace App\Controller\Shop;

use App\Service\CategoryManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CategoryController extends AbstractController
{
    #[Route('/categories', name: 'app_shop_categories')]
    public function index(CategoryManager $categoryManager): Response
    {
        return $this->render('shop/category/index.html.twig', [
            'categories' => $categoryManager->findAll(),
        ]);
    }
	
	public function menuCategories(CategoryManager $categoryManager): Response
	{
		$categories = $categoryManager->findBy([], ['name' => 'ASC']);
		
		return $this->render('_partials/_menu_categories.html.twig', [
			'categories' => $categories,
		]);
	}
}
