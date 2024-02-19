<?php

namespace App\Entity;

use Symfony\Component\PasswordHasher\PasswordHasherInterface;
use Symfony\Component\Security\Core\Validator\Constraints\UserPassword;
use Symfony\Component\Validator\Constraints as Assert;

class PasswordUpdate
{
    /**
     * @UserPassword(message="Mot de passe invalide !")
     */
    public $oldPassword;

    /**
     * @var string The hashed password
     */
    #[Assert\NotBlank]
    #[Assert\Length(
        min: '4',
        max: PasswordHasherInterface::MAX_PASSWORD_LENGTH,
        minMessage: 'Le mot de passe doit faire au moins {{ limit }} caractères',
        maxMessage: 'Le mot de passe ne doit pas dépasser {{ limit }} caractères'
    )]
    public $password;

    #[Assert\EqualTo(propertyPath: 'password', message: 'Les mots de passe de correspondent pas !')]
    public $confirmPassword;
}
