<?php

namespace App\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

class FrenchToDateTimeTransformer implements DataTransformerInterface
{
    public function transform($value): mixed
    {
        if (null === $value) {
            return '';
        }

        return $value->format('d/m/Y');
    }

    public function reverseTransform($value): mixed
    {
        if (null === $value) {
            throw new TransformationFailedException('Vous devez fournir une date !');
        }

        $date = \DateTime::createFromFormat('d/m/Y', $value);

        if (false === $date) {
            throw new TransformationFailedException("Le format de la date n'est pas le bon !");
        }

        return $date;
    }
}
