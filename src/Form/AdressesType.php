<?php

namespace App\Form;

use App\Entity\Address;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CountryType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdressesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {   $user = $options['user'];
        $builder
        // ->add('user', HiddenType::class, [
        //     'data' => $user->getId() ,
        //     // $options['user']->getId()
        // ])
            ->add('title' , TextType::class, ['attr' => ['class' => 'form-control']])
            ->add('firstName' , TextType::class, ['attr' => ['class' => 'form-control']])
            ->add('lastName' , TextType::class, ['attr' => ['class' => 'form-control']])
            ->add('company' , TextType::class , [
                'required'   => false,
                'attr' => ['class' => 'form-control']
                
                ])
            ->add('address' , TextType::class, ['attr' => ['class' => 'form-control']])
            ->add('postalCode' , TextType::class, ['attr' => ['class' => 'form-control']])
            ->add('country' , CountryType::class)
            ->add('phone' , TextType::class, ['attr' => ['class' => 'form-control']])
            ->add('city' , TextType::class, ['attr' => ['class' => 'form-control']])
            ->add('envoyer' , SubmitType::class, ['attr' => ['class' => 'btn btn-primary mt-3']]);
          
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Address::class,
            'user' => null
        ]);
    }
}
