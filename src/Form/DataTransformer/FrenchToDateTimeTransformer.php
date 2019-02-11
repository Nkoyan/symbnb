<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class FrenchToDateTimeTransformer implements DataTransformerInterface
{
    public function transform($date)
    {
        if (null === $date) {
            return '';
        }

        return $date->format('d/m/Y');
    }

    public function reverseTransform($frenchDate)
    {
        if (null === $frenchDate) {
            throw new TransformationFailedException('Vous devez fournir une date !');
        }

        $date = \DateTime::createFromFormat('d/m/Y', $frenchDate);

        if (false === $date) {
            throw new TransformationFailedException("Le format de la date n'est pas le bon !");
        }

        return $date;
    }
}
