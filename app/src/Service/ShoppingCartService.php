<?php
/**
 * User: Marius Mertoiu
 * Date: 21/11/2021 11:47
 * Email: marius.mertoiu@gmail.com
 */

namespace App\Service;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ShoppingCartService
{
    /**
     * The key used in session for saving the shopping cart products
     */
    const PRODUCT_QUANTITY_SESSION_KEY = 'cart_products';

    /**
     * Default VAT
     */
    const DEFAULT_VAT = 1.19;

    /**
     * Default delivery cost
     */
    const DELIVERY_COST = 15;

    private SessionInterface $session;
    private ProductRepository $productRepository;

    public function __construct(
        RequestStack $requestStack,
        ProductRepository $productRepository
    ) {
        $this->session = $requestStack->getSession();
        $this->productRepository = $productRepository;
    }

    /**
     * Get saved shopping cart products
     *
     * @return ?array
     */
    public function getSavedProducts(): ?array
    {
        return $this->session->get(self::PRODUCT_QUANTITY_SESSION_KEY);
    }

    /**
     * Save a product quantity in the shopping cart
     *
     * @param Product $product
     * @param int $quantity
     * @return array|null
     */
    public function saveProduct(
        int $productId,
        int $quantity
    ): ?array
    {
        // Get current data
        $currentData = $this->getSavedProducts();

        // Assign the new quantity to the product
        if ($quantity > 0) {
            $currentData[$productId] = $quantity;
        } elseif (isset($currentData[$productId])) {
            unset($currentData[$productId]);
        }

        // Save the data
        $this->session->set(ShoppingCartService::PRODUCT_QUANTITY_SESSION_KEY, $currentData);

        return $currentData;
    }

    /**
     * Get valid products from saved products
     *
     * @return Product[]
     */
    public function getValidProducts(): array
    {
        $products = [];

        if (!empty($this->getSavedProducts())) {
            foreach ($this->getSavedProducts() as $productId => $quantity) {
                $product = $this->productRepository->find($productId);
                $product->setShoppingCartQuantity($quantity);

                $products[] = $product;
            }
        }

        return $products;
    }

    /**
     * Calculate and return the shopping cart total variables
     *
     * @return int[]
     */
    public function getTotal(): array
    {
        $feedback = [
            'products_cost' => 0,
            'products_cost_without_vat' => 0,
            'vat_value' => 0,
            'delivery_cost' => 0,
            'total' => 0,
            'currency' => ''
        ];

        foreach ($this->getValidProducts() as $product) {
            if ($product->getPrice() <= 0) {
                continue;
            }
            
            $feedback['products_cost'] += $product->getPrice() * $product->getShoppingCartQuantity();
            $feedback['products_cost_without_vat'] +=
                $product->getPriceWithoutVat() * $product->getShoppingCartQuantity();
            $feedback['vat_value'] += $product->getVatValue() * $product->getShoppingCartQuantity();
            $feedback['currency'] = $product->getCurrency()->getName();
        }

        $feedback['products_cost_without_vat'] = number_format($feedback['products_cost_without_vat'], 2, '.', '');
        $feedback['vat_value'] = number_format($feedback['vat_value'], 2, '.', '');
        $feedback['delivery_cost'] = self::DELIVERY_COST;
        $feedback['total'] = number_format($feedback['products_cost'] + self::DELIVERY_COST, 2, '.', '');

        return $feedback;
    }

    /**
     * Delete the saved data
     *
     * @return mixed
     */
    public function emptyShoppingCartData()
    {
        return $this->session->set(self::PRODUCT_QUANTITY_SESSION_KEY, null);
    }
}