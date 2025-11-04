<?php

namespace Database\Seeders;

use App\Models\Bill;
use App\Models\BillItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BillItemSeeder extends Seeder
{
    public function run(): void
    {
        $bills = Bill::with(['appointment.doctor'])->get();

        if ($bills->isEmpty()) {
            return;
        }

        DB::transaction(function () use ($bills) {
            foreach ($bills as $bill) {
                BillItem::where('bill_id', $bill->id)->delete();

                $items = $this->buildItemsForBill($bill);

                foreach ($items as $item) {
                    BillItem::create([
                        'bill_id' => $bill->id,
                        'description' => $item['description'],
                        'amount' => $item['amount'],
                    ]);
                }

                $totalAmount = round(array_sum(array_column($items, 'amount')), 2);
                $bill->update(['total_amount' => $totalAmount]);
            }
        });
    }

    /**
     * Build line items for a bill ensuring realistic Malaysian clinic charges.
     *
     * @return list<array{description:string, amount:float}>
     */
    private function buildItemsForBill(Bill $bill): array
    {
        $target = round(max(100.00, min(500.00, (float) $bill->total_amount ?? 250.00)), 2);

        $specialisation = $bill->appointment?->doctor?->specialization;
        $consultationLabel = $specialisation
            ? sprintf('Consultation fee (%s)', $specialisation)
            : 'Consultation fee';

        $categories = [
            [
                'label' => $consultationLabel,
                'min' => 80.00,
                'max' => 300.00,
            ],
            [
                'label' => 'Medication charges',
                'min' => 20.00,
                'max' => 150.00,
            ],
        ];

        $optionalCategories = [
            [
                'label' => 'Laboratory investigations',
                'min' => 50.00,
                'max' => 200.00,
                'probability' => 0.55,
            ],
            [
                'label' => 'Diagnostic imaging services',
                'min' => 100.00,
                'max' => 300.00,
                'probability' => 0.40,
            ],
            [
                'label' => 'Minor procedure',
                'min' => 80.00,
                'max' => 250.00,
                'probability' => 0.35,
            ],
        ];

        shuffle($optionalCategories);

        $minSum = $this->sumAttribute($categories, 'min');
        $maxSum = $this->sumAttribute($categories, 'max');

        foreach ($optionalCategories as $index => $optional) {
            if ($minSum + $optional['min'] > $target) {
                continue;
            }

            if ($maxSum < $target) {
                $categories[] = $optional;
                $minSum += $optional['min'];
                $maxSum += $optional['max'];
                unset($optionalCategories[$index]);
            }
        }

        foreach ($optionalCategories as $optional) {
            if (count($categories) >= 5) {
                break;
            }

            if ($minSum + $optional['min'] > $target) {
                continue;
            }

            if ($this->shouldInclude($optional['probability'])) {
                $categories[] = $optional;
                $minSum += $optional['min'];
                $maxSum += $optional['max'];
            }
        }

        if ($target < $minSum) {
            $target = round($minSum, 2);
        } elseif ($target > $maxSum) {
            $target = round($maxSum, 2);
        }

        $amounts = $this->allocateAmounts($categories, $target);

        $items = [];
        foreach ($categories as $index => $category) {
            $items[] = [
                'description' => $category['label'],
                'amount' => $amounts[$index],
            ];
        }

        return $items;
    }

    /**
     * Allocate random amounts to categories while respecting min/max and total.
     *
     * @param  array<int, array{label:string, min:float, max:float}>  $categories
     * @return list<float>
     */
    private function allocateAmounts(array $categories, float $target): array
    {
        $count = count($categories);
        $amounts = [];
        $remaining = round($target, 2);

        foreach ($categories as $category) {
            $amounts[] = round($category['min'], 2);
            $remaining -= round($category['min'], 2);
        }

        $remaining = round(max(0.0, $remaining), 2);

        for ($i = 0; $i < $count; $i++) {
            $currentAmount = $amounts[$i];
            $category = $categories[$i];
            $capacity = round($category['max'] - $category['min'], 2);

            $remainingCapacityAfter = 0.0;
            for ($j = $i + 1; $j < $count; $j++) {
                $nextCategory = $categories[$j];
                $remainingCapacityAfter += round($nextCategory['max'] - $nextCategory['min'], 2);
            }
            $remainingCapacityAfter = round($remainingCapacityAfter, 2);

            $minExtra = round(max(0.0, $remaining - $remainingCapacityAfter), 2);
            $maxExtra = round(min($capacity, $remaining), 2);

            if ($maxExtra < $minExtra) {
                $extra = $minExtra;
            } else {
                $extra = $minExtra;
                $range = round($maxExtra - $minExtra, 2);
                if ($range > 0) {
                    $extra += $this->randomCurrency(0.00, $range);
                }
            }

            $extra = round(min($extra, $remaining), 2);
            $amounts[$i] = round($currentAmount + $extra, 2);
            $remaining = round($remaining - $extra, 2);
        }

        if ($remaining !== 0.0) {
            $lastIndex = $count - 1;
            $amounts[$lastIndex] = round($amounts[$lastIndex] + $remaining, 2);
        }

        return $amounts;
    }

    /**
     * Decide if an optional category should be included based on probability.
     */
    private function shouldInclude(float $probability): bool
    {
        return mt_rand() / mt_getrandmax() < $probability;
    }

    /**
     * Generate a random currency value between two bounds (inclusive).
     */
    private function randomCurrency(float $min, float $max): float
    {
        $minCents = (int) round($min * 100);
        $maxCents = (int) round($max * 100);

        if ($maxCents <= $minCents) {
            return round($minCents / 100, 2);
        }

        return round(random_int($minCents, $maxCents) / 100, 2);
    }

    /**
     * Utility to sum a given attribute for an array of categories.
     *
     * @param  array<int, array<string, float|string>>  $categories
     */
    private function sumAttribute(array $categories, string $attribute): float
    {
        $sum = 0.0;
        foreach ($categories as $category) {
            $sum += (float) $category[$attribute];
        }

        return round($sum, 2);
    }
}