<?php
/**
 * User: Marius Mertoiu
 * Date: 21/11/2021 20:26
 * Email: marius.mertoiu@gmail.com
 */

namespace App\Service;

use DateTime;

class CreditCardValidator
{
    const VALID_CREDIT_CARD_NUMBERS = [
        '5000411132229999',
        '5000411132229998',
        '5000411132229997'
    ];

    /**
     * Mocked credit card validation
     *
     * @param string $number
     * @param DateTime $expiration
     * @return bool
     */
    public static function validateCreditCard(
        string $number,
        DateTime $expiration
    ): bool
    {
        // Validate expiration date
        if ($expiration < new DateTime()) {
            return false;
        }

        // Validate credit card number
        $number = str_replace(' ', '', $number);
        if (!in_array($number, self::VALID_CREDIT_CARD_NUMBERS)) {
            return false;
        }

        return true;
    }
}