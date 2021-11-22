<?php

namespace App\Entity;

use App\Repository\PaymentValidationRequestRepository;
use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PaymentValidationRequestRepository::class)
 */
class PaymentValidationRequest
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $merchant;

    /**
     * @ORM\Column(type="float")
     */
    private $amount;

    /**
     * @ORM\Column(type="string", length=10)
     */
    private $currency;

    /**
     * @ORM\Column(type="string", length=100)
     */
    private $creditCardName;

    /**
     * @ORM\Column(type="string", length=16)
     */
    private $creditCardNumber;

    /**
     * @ORM\Column(type="datetime")
     */
    private $createdAt;

    /**
     * @ORM\Column(type="text")
     */
    private $backTo;

    /**
     * @ORM\Column(type="string", length=32)
     */
    private $token;

    /**
     * @ORM\Column(type="integer")
     */
    private $orderId;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $feedbackUrl;

    /**
     * Number of seconds untill the request expires
     */
    const LIFE_LENGTH = 3000;

    public function __construct()
    {
        $this->createdAt = new DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMerchant(): ?string
    {
        return $this->merchant;
    }

    public function setMerchant(string $merchant): self
    {
        $this->merchant = $merchant;

        return $this;
    }

    public function getAmount(): ?float
    {
        return $this->amount;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function getCurrency(): ?string
    {
        return $this->currency;
    }

    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function getCreditCardName(): ?string
    {
        return $this->creditCardName;
    }

    public function setCreditCardName(string $creditCardName): self
    {
        $this->creditCardName = $creditCardName;

        return $this;
    }

    public function getCreditCardNumber(): ?string
    {
        return $this->creditCardNumber;
    }

    public function setCreditCardNumber(string $creditCardNumber): self
    {
        $this->creditCardNumber = $creditCardNumber;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeInterface $createdAt): self
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    public function getBackTo(): ?string
    {
        return $this->backTo;
    }

    public function setBackTo(string $backTo): self
    {
        $this->backTo = $backTo;

        return $this;
    }

    /**
     * Check if the request has expired
     *
     * @return bool
     */
    public function isExpired(): bool
    {
        $difference = (new DateTime())->getTimestamp() - $this->getCreatedAt()->getTimestamp();

        return $difference > self::LIFE_LENGTH;
    }

    public function getToken(): ?string
    {
        return $this->token;
    }

    public function setToken(string $token): self
    {
        $this->token = $token;

        return $this;
    }

    public function getOrderId(): ?int
    {
        return $this->orderId;
    }

    public function setOrderId(int $orderId): self
    {
        $this->orderId = $orderId;

        return $this;
    }

    public function getFeedbackUrl(): ?string
    {
        return $this->feedbackUrl;
    }

    public function setFeedbackUrl(string $feedbackUrl): self
    {
        $this->feedbackUrl = $feedbackUrl;

        return $this;
    }
}
