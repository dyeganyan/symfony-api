<?php

namespace App\Enum;

enum DiscountType: string
{
    case FIXED_AMOUNT = 'fixed_amount';
    case PERCENTAGE = 'percentage';
}
