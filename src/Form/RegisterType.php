<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;

class RegisterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lastName', TextType::class, [
                'label'    => 'Nom : ',
                'required' => 1,
                'attr'     => [
                    'class' => 'form-control col-xl-6 col-md-6 col-lg-6 p-0 mb-3',
                ]
            ])
            ->add('firstName', TextType::class, [
                'label'    => 'Prénom : ',
                'required' => 1,
                'attr'     => [
                    'class' => 'form-control col-xl-6 col-md-6 col-lg-6 p-0 mb-3',
                ]
            ])
            ->add('pseudo', TextType::class, [
                'label'    => 'Pseudo : ',
                'required' => 1,
                'attr'     => [
                    'class' => 'form-control col-xl-6 col-md-6 col-lg-6 p-0 mb-3',
                ]
            ])
            ->add('email', EmailType::class, [
                'label'    => 'Email : ',
                'required' => 1,
                'attr'     => [
                    'class' => 'form-control col-xl-6 col-md-6 col-lg-6 p-0 mb-3',
                ]
            ])
            ->add('password', RepeatedType::class, [
                'first_options' => [
                    'label' => 'Mot de passe',
                    'attr' => [
                        'class' => 'form-control col-xl-6 col-md-6 col-lg-6 p-0 mb-3',
                        'required' => 1,
                    ],
                ],
                'second_options' => [
                    'label' => 'Confirmer le mot de passe',
                    'attr' => [
                        'class' => 'form-control col-xl-6 col-md-6 col-lg-6 p-0 mb-3',
                        'required' => 1,
                    ],
                ],
                'type' => PasswordType::class,
                'invalid_message' => 'Les mots de passe doivent être identiques.'
            ])
            ->add('submit', SubmitType::class, [
                'label'     => "S'inscrire",
                'attr' => [
                    'class' => 'btn btn-primary',
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
