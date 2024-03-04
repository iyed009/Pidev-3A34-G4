<?php

namespace App\Form;

use App\Data\SearchData;
use App\Entity\CategorieP;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SearchForm extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('q',TextType::class, [
                'label' => false,
                'required' => false,
                'attr' => [
                    'placeholder' => 'Rechercher'
                ]
            ])
            ->add('categorieP',EntityType::class,[
                'label' => false,
                'required' => false,
                'class' =>CategorieP::class,
                'expanded'=> true,
                'multiple' => true
    ])
            ->add('min', NumberType::class,[
                'label' => false,
                'required' => false,
                'attr' =>[
                    'placeholder' => 'Prix Min'
                ]
            ])
            ->add('max', NumberType::class,[
                'label' => false,
                'required' => false,
                'attr' =>[
                    'placeholder' => 'Prix Max'
                ]
            ]);
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SearchData:: class,
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }
    public function getBlockPrefix()
    {
        return '';
    }
}
