<?php

namespace App\Controller;

use App\Entity\PaymentValidationRequest;
use App\Service\CreditCardValidator;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class PaymentController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(
        EntityManagerInterface $em
    ) {
        $this->em = $em;
    }

    /**
     * Mocked 3D secure. It parses a POST request and saves the info in database
     *
     * @Route("/3dsecure_register", name="3dsecure_register", methods={"POST"})
     */
    public function mocked3dSecureRegister(Request $request): JsonResponse
    {
        $feedback = [
            'status' => false,
            'message' => ''
        ];

        $merchant = $request->request->get('merchant');
        $amount = $request->request->get('amount');
        $currency = $request->request->get('currency');
        $creditCardName = $request->request->get('creditCardName');
        $creditCardNumber = $request->request->get('creditCardNumber');
        $creditCardCvv = $request->request->get('creditCardCvv');
        $orderId = (int)$request->request->get('orderId');
        $token = $request->request->get('token');
        $feedbackUrl = $request->request->get('feedbackUrl');
        $backTo = $request->request->get('backTo');

        try {
            $creditCardExpiration = !empty($request->request->get('creditCardExpiration')) ?
                new DateTime('01/' . $request->request->get('creditCardExpiration')) :
                null
            ;
        } catch (Exception $e) {
            $creditCardExpiration = null;
        }

        if (empty($merchant)) {
            $feedback['message'] = 'Invalid merchant name.';
            return new JsonResponse($feedback);
        }

        if (empty($amount)) {
            $feedback['message'] = 'Invalid amount.';
            return new JsonResponse($feedback);
        }

        if (empty($currency)) {
            $feedback['message'] = 'Invalid currency.';
            return new JsonResponse($feedback);
        }

        if (empty($creditCardName)) {
            $feedback['message'] = 'Invalid credit card name.';
            return new JsonResponse($feedback);
        }

        if (empty($creditCardNumber)) {
            $feedback['message'] = 'Invalid credit card number.';
            return new JsonResponse($feedback);
        }

        if (empty($creditCardExpiration)) {
            $feedback['message'] = 'Invalid expiration date.';
            return new JsonResponse($feedback);
        }

        if (empty($creditCardCvv)) {
            $feedback['message'] = 'Invalid CVV.';
            return new JsonResponse($feedback);
        }

        if (empty($token) || strlen($token) < 32) {
            $feedback['message'] = 'Invalid token.';
            return new JsonResponse($feedback);
        }

        if (empty($feedbackUrl)) {
            $feedback['message'] = 'Invalid feedback URL.';
            return new JsonResponse($feedback);
        }

        if (empty($backTo)) {
            $feedback['message'] = 'Invalid back to data.';
            return new JsonResponse($feedback);
        }

        // Validate credit card data
        if (!CreditCardValidator::validateCreditCard($creditCardNumber, $creditCardExpiration)) {
            $feedback['message'] = 'Credit card invalid.';
            return new JsonResponse($feedback);
        }

        $parsedCcNumber = str_repeat('X', strlen($creditCardNumber) - 4) . substr($creditCardNumber, -4);

        // Save 3dSecure request
        $paymentValidationRequest = new PaymentValidationRequest();
        $paymentValidationRequest->setMerchant($merchant);
        $paymentValidationRequest->setAmount($amount);
        $paymentValidationRequest->setCurrency($currency);
        $paymentValidationRequest->setCreditCardName($creditCardName);
        $paymentValidationRequest->setCreditCardNumber($parsedCcNumber);
        $paymentValidationRequest->setOrderId($orderId);
        $paymentValidationRequest->setToken($token);
        $paymentValidationRequest->setFeedbackUrl($feedbackUrl);
        $paymentValidationRequest->setBackTo($backTo);

        $this->em->persist($paymentValidationRequest);
        $this->em->flush();

        $feedback['status'] = true;
        $feedback['message'] = 'Request saved successfully.';
        $feedback['id'] = $paymentValidationRequest->getId();

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
        HttpClientInterface $client
    ): Response
    {
        // Parse 3d Secure password
        if ($request->request->get('password')) {
            try {
                $feedbackRequest = $client->request(
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

                $feedback = $feedbackRequest->toArray();

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
