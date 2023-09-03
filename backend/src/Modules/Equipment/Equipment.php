<?php

namespace Modules\Equipment;


use function Modules\Utils\database;

class Equipment
{
    public function __construct(
        public int    $id,
        public int    $count,
        public string $name,
        public int    $category_id,
        public string $category_name,
        public int    $location_id,
        public string $location_name,
        public int    $manufacturer_id,
        public string $manufacturer_name,
    )
    {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'count' => $this->count,
            'name' => $this->name,
            'category_id' => $this->category_id,
            'category_name' => $this->category_name,
            'location_id' => $this->location_id,
            'location_name' => $this->location_name,
            'manufacturer_id' => $this->manufacturer_id,
            'manufacturer_name' => $this->manufacturer_name,
        ];
    }

    public static function all(): array
    {
        $pdo = database();
        $stmt = $pdo->prepare('SELECT Equipment.id               as id,
       Equipment.name             as name,
       Equipment.count            as count,
       EquipmentCategory.name     as category,
       EquipmentCategory.id       as category_id,
       EquipmentLocation.name     as location,
       EquipmentLocation.id       as location_id,
       EquipmentManufacturer.name as manufacturer,
       EquipmentManufacturer.id   as manufacturer_id
FROM Equipment,
     EquipmentCategory,
     EquipmentLocation,
     EquipmentManufacturer
WHERE Equipment.category_id = EquipmentCategory.id
  AND Equipment.location_id = EquipmentLocation.id
  AND Equipment.manufacturer_id = EquipmentManufacturer.id');

        $result = [];
        $stmt->execute();
        while ($row = $stmt->fetch()) {
            $result[] = new Equipment(
                $row['id'],
                $row['count'],
                $row['name'],
                $row['category_id'],
                $row['category'],
                $row['location_id'],
                $row['location'],
                $row['manufacturer_id'],
                $row['manufacturer'],
            );
        }

        return $result;
    }
}