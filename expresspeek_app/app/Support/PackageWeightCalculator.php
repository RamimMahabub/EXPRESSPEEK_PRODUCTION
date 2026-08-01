<?php

namespace App\Support;

use Illuminate\Validation\ValidationException;

final class PackageWeightCalculator
{
    public const DIVISOR = 5000;

    /**
     * Normalize package rows and store the chargeable weight in the legacy
     * `weight` key so existing quotes, labels, and invoices remain compatible.
     * Actual weight is mandatory; dimensions are an optional comparison.
     */
    public static function normalize(array $packages): array
    {
        return collect($packages)
            ->map(function (array $package, int|string $index): array {
                $actualWeight = max((float) ($package['weight'] ?? 0), 0);
                $length = max((float) ($package['length'] ?? 0), 0);
                $width = max((float) ($package['width'] ?? 0), 0);
                $height = max((float) ($package['height'] ?? 0), 0);
                $dimensions = [$length, $width, $height];
                $hasAnyDimension = collect($dimensions)->contains(fn (float $value) => $value > 0);
                $hasAllDimensions = collect($dimensions)->every(fn (float $value) => $value > 0);

                if ($actualWeight <= 0) {
                    throw ValidationException::withMessages([
                        "packages.{$index}.weight" => 'Actual package weight is required.',
                    ]);
                }

                if ($hasAnyDimension && ! $hasAllDimensions) {
                    throw ValidationException::withMessages([
                        "packages.{$index}.length" => 'Enter length, width, and height together, or clear all dimensions.',
                    ]);
                }

                $volumetricWeight = $hasAllDimensions
                    ? ($length * $width * $height) / self::DIVISOR
                    : 0;
                $chargeableWeight = max($actualWeight, $volumetricWeight);
                $roundedChargeableWeight = max(round($chargeableWeight, 2), 0.01);

                return [
                    'weight' => $roundedChargeableWeight,
                    'actual_weight' => $actualWeight > 0 ? round($actualWeight, 2) : null,
                    'length' => $hasAllDimensions ? round($length, 2) : null,
                    'width' => $hasAllDimensions ? round($width, 2) : null,
                    'height' => $hasAllDimensions ? round($height, 2) : null,
                    'volumetric_weight' => $hasAllDimensions ? round($volumetricWeight, 2) : null,
                    'chargeable_weight' => $roundedChargeableWeight,
                    'quantity' => max((int) ($package['quantity'] ?? 1), 1),
                ];
            })
            ->values()
            ->all();
    }

    public static function totals(array $packages): array
    {
        $totalPackages = 0;
        $totalWeight = 0.0;

        foreach ($packages as $package) {
            $quantity = max((int) ($package['quantity'] ?? 1), 1);
            $totalPackages += $quantity;
            $totalWeight += (float) ($package['chargeable_weight'] ?? $package['weight'] ?? 0) * $quantity;
        }

        return [
            'packages' => $totalPackages,
            'weight' => round($totalWeight, 2),
        ];
    }
}
