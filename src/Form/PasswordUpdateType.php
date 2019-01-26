<?php

namespace App\Form;

use App\Entity\PasswordUpdate;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PasswordUpdateType extends ApplicationType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('oldPassword', PasswordType::class, $this->getConfiguration('Mot de passe Actuel', 'Mot de passe Actuel ...'))
            ->add('password', PasswordType::class, $this->getConfiguration('Nouveau mot de passe', 'Choisissez un bon mot de passe !'))
            ->add('confirmPassword', PasswordType::class, $this->getConfiguration('Confirmation du nouveau mot de passe', 'Veuillez confirmer votre mot de passe'));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PasswordUpdate::class,
        ]);
    }
}
