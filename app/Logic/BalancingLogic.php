<?php

namespace App\Logic;

use InvalidArgumentException;

class BalancingLogic
{
    public static function validate(array $plan): array
    {
        if (empty($plan)) {
            return ['Plan cannot be empty'];
        }

        $errors = [];
        foreach ($plan as $index => $value) {
            if (!is_int($value)) {
                $errors[] = "Slot {$index}: value must be non-negative integer";
            } elseif ($value < 0) {
                $errors[] = "Slot {$index}: value must be non-negative integer";
            }
        }

        return $errors;
    }

    public static function balance(array $plan): array
    {
        $errors = self::validate($plan);
        if (!empty($errors)) {
            throw new InvalidArgumentException(implode(', ', $errors));
        }

        $total = array_sum($plan);
        $activeIndices = [];

        foreach ($plan as $index => $value) {
            if ($value > 0) {
                $activeIndices[] = $index;
            }
        }

        $activeCount = count($activeIndices);
        if ($activeCount === 0) {
            return array_fill(0, count($plan), 0);
        }

        $base = intdiv($total, $activeCount);
        $remainder = $total % $activeCount;

        $balanced = array_fill(0, count($plan), 0);

        usort($activeIndices, function ($a, $b) use ($plan) {
            if ($plan[$a] === $plan[$b]) {
                return $a <=> $b;
            }
            return $plan[$b] <=> $plan[$a];
        });

        foreach ($activeIndices as $i => $index) {
            $balanced[$index] = $base + ($i < $remainder ? 1 : 0);
        }

        ksort($balanced);
        return $balanced;
    }
}
