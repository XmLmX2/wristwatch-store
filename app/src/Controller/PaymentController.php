<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\PaymentValidationRequest;
use App\Service\OrderService;
use App\Service\PaymentService;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PaymentController extends AbstractController
{
    /**
     * Mocked 3D secure. It parses a POST request and saves the info in database
     *
     * @Route("/3dsecure_register", name="3dsecure_register", methods={"POST"})
     */
    public function mocked3dSecureRegister(
        Request $request,
        PaymentService $paymentService
    ): JsonResponse
    {
        $feedback = $paymentService->validatePaymentRequest(
            $request->request->get('merchant'),
            $request->request->get('amount'),
            $request->request->get('currency'),
            $request->request->get('creditCardName'),
            $request->request->get('creditCardNumber'),
            $request->request->get('creditCardExpiration'),
            $request->request->get('creditCardCvv'),
            $request->request->get('orderId'),
            $request->request->get('token'),
            $request->request->get('feedbackUrl'),
            $request->request->get('backTo'),

        );

        return new JsonResponse($feedback);
    }

    /**
     * Mocked 3D secure
     *
     * @Route("/3dsecure/{paymentValidationRequest}", name="3dsecure", methods={"GET", "POST"})
     */
    public function mocked3dSecure(
        PaymentValidationRequest $paymentValidationRequest,
        Request $request,
        HttpClientInterface $client,
        OrderService $orderService
    ): Response
    {
        // Parse 3d Secure password
        if ($request->request->get('password')) {

            try {
                /**
                 * Disable HTTP request implementation because it requires a public IP
                 */
                /*$feedbackRequest = $client->request(
                    'POST',
                    base64_decode($paymentValidationRequest->getFeedbackUrl()),
                    [
                        'body' => [
                            'order_id' => $paymentValidationRequest->getOrderId(),
                            'token' => $paymentValidationRequest->getToken(),
                            'status' => 1
                        ]
                    ]
                );

                $feedback = $feedbackRequest->toArray();*/

                $feedback = $orderService->setOrderStatus(
                    $paymentValidationRequest->getOrderId(),
                    $paymentValidationRequest->getToken(),
                    1
                );

                if (!empty($feedback['status'])) {
                    return $this->redirect(base64_decode($paymentValidationRequest->getBackTo()));
                }
            } catch (Exception $e) {
                // TODO :: ...
            }


        }

        return $this->render('site/payment/3dsecure.html.twig', [
            'payment' => $paymentValidationRequest
        ]);
    }
}
