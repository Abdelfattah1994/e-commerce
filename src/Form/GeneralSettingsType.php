<?php

namespace App\Form;

use App\Entity\SiteSettings;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GeneralSettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('siteName', TextType::class, ['label' => 'Nom du site'])
            ->add('logo', TextType::class, ['required' => false])
            ->add('favicon', TextType::class, ['required' => false])
            ->add('heroTitle', TextType::class)
            ->add('heroSubtitle', TextType::class)
            ->add('heroDescription', TextType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SiteSettings::class,
        ]);
    }
}
