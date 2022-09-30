<?php

namespace App\Helpers;

use Symfony\Component\Form\FormInterface;
use Symfony\Component\Validator\ConstraintViolationList;

/** @codeCoverageIgnore */
class ValidatorParser {

    public const ERROR_REGEX = '/\[([\w\_\-]+)\]/';
    public const TARGET = 'target_property';

    public static function handleViolationList(ConstraintViolationList $violationList): array {
        $violations = [];
        foreach($violationList as $violation) {
            $payload = $violation?->getConstraint()?->payload ?? [];
            $propertyPath = $payload[self::TARGET] ?? $violation->getPropertyPath();
            $violations[$propertyPath][] = $violation->getMessage();
        }
        return $violations;
    }

    public static function handleFormError(FormInterface $form): array {
        $errors = [];

        foreach($form->getErrors(true, true) as $error) {
            if($error->getCause() !== null)
                $key = $error->getCause()->getPropertyPath() == null
                    ? $error->getOrigin()?->getName()
                    : self::formatErrorPath($error->getCause()->getPropertyPath());
            else {
                $key = 'default';
            }

            $errors[$key] = $error->getMessage();
        }

        return $errors;
    }


    protected static function formatErrorPath($path) {
        preg_match_all(self::ERROR_REGEX, $path, $matches, PREG_SET_ORDER, 0);
        $error = implode('.', array_map(function ($key) {
            return $key[1];
        }, $matches));
        return $error === '' ? 'error' : $error;
    }
}