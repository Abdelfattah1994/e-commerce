<?php

namespace App\Form;

use App\Entity\SiteSettings;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SiteSettingsType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('siteName', TextType::class)
            ->add('logo', TextType::class, ['required' => false])
            ->add('favicon', TextType::class, ['required' => false])
            ->add('heroTitle', TextType::class)
            ->add('heroSubtitle', TextType::class)
            ->add('contactEmail', EmailType::class)
            ->add('contactPhone', TextType::class, ['required' => false])
            ->add('contactAddress', TextType::class, ['required' => false]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => SiteSettings::class,
        ]);
    }
}
