<?php

use Modules\Login\Permission;
use Shuchkin\SimpleXLSXGen;
use function Modules\Utils\Api\init;
use function Modules\Utils\database;

include_once "../../Modules/Autoload.php";
$user = init(Permission::UploadQuiz);

$db = database();
$data = $db->query(<<<SQL
    SELECT 
        QuizCategory.name AS category,
        Quiz.name AS quiz,
        QuizQuestion.question
    FROM QuizQuestion 
        LEFT JOIN Quiz 
            ON QuizQuestion.quiz_id = Quiz.id 
        LEFT JOIN QuizCategory 
            ON Quiz.category_id = QuizCategory.id
SQL
)->fetchAll(PDO::FETCH_ASSOC);

$array = [
    ["Category", "Quiz", "Question"]
];

foreach ($data as $datum) {
    $array[] = [
        $datum["category"],
        $datum["quiz"],
        $datum["question"]
    ];
}

error_log(json_encode($array));

$xlsx = SimpleXLSXGen::fromArray($data);
$xlsx->download();
