<?php

namespace App\Form;

use App\Entity\Order;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstName', TextType::class, [
                'label' => 'shopping_cart.first_name',
                'row_attr' => [
                    'class' => 'col-sm-4'
                ]
            ])
            ->add('lastName', TextType::class, [
                'label' => 'shopping_cart.last_name',
                'row_attr' => [
                    'class' => 'col-sm-4'
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'shopping_cart.email',
                'row_attr' => [
                    'class' => 'col-sm-4'
                ]
            ])
            ->add('address', TextType::class, [
                'label' => 'shopping_cart.address',
                'attr' => [
                    'autocomplete' => 'address'
                ],
                'row_attr' => [
                    'class' => 'col-12'
                ]
            ])
            ->add('creditCardName', TextType::class, [
                'label' => 'shopping_cart.name_on_card',
                'mapped' => false,
                'help' => 'shopping_cart.card_name_info',
                'attr' => [
                    'autocomplete' => 'cc-name'
                ],
                'row_attr' => [
                    'class' => 'col-md-6'
                ]
            ])
            ->add('creditCardNumber', TextType::class, [
                'label' => 'shopping_cart.credit_card_number',
                'mapped' => false,
                'attr' => [
                    'autocomplete' => 'cc-number'
                ],
                'row_attr' => [
                    'class' => 'col-md-6'
                ]
            ])
            ->add('creditCardExpiration', TextType::class, [
                'label' => 'shopping_cart.expiration',
                'mapped' => false,
                'attr' => [
                    'autocomplete' => 'cc-expiration'
                ],
                'row_attr' => [
                    'class' => 'col-md-3'
                ]
            ])
            ->add('creditCardCvv', TextType::class, [
                'label' => 'shopping_cart.cvv',
                'mapped' => false,
                'attr' => [
                    'autocomplete' => 'cc-cvv'
                ],
                'row_attr' => [
                    'class' => 'col-md-3'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }
}
