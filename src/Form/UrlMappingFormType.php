<?php

namespace App\Form;

use App\Entity\UrlMapping;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class UrlMappingFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('longUrl', UrlType::class, [
                'label' => 'Длинный URL',
                'attr' => [
                    'placeholder' => 'Введите длинный URL',
                ],
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Сократить URL',
                'attr' => [
                    'class' => 'btn btn-primary',
                ],
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => UrlMapping::class,
        ]);
    }
}
