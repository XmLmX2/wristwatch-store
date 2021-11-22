<?php

namespace App\Controller;

use App\Entity\Order;
use App\Service\OrderService;
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
    public function setOrderStatus(
        Request $request,
        OrderService $orderService
    ): JsonResponse
    {
        $feedback = $orderService->setOrderStatus(
            (int)$request->request->get('order_id'),
            $request->request->get('token'),
            (int)$request->request->get('status')
        );

        return new JsonResponse($feedback);
    }
}
