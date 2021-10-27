<?php

namespace App\Form;

use App\Entity\Annonces;
use App\Entity\Categories;
use App\Entity\Departements;
use App\Entity\Regions;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\Constraints\File;

class AnnoncesTypesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class)
            ->add('price')
            ->add('regions', EntityType::class, [
                'mapped' => false,
                'class' => Regions::class,
                'choice_label' => 'name',
                'placeholder' => 'Régions',
                'label' => 'Régions',
                'required' => false,
            ])
            ->add('departements', ChoiceType::class, [
                'placeholder' => 'Département (Choisir une région)',
                'required' => false,
                'label' => 'Départements'
            ])
            ->add('content', CKEditorType::class)
            ->add('categories', EntityType::class, [
                'class' => Categories::class
            ])
            ->add('images', FileType::class, [
                'label' => 'Ajouter une ou plusieurs images (jpg, png, gif acceptés)',
                'multiple' => true,
                'mapped' => false,
                'required' => false,
            ])
            ->add('Valider', SubmitType::class);

        $formModifier = function(FormInterface $form, Regions $region = null){
            $departements = null === $region ? [] : $region->getDepartements();

            $form->add('departements', EntityType::class, [
                'class' => Departements::class,
                'choices' => $departements,
                'choice_label' => 'name',
                'placeholder' => 'Département (Choisir une région)',
                'label' => 'Départements',
                'required' => false,
            ]);
        };

        $builder->get('regions')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) use ($formModifier) {
                $region = $event->getForm()->getData();
                $formModifier($event->getForm()->getParent(), $region);
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Annonces::class,
        ]);
    }
}
