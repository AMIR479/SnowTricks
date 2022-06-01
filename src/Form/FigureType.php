<?php

namespace App\Form;

use App\Entity\Figure;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;

class FigureType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom')
            ->add('description')
            ->add('groupe')
            //On ajoute le champ "image" dans le formulaire
            ->add('image', FileType::class, [
                'label'=> ('Ajouter des images'),
                'multiple'=> true,
                'mapped'=> false,
                'required'=> false
            ])
            //On ajoute le champ "video" dans le formulaire
            ->add('video', FileType::class, [
                'label'=> ('Ajouter des videos'),
                'multiple'=> true,
                'mapped'=> false,
                'required'=> false
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Figure::class,
        ]);
    }
}
