<?php

namespace App\Form;

use App\Entity\Articles;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticlesFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name')
            ->add('price')
            ->add('Description')
            ->add('image', FileType::class, [
                'label' => 'Image',
                'mapped' => false, // Indique que ce champ ne correspond pas à une propriété de l'entité
                'required' => false, // Le champ n'est pas obligatoire
                'attr' => ['accept' => 'image/*'], // Accepter uniquement les fichiers image
            ])        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Articles::class,
        ]);
    }
}
