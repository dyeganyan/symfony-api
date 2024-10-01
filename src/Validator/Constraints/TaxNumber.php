<?php

namespace App\Validator\Constraints;

use App\Enum\CountryCodeTaxMapping;
use Symfony\Component\Validator\Constraint;

#[\Attribute]
class TaxNumber extends Constraint
{
    public $message = 'Invalid tax number';
}
