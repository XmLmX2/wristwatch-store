<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Product;
use App\Form\OrderType;
use App\Service\OrderService;
use App\Service\ShoppingCartService;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ShoppingCartController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/shopping_cart", name="shopping_cart")
     */
    public function index(
        ShoppingCartService $shoppingCartService,
        Request $request,
        OrderService $orderService
    ): Response
    {
        $order = $orderService->initNewOrder();
        $orderForm = $this->createForm(OrderType::class, $order);

        $orderForm->handleRequest($request);

        if ($orderForm->isSubmitted() && $orderForm->isValid()) {
            $order = $orderService->handleOrderForm($orderForm, $request);

            if ($order) {
                $paymentStatus = $orderService->handlePaymentMethod($order, $orderForm);

                if (!empty($paymentStatus['status'])) {
                    if (!empty($paymentStatus['redirect'])) {
                        return $this->redirect($paymentStatus['redirect']);
                    }
                }
            }

            return $this->redirectToRoute('shopping_cart_feedback');
        }

        return $this->render('site/shopping_cart/index.html.twig', [
            'products' => $shoppingCartService->getValidProducts(),
            'total' => $shoppingCartService->getTotal(),
            'orderForm' => $orderForm->createView()
        ]);
    }

    /**
     * @Route("/shopping_cart/feedback", name="shopping_cart_feedback")
     */
    public function shoppingCartFeedback(OrderService $orderService): Response
    {
        $latestOrderId = $orderService->getLatestOrderId();

        $order = null;

        if ($latestOrderId) {
            $order = $this->em->getRepository(Order::class)->find($latestOrderId);
        }

        return $this->render('site/shopping_cart/feedback.html.twig', [
            'order' => $order
        ]);
    }

    /**
     * @Route("/shopping_cart/save_product", name="shopping_cart_add_product", methods={"POST"})
     */
    public function shoppingCartSaveProduct(
        Request $request,
        ShoppingCartService $shoppingCartService
    ): RedirectResponse
    {
        $productId = (int)$request->request->get('product_id');
        $quantity = (int)$request->request->get('quantity');

        if (!$productId) {
            throw new Exception('Invalid product ID!');
        }

        $shoppingCartService->saveProduct($productId, $quantity);

        return $this->redirectToRoute('shopping_cart');
    }

    /**
     * @Route("/ajax/shopping_cart/save_quantity/{product}/{quantity}", name="save_product_quantity")
     */
    public function saveProductQuantity(
        Product $product,
        int $quantity,
        ShoppingCartService $shoppingCartService
    ): JsonResponse
    {
        $feedback = [];

        $feedback['data'] = $shoppingCartService->saveProduct($product->getId(), $quantity);

        $feedback['content'] = $this->renderView('site/shopping_cart/snippets/shopping_cart.html.twig', [
            'products' => $shoppingCartService->getValidProducts(),
            'total' => $shoppingCartService->getTotal()
        ]);

        return new JsonResponse($feedback);
    }
}
