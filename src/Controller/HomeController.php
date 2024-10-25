<?php
	
	namespace App\Controller;
	
	use App\Entity\Product;
    use App\Service\CategoryManager;
    use App\Service\ProductManager;
    use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
	use Symfony\Component\HttpFoundation\Response;
	use Symfony\Component\Routing\Attribute\Route;
	
	class HomeController extends AbstractController
	{
        public function __construct(private readonly ProductManager $productManager, private readonly CategoryManager $categoryManager)
        {
        }

		#[Route('', name: 'app_home')]
		public function index(): Response
		{
			return $this->render('shop/index.html.twig', [
                'featured_products' => $this->productManager->getFeatured(), // TODO : il faut injecter le bon service et appeler la methode pour recuperer les données
                'categories' => $this->categoryManager->actives() // TODO : il faut injecter le bon service et appeler la methode pour recuperer les données
			]);
		}
	}
