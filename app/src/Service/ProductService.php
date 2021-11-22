<?php
/**
 * User: Marius Mertoiu
 * Date: 20/11/2021 19:24
 * Email: marius.mertoiu@gmail.com
 */

namespace App\Service;

use App\Entity\Product;
use App\Entity\ProductImage;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\FormInterface;

class ProductService
{
    private EntityManagerInterface $em;
    private ProductImageUploader $productImageUploader;

    public function __construct(
        EntityManagerInterface $em,
        ProductImageUploader $productImageUploader
    ) {
        $this->em = $em;
        $this->productImageUploader = $productImageUploader;
    }

    public function parseProductFormData(FormInterface $form): ?Product
    {
        $product = $form->getData();

        $this->em->persist($product);

        // Upload product images
        $images = $form->get('images')->getData();

        foreach ($images as $image) {
            $upload = $this->productImageUploader->upload($image);

            if ($upload) {
                $productImage = new ProductImage();
                $productImage->setName($upload);
                $productImage->setProduct($product);

                $this->em->persist($productImage);
            }
        }

        $this->em->flush();

        return $product;
    }

}