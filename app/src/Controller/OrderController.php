<?php

namespace App\Controller;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * @Route("/admin/order/list", name="order_list")
     */
    public function adminOrderList(): Response
    {
        return $this->render('admin/order/list.html.twig', [
            'orders' => $this->em->getRepository(Order::class)->findAll()
        ]);
    }

    /**
     * @Route("/order/set_status", name="set_order_status", methods={"POST"})
     */
    public function setOrderStatus(Request $request): JsonResponse
    {
        $feedback = [
            'status' => false,
            'message' => ''
        ];

        $orderId = (int)$request->request->get('order_id');
        $token = $request->request->get('token');
        $status = (int)$request->request->get('status');

        if (!$orderId) {
            $feedback['message'] = 'Invalid order ID.';
            return new JsonResponse($feedback);
        }

        if (empty($token) || strlen($token) < 32) {
            $feedback['message'] = 'Invalid token.';
            return new JsonResponse($feedback);
        }

        $order = $this->em->getRepository(Order::class)->find($orderId);

        if (!$order) {
            $feedback['message'] = 'Invalid order data.';
            return new JsonResponse($feedback);
        }

        if ($order->getToken() !== $token) {
            $feedback['message'] = 'Invalid token.';
            return new JsonResponse($feedback);
        }

        $order->setPaymentStatus($status);
        $this->em->persist($order);
        $this->em->flush();

        $feedback['status'] = true;

        return new JsonResponse($feedback);
    }
}
