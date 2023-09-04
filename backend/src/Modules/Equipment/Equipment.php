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

    public static function fromId(int $id): ?Equipment
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
  AND Equipment.manufacturer_id = EquipmentManufacturer.id
  AND Equipment.id = ?
  ');
        $stmt->execute([$id]);
        $row = $stmt->fetch();
        if ($row === false) {
            return null;
        }

        return new Equipment(
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

    public static function create(string $name, int $count, int $category, int $location, int $manufacturer): void
    {
        $pdo = database();
        $stmt = $pdo->prepare('INSERT INTO Equipment (name, count, category_id, location_id, manufacturer_id) VALUES (:name, :count, :category, :location, :manufacturer)');
        $stmt->execute([
            'name' => $name,
            'count' => $count,
            'category' => $category,
            'location' => $location,
            'manufacturer' => $manufacturer,
        ]);
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

    public function delete(): void
    {
        $pdo = database();
        $stmt = $pdo->prepare('DELETE FROM Equipment WHERE id = :id');
        $stmt->execute(['id' => $this->id]);
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

    public function save(): void
    {
        $pdo = database();
        $stmt = $pdo->prepare('UPDATE Equipment SET name = :name, count = :count, category_id = :category_id, location_id = :location_id, manufacturer_id = :manufacturer_id WHERE id = :id');
        $stmt->execute([
            'id' => $this->id,
            'name' => $this->name,
            'count' => $this->count,
            'category_id' => $this->category_id,
            'location_id' => $this->location_id,
            'manufacturer_id' => $this->manufacturer_id,
        ]);
    }
}