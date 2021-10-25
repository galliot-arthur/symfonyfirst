<?php

namespace App\Form;

use App\Entity\Messages;
use App\Entity\Users;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MessagesType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                "label" => false,
                "attr" => [
                    "placeholder" => "Objet"
                ]
            ])
            ->add('message', TextareaType::class)
            //->add('created_at')
            //->add('is_read')
            ->add('recipient', EntityType::class, [
                "label" => "Destinataire",
                "class" => Users::class,
                "choice_label" => "name"
            ])
            ->add('Envoyer', SubmitType::class,[
                "attr" => [
                    "class" => "btn-dark"
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Messages::class,
        ]);
    }
}
