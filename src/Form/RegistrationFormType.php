<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use VictorPrdh\RecaptchaBundle\Form\ReCaptchaType; // Ensure you have this use statement

class RegistrationFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', null, [
                'required' => false,
            ])
            ->add('prenom', null, [
                'required' => false,
            ])
            ->add('email', null, [
                'required' => false,
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => ['label' => 'Password'],
                'second_options' => ['label' => 'Confirm Password'],
                'invalid_message' => 'The password fields must match.',
                'required' => true,
            ])
            ->add('numTele', null, [
                'required' => false,
            ])
            ->add('adresse', null, [
                'required' => false,
            ])
            ->add('roles', TextType::class, [
                'mapped' => false, // This field does not directly map to the entity's property
                'disabled' => true, // Makes the field non-interactive
                'required' => false, // This field is not required
                'label' => 'Role', // Label for the form field
                'attr' => ['value' => 'CLIENT'], // You could dynamically set this based on the actual user role
            ])

            ->add('avatar', FileType::class, [
                'label' => 'avatar',
                'mapped' => false,
                'required' => false,
                'constraints' => [
                    new Assert\File([
                        'maxSize' => '2Mi',
                        'mimeTypesMessage' => 'Please upload a valid image file',
                    ])
                ],
            ])
            ->add('agreeTerms', CheckboxType::class, [
                'mapped' => false,
                'constraints' => [
                    new IsTrue([
                        'message' => 'You should agree to our terms.',
                    ]),
                ],
                'label' => 'I agree to the terms and conditions',
            ])
            ->add('recaptcha', ReCaptchaType::class, [ // Add the reCAPTCHA field here
                'mapped' => false, // reCAPTCHA field is usually not mapped to any entity property
                // You can add additional options if needed
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'attr' => ['novalidate' => 'novalidate'], // Assuming you're disabling HTML5 validation for testing
        ]);
    }
}
