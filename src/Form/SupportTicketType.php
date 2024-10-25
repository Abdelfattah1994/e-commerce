<?php

namespace App\Form;

use App\Entity\SupportTicket;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SupportTicketType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('subject', TextType::class)
            ->add('message', TextareaType::class)
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'Ouvert' => 'OPEN',
                    'En cours' => 'IN_PROGRESS',
                    'Fermé' => 'CLOSED',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SupportTicket::class,
        ]);
    }
}
