<?php

namespace App\Form;

use App\Entity\Project;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Eckinox\TinymceBundle\Form\Type\TinymceType;


class ProjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('imageFile', VichImageType::class, [
                'required' => false,
                'image_uri' => false,
                'delete_label' => false,
                'allow_delete' => false,
                'download_label' => false,
            ])
            ->add('filename')
            ->add('nbTasks')
            ->add('description', TinymceType::class, [
                "attr" => [
                    "toolbar" => "undo redo | styles | bold italic | alignleft aligncenter alignright alignjustify | outdent indent",
                ]
            ])
            // add roles choicetype
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'In Progress' => 'In Progress',
                    'Done' => 'Done',
                    'Blocked' => 'Blocked',
                ],
                'mapped' => false 
                
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Project::class,
        ]);
    }
}
