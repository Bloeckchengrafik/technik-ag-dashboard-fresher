<?php

namespace Modules\Equipment;

interface EquipmentCategorizer
{
    public function categorize(array $equipment_data): array;
    public function finalize(array $equipment_data): array;
}