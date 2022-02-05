<?php

namespace App\Form;

use App\Entity\Order;
use App\Form\EventListener\ClearCartListener;
use App\Form\EventListener\RemoveCartItemListener;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CartType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('items', CollectionType::class, [
                'entry_type' => CartItemType::class,
            ])
            ->add('save', SubmitType::class)
            ->add('clear', SubmitType::class);

        $builder->addEventSubscriber(new RemoveCartItemListener());
        $builder->addEventSubscriber(new ClearCartListener());
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Order::class,
        ]);
    }
}
