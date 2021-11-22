<?php
namespace App\Twig;

use App\Entity\Brand;
use App\Service\ShoppingCartService;
use Doctrine\ORM\EntityManagerInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class AppExtension extends AbstractExtension
{
    private EntityManagerInterface $em;
    private ShoppingCartService $shoppingCartService;

    public function __construct(
        EntityManagerInterface $em,
        ShoppingCartService $shoppingCartService
    ) {
        $this->em = $em;
        $this->shoppingCartService = $shoppingCartService;
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('getBrands', [$this, 'getBrands']),
            new TwigFunction('getShoppingCartProducts', [$this, 'getShoppingCartProducts']),
        ];
    }

    /**
     * Get brands
     *
     * @param int $limit
     * @return array
     */
    public function getBrands(int $limit = 6): array
    {
        return $this->em->getRepository(Brand::class)->findBy(
            [],
            [
                'name' => 'ASC'
            ],
            $limit
        );
    }

    /**
     * Get shopping cart products
     *
     * @return array|null
     */
    public function getShoppingCartProducts(): ?array
    {
        return $this->shoppingCartService->getSavedProducts();
    }
}