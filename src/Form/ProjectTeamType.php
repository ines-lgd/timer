<?php

namespace App\Form;

use App\Entity\Project;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProjectTeamType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('name', TextType::class, [
                'label'    => 'Nom : ',
                'required' => 1,
                'attr'     => [
                    'class' => 'form-control col-xl-6 col-md-6 col-lg-6 p-0 mb-3',
                ]
            ])
            ->add('description', TextareaType::class, [
                'label'    => 'Description : ',
                'required' => 1,
                'attr'     => [
                    'class' => 'form-control col-xl-12 col-md-12 col-lg-12 p-0 mb-3',
                ]
            ])
            ->add('leader', EntityType::class, [
                'class' => User::class,
                'choices' => $options['data']->getTeam()->getUsers(),
                'choice_label' => 'name'
            ])
            ->add('submit', SubmitType::class, [
                'label'     => "Confirmer",
                'attr' => [
                    'class' => 'btn btn-primary',
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
        ]);
    }
}
