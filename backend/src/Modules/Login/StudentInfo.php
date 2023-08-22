<?php

namespace Modules\Login;

use function Modules\Utils\database;

class StudentInfo
{
    public int $user_id;
    public int $year;
    public string $tutor;
    public string $last_updated;

    public function __construct(int $user_id, int $year, string $tutor, string $last_updated)
    {
        $this->user_id = $user_id;
        $this->year = $year;
        $this->tutor = $tutor;
        $this->last_updated = $last_updated;
    }

    public function needsUpdate(): bool
    {
        // Checks if the information is older than 1 school year (starting on 1st of August)
        $lastUpdated = strtotime($this->last_updated);
        $currentYear = date('Y');
        $currentMonth = date('m');
        $currentDay = date('d');
        $currentYear = $currentMonth >= 8 ? $currentYear : $currentYear - 1;
        $lastUpdatedYear = date('Y', $lastUpdated);
        $lastUpdatedMonth = date('m', $lastUpdated);
        $lastUpdatedDay = date('d', $lastUpdated);
        $lastUpdatedYear = $lastUpdatedMonth >= 8 ? $lastUpdatedYear : $lastUpdatedYear - 1;
        return $lastUpdatedYear < $currentYear || ($lastUpdatedYear === $currentYear && $lastUpdatedMonth < $currentMonth) || ($lastUpdatedYear === $currentYear && $lastUpdatedMonth === $currentMonth && $lastUpdatedDay < $currentDay);
    }

    public function save(): void
    {
        $db = database();
        $stmt = $db->prepare('UPDATE StudentInformation SET year = ?, tutor = ?, last_updated = ? WHERE user_id = ?');
        $stmt->bindValue('i', $this->year);
        $stmt->bindValue('s', $this->tutor);
        $stmt->bindValue('s', $this->last_updated);
        $stmt->bindValue('i', $this->user_id);
        $stmt->execute();
    }

    public static function create(User $user, int $year, string $tutor): StudentInfo
    {
        $db = database();
        $stmt = $db->prepare('INSERT INTO StudentInformation (user_id, year, tutor) VALUES (?, ?, ?)');
        $stmt->execute([$user->id, $year, $tutor]);
        return new StudentInfo($user->id, $year, $tutor, date('Y-m-d H:i:s'));
    }

    public static function fromUser(User $user): ?StudentInfo
    {
        $db = database();
        $stmt = $db->prepare('SELECT * FROM StudentInformation WHERE user_id = ?');
        $stmt->bindValue('i', $user->id);
        $stmt->execute();
        $rows = $stmt->rowCount();
        if ($rows === 0) {
            return null;
        }
        $row = $stmt->fetch();
        return new StudentInfo($row['user_id'], $row['year'], $row['tutor'], $row['last_updated']);
    }
}