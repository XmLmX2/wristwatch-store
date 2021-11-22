<?php

namespace App\Controller;

use App\Entity\Brand;
use App\Entity\Product;
use App\Form\ProductType;
use App\Service\ProductService;
use App\Service\ShoppingCartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProductController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/products/{brand}/{sortBy}", name="product_list")
     */
    public function products(
        ?Brand $brand = null,
        ?string $sortBy = null,
        TranslatorInterface $translator,
        ShoppingCartService $shoppingCartService
    ): Response
    {
        $params = [];

        if ($brand) {
            $params['brand'] = $brand;
        }

        switch ($sortBy) {
            case 'name':
                $orderParams = [
                    'name' => 'ASC'
                ];
                break;
            case 'price_0':
                $orderParams = [
                    'price' => 'ASC'
                ];
                break;
            case 'price_1':
                $orderParams = [
                    'price' => 'DESC'
                ];
                break;
            default:
                $orderParams = [
                    'id' => 'DESC'
                ];
        }

        $products = $this->em->getRepository(Product::class)->findBy($params, $orderParams);

        $savedShoppingCartProducts = $shoppingCartService->getSavedProducts();

        foreach ($products as $product) {
            $product->setShoppingCartQuantity($savedShoppingCartProducts[$product->getId()] ?? 1);
        }

        $brands = $this->em->getRepository(Brand::class)->findBy([], [
            'name' => 'ASC'
        ]);

        $orderByOptions = [
            'id' => $translator->trans('listing.preset'),
            'name' => $translator->trans('Name'),
            'price_0' => $translator->trans('listing.price_asc'),
            'price_1' => $translator->trans('listing.price_desc')
        ];

        return $this->render('site/product/list.html.twig', [
            'products' => $products,
            'brands' => $brands,
            'selectedBrand' => $brand,
            'selectedSoryBy' => $sortBy,
            'orderByOptions' => $orderByOptions,
        ]);
    }

    /**
     * @Route("/product/{product}", name="product", requirements={"product"="\d+"})
     */
    public function product(
        Product $product,
        ShoppingCartService $shoppingCartService
    ): Response
    {
        $savedShoppingCartProducts = $shoppingCartService->getSavedProducts();

        $product->setShoppingCartQuantity($savedShoppingCartProducts[$product->getId()] ?? 1);

        return $this->render('site/product/view.html.twig', [
            'product' => $product
        ]);
    }

    /**
     * @Route("/admin/product/list", name="admin_product_list")
     */
    public function adminProductList(): Response
    {
        $products = $this->em->getRepository(Product::class)->findAll();

        return $this->render('admin/product/list.html.twig', [
            'products' => $products
        ]);
    }

    /**
     * @Route("/admin/product/add", name="product_add")
     */
    public function addProduct(
        Request $request,
        ProductService $productService
    ): Response
    {
        $product = new Product();

        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $product = $productService->parseProductFormData($form);

            if ($product) {
                return $this->redirectToRoute('admin_product_list');
            }
        }

        return $this->render('admin/product/add.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
