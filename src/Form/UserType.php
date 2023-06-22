<?php

namespace App\Form;

use App\Entity\Order;
use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Email',
                ])
            ->add('password', PasswordType::class, [
                'label' => 'Password',
            ])
            ->add('roles', ChoiceType::class, [
                'choices' => ['User'=>'ROLE_USER', 'Admin'=>'ROLE_ADMINISTRATOR'],
                'multiple'=> true 
            ]);
            
            
        
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
