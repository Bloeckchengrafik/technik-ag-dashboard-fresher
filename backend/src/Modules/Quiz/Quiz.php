<?php

namespace Modules\Quiz;

use PDO;
use function Modules\Utils\database;

class Quiz
{
    function __construct(
        public int    $id,
        public int    $category_id,
        public string $name,
        public string $category_name,
    )
    {
    }

    public static function byId(int $id): ?Quiz
    {
        $pdo = database();
        $stmt = $pdo->prepare('SELECT q.id, q.name, q.category_id, c.name as category_name FROM Quiz q JOIN QuizCategory c ON q.category_id = c.id WHERE q.id = :id');
        $stmt->execute(['id' => $id]);
        $quiz = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$quiz) {
            return null;
        }

        return new Quiz(
            id: $quiz['id'],
            category_id: $quiz['category_id'],
            name: $quiz['name'],
            category_name: $quiz['category_name'],
        );
    }

    public function toArray(): array
    {
        return [
            'id'            => $this->id,
            'category_id'   => $this->category_id,
            'name'          => $this->name,
            'category_name' => $this->category_name,
        ];
    }

    public static function all(): array
    {
        $pdo = database();
        $stmt = $pdo->prepare('SELECT q.id, q.name, q.category_id, c.name as category_name FROM Quiz q JOIN QuizCategory c ON q.category_id = c.id');
        $stmt->execute();
        $quizzes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        return array_map(function ($quiz) {
            return new Quiz(
                id: $quiz['id'],
                category_id: $quiz['category_id'],
                name: $quiz['name'],
                category_name: $quiz['category_name'],
            );
        }, $quizzes);
    }
}