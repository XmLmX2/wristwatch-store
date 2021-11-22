<?php
/**
 * User: Marius Mertoiu
 * Date: 21/11/2021 19:55
 * Email: marius.mertoiu@gmail.com
 */

namespace App\Service;

use App\Entity\Currency;
use App\Entity\Order;
use App\Entity\OrderPaymentType;
use App\Entity\OrderProduct;
use App\Entity\Product;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\HttpClient\Exception\DecodingExceptionInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class OrderService
{
    /**
     * Credit card payment type ID
     */
    const CREDIT_CARD_PAYMENT_TYPE_ID = 1;

    /**
     * Keyword used to save latest order ID
     */
    const LATEST_ORDER_KEYWORD = 'latest_order';

    private EntityManagerInterface $em;
    private HttpClientInterface $client;
    private UrlGeneratorInterface $router;
    private ShoppingCartService $shoppingCartService;
    private SessionInterface $session;
    private PaymentService $paymentService;

    public function __construct(
        EntityManagerInterface $em,
        HttpClientInterface $client,
        UrlGeneratorInterface $router,
        ShoppingCartService $shoppingCartService,
        RequestStack $requestStack,
        PaymentService $paymentService
    ) {
        $this->em = $em;
        $this->client = $client;
        $this->router = $router;
        $this->shoppingCartService = $shoppingCartService;
        $this->session = $requestStack->getSession();
        $this->paymentService = $paymentService;
    }

    /**
     * Init a new order for shopping cart
     *
     * @return Order
     */
    public function initNewOrder(): Order
    {
        $order = new Order();

        // Get payment type
        $paymentType = $this->em->getRepository(OrderPaymentType::class)->find(
            OrderService::CREDIT_CARD_PAYMENT_TYPE_ID
        );
        $order->setPaymentType($paymentType);
        $order->setToken(md5(uniqid(rand(), true)));

        return $order;
    }

    /**
     * Handle order form
     *
     * @param FormInterface $form
     * @param Request $request
     * @return Order
     */
    public function handleOrderForm(
        FormInterface $form,
        Request $request
    ): Order
    {
        /** @var Order $order */
        $order = $form->getData();

        $this->em->persist($order);

        $products = $request->request->get('products');

        if (!empty($products)) {
            $currency = null;
            $total = 0;

            foreach ($products as $productId => $row) {
                $product = $this->em->getRepository(Product::class)->find($productId);

                if ($product) {
                    $price = (float)$row['price'];
                    $quantity = (int)$row['quantity'];

                    $orderProduct = new OrderProduct();
                    $orderProduct->setProduct($product);
                    $orderProduct->setStoreOrder($order);
                    $orderProduct->setPrice($price);
                    $orderProduct->setQuantity($quantity);

                    $total += $price * $quantity;

                    if (!$currency && !empty($row['currency'])) {
                        $currency = $this->em->getRepository(Currency::class)->find($row['currency']);
                    }

                    $this->em->persist($orderProduct);
                }
            }

            $order->setTotal($total);
            $order->setCurrency($currency);

            $this->em->persist($order);
            $this->em->flush();
        }

        $this->shoppingCartService->emptyShoppingCartData();
        $this->saveLatestOrder($order);

        return $order;
    }

    /**
     * Handle payment method
     *
     * @param Order $order
     * @param FormInterface $form
     * @return array
     */
    public function handlePaymentMethod(
        Order $order,
        FormInterface $form
    ): array
    {
        $feedback = [
            'status' => false,
            'message' => '',
            'redirect' => null
        ];

        if ($order->getPaymentType()->getId() === self::CREDIT_CARD_PAYMENT_TYPE_ID) {
            try {

                /**
                 * Disable HTTP request implementation because it requires a public IP
                 */
                /*$ccValidationRequest = $this->client->request(
                    'POST',
                    $this->router->generate('3dsecure_register', [], UrlGeneratorInterface::ABSOLUTE_URL),
                    [
                        'body' => [
                            'merchant' => 'Wristwatch Store',
                            'amount' => $order->getTotal(),
                            'currency' => $order->getCurrency()->getName(),
                            'creditCardName' => $form->get('creditCardName')->getData(),
                            'creditCardNumber' => $form->get('creditCardNumber')->getData(),
                            'creditCardExpiration' => $form->get('creditCardExpiration')->getData(),
                            'creditCardCvv' => $form->get('creditCardCvv')->getData(),
                            'orderId' => $order->getId(),
                            'token' => $order->getToken(),
                            'feedbackUrl' => base64_encode($this->router->generate('set_order_status', [], UrlGeneratorInterface::ABSOLUTE_URL)),
                            'backTo' => base64_encode($this->router->generate('shopping_cart_feedback', [], UrlGeneratorInterface::ABSOLUTE_URL))
                        ]
                    ]
                );

                $feedback = $ccValidationRequest->toArray();*/

                $feedback = $this->paymentService->validatePaymentRequest(
                    'Wristwatch Store',
                    $order->getTotal(),
                    $order->getCurrency()->getName(),
                    $form->get('creditCardName')->getData(),
                    $form->get('creditCardNumber')->getData(),
                    $form->get('creditCardExpiration')->getData(),
                    $form->get('creditCardCvv')->getData(),
                    $order->getId(),
                    $order->getToken(),
                    base64_encode($this->router->generate('set_order_status', [], UrlGeneratorInterface::ABSOLUTE_URL)),
                    base64_encode($this->router->generate('shopping_cart_feedback', [], UrlGeneratorInterface::ABSOLUTE_URL))
                );

                if (empty($feedback['status'])) {
                    // Set order payment status as failed
                    $order->setPaymentStatus(Order::FAILED_PAYMENT_STATUS);
                    $this->em->persist($order);
                    $this->em->flush();
                } else {
                    $feedback['redirect'] = $this->router->generate(
                        '3dsecure',
                        [
                            'paymentValidationRequest' => $feedback['id']
                        ],
                        UrlGeneratorInterface::ABSOLUTE_URL
                    );
                }
            } catch (Exception | TransportExceptionInterface | DecodingExceptionInterface $e) {
                // TODO :: ...
            }
        }

        return $feedback;
    }

    /**
     * Get latest order ID from session
     *
     * @return mixed
     */
    public function getLatestOrderId()
    {
        return $this->session->get(self::LATEST_ORDER_KEYWORD);
    }

    /**
     * Save latest order ID in session
     *
     * @param Order $order
     * @return mixed
     */
    public function saveLatestOrder(Order $order)
    {
        return $this->session->set(self::LATEST_ORDER_KEYWORD, $order->getId());
    }

    /**
     * Set order status
     *
     * @param int $orderId
     * @param string $token
     * @param int $status
     * @return array
     */
    public function setOrderStatus(
        int $orderId,
        string $token,
        int $status
    ): array
    {
        $feedback = [
            'status' => false,
            'message' => ''
        ];

        if (!$orderId) {
            $feedback['message'] = 'Invalid order ID.';
            return $feedback;
        }

        if (empty($token) || strlen($token) < 32) {
            $feedback['message'] = 'Invalid token.';
            return $feedback;
        }

        $order = $this->em->getRepository(Order::class)->find($orderId);

        if (!$order) {
            $feedback['message'] = 'Invalid order data.';
            return $feedback;
        }

        if ($order->getToken() !== $token) {
            $feedback['message'] = 'Invalid token.';
            return $feedback;
        }

        $order->setPaymentStatus($status);
        $this->em->persist($order);
        $this->em->flush();

        $feedback['status'] = true;

        return $feedback;
    }
}