<?php

namespace App\Helpers;

use App\Models\Unit;

class UnitHelper
{
    // Convert to base unit (e.g. 1 kg -> 1000 grams)
    public static function convertToBase($amount, $unitName)
    {
        $unit = Unit::where('name', $unitName)->first();
        if (!$unit) return $amount;

        return $amount * $unit->conversion_factor;
    }

    // Convert from base unit to main unit (e.g. 1000 grams -> 1 kg)
    public static function convertFromBase($amount, $unitName)
    {
        $unit = Unit::where('name', $unitName)->first();
        if (!$unit || $unit->conversion_factor == 0) return $amount;

        return $amount / $unit->conversion_factor;
    }
}
