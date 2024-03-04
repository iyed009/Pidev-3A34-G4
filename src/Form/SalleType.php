<?php

namespace App\Form;

use App\Entity\Salle;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class SalleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('addresse')
            ->add('numTel')
            ->add('capacite')
            ->add('nbrClient')
            ->add('description')
           // ->add('utilisateur')
           ->add('logoSalle',FileType::class,[
               'label' => 'logoSalle',
               'mapped' => false,
               'required' => false,
               'constraints' => [
                   new Assert\File([
                       'maxSize' => '2Mi',
                       'mimeTypesMessage' => 'Please upload a valid image file',
                   ])
               ],])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Salle::class,
        ]);
    }
}
