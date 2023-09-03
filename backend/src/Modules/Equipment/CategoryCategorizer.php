<?php

namespace Modules\Equipment;


class CategoryCategorizer implements EquipmentCategorizer
{

    public function categorize(array $equipment_data): array
    {
        $categories = [];
        foreach ($equipment_data as $equipment) {
            /** @var Equipment $equipment */
            $category = $equipment->category_name;
            if (!isset($categories[$category])) {
                $categories[$category] = [];
            }

            $categories[$category][] = $equipment;
        }

        return $categories;
    }

    public function finalize(array $equipment_data): array
    {
        return $equipment_data;
    }
}