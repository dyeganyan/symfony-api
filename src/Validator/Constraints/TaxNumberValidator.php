<?php

namespace App\Validator\Constraints;

use App\Enum\CountryCodeTaxMapping;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

final class TaxNumberValidator extends ConstraintValidator
{
    public function validate($value, Constraint $constraint): void
    {
        preg_match("/^[A-Z]{2}/", $value, $matches);
        $countryCode = isset($matches[0]) ? $matches[0] : null;

        $availableCountryCodes = array_map(fn($case) => $case->name, CountryCodeTaxMapping::cases());

        if (!$countryCode || !in_array($countryCode, $availableCountryCodes)) {
            $message = $constraint->message . ': It must start with ' . implode(', ', $availableCountryCodes);
            $this->context->buildViolation($message)->addViolation();
        }
    }
}
