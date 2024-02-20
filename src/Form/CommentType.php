<?php

namespace App\Form;

use App\Entity\Comment;
use App\Entity\Product;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\RangeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('content', TextareaType::class,[
            'attr' => [
                'class' => 'form-control'
            ]])
            ->add('rating', RangeType::class, [
                'label' => 'Note (de 0 à 5)',
                'attr' => [
                    'min' => 0,
                    'max' => 5,
                    'class' => 'mt-4'
                ]
            ])
            ->add('envoyer', SubmitType::class,[
                'attr' => [
                    'class' => 'btn btn-primary mt-4'
                ]]);


        // ->add('product', EntityType::class, [
        //     'class' => Product::class,
        //     'choice_label' => 'id',
        // ])
        // ->add('user', EntityType::class, [
        //     'class' => User::class,
        //     'choice_label' => 'id',
        // ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Comment::class,
        ]);
    }
}
