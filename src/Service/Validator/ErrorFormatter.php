<?php
/**
 * Created by Marco Barrella <marco@barrella.it>.
 * User: marcobarrella
 * Date: 07/03/2020
 * Time: 15:30
 */

namespace App\Service\Validator;


use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class ErrorFormatter
{
    public static function normalizeError(ConstraintViolation $error): array
    {
        return [
            'property' => $error->getPropertyPath(),
            'message' => $error->getMessage()
        ];
    }

    public static function normalizeErrors(ConstraintViolationListInterface $errors): array
    {
        $errorsFormatted = [];

        foreach ($errors as $error) {

            array_push($errorsFormatted, self::normalizeError($error));
        }

        return $errorsFormatted;
    }
}