<?php
/**
 * User: Marius Mertoiu
 * Date: 22/11/2021 20:55
 * Email: marius.mertoiu@gmail.com
 */

namespace App\Service;

use App\Entity\PaymentValidationRequest;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;

class PaymentService
{
    private EntityManagerInterface $em;
    
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Validate and register payment request
     *
     * @param string $merchant
     * @param float $amount
     * @param string $currency
     * @param string $creditCardName
     * @param string $creditCardNumber
     * @param string $creditCardExpiration
     * @param string $creditCardCvv
     * @param int $orderId
     * @param string $token
     * @param string $feedbackUrl
     * @param string $backTo
     * @return array
     */
    public function validatePaymentRequest(
        string $merchant,
        float $amount,
        string $currency,
        string $creditCardName,
        string $creditCardNumber,
        string $creditCardExpiration,
        string $creditCardCvv,
        int $orderId,
        string $token,
        string $feedbackUrl,
        string $backTo
    ): array
    {
        $feedback = [
            'status' => false,
            'message' => ''
        ];

        try {
            $creditCardExpiration = !empty($creditCardExpiration) ?
                new DateTime('01/' . $creditCardExpiration) :
                null
            ;
        } catch (Exception $e) {
            $creditCardExpiration = null;
        }

        if (empty($merchant)) {
            $feedback['message'] = 'Invalid merchant name.';
            return $feedback;
        }

        if (empty($amount)) {
            $feedback['message'] = 'Invalid amount.';
            return $feedback;
        }

        if (empty($currency)) {
            $feedback['message'] = 'Invalid currency.';
            return $feedback;
        }

        if (empty($creditCardName)) {
            $feedback['message'] = 'Invalid credit card name.';
            return $feedback;
        }

        if (empty($creditCardNumber)) {
            $feedback['message'] = 'Invalid credit card number.';
            return $feedback;
        }

        if (empty($creditCardExpiration)) {
            $feedback['message'] = 'Invalid expiration date.';
            return $feedback;
        }

        if (empty($creditCardCvv)) {
            $feedback['message'] = 'Invalid CVV.';
            return $feedback;
        }
        
        if ($orderId <= 0) {
            $feedback['message'] = 'Invalid order ID.';
        }

        if (empty($token) || strlen($token) < 32) {
            $feedback['message'] = 'Invalid token.';
            return $feedback;
        }

        if (empty($feedbackUrl)) {
            $feedback['message'] = 'Invalid feedback URL.';
            return $feedback;
        }

        if (empty($backTo)) {
            $feedback['message'] = 'Invalid back to data.';
            return $feedback;
        }

        // Validate credit card data
        if (!CreditCardValidator::validateCreditCard($creditCardNumber, $creditCardExpiration)) {
            $feedback['message'] = 'Credit card invalid.';
            return $feedback;
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
        
        return $feedback;
    }
}