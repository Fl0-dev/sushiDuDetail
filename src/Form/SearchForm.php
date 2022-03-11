<?php

namespace App\Form;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SearchType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\SearchData;

class SearchForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
//            ->add('text',SearchType::class,[
//                'required'=>false,
//                'label'=>'Le nom du produit ?',
//            ])
            ->add('categories',EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
                'multiple'=>true,
                //'expanded'=>true,
                'by_reference'=> false,
                'required'=>false,
            ])
            ->add('min', NumberType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Prix min',
                ]
            ])
            ->add('max', NumberType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Prix max'
                ]
            ])
            ->add('order', ChoiceType::class, [
                'choices' => [
                    'A->Z' => 0,
                    'Z->A' => 1,
                    'Prix ASC' => 2,
                    'Prix DESC' => 3
                ]
            ])

        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
//            'data_class' => SearchData::class,
//            'method' => 'GET',
//            'csrf_protection' => false
        ]);
    }
}
