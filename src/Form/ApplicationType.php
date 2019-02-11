<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;

abstract class ApplicationType extends AbstractType
{
    /**
     * Permet d'avoir la configuration de base d'un champ.
     *
     * @param string $label
     * @param string $placeholder
     */
    protected function getConfiguration($label, $placeholder, array $options = []): array
    {
        return array_merge_recursive([
            'label' => $label,
            'attr' => [
                'placeholder' => $placeholder,
            ], ], $options);
    }
}
