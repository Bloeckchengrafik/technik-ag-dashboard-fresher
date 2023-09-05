<?php

use Modules\Login\Permission;
use Modules\Quiz\Quiz;
use function Modules\Utils\Api\init;
use function Modules\Utils\database;
use function Modules\Utils\Json\error;
use function Modules\Utils\Json\ok;

include_once "../../Modules/Autoload.php";

init(Permission::DoQuiz);

$id = intval($_GET['id']);
$quiz = Quiz::byId($id);
if (!$quiz) {
    error("Quiz not found");
}

$pdo = database();
$stmt = $pdo->prepare("SELECT * FROM QuizQuestion WHERE quiz_id = ?");
$stmt->execute([$id]);
$questions = $stmt->fetchAll(PDO::FETCH_ASSOC);

ok([
    "quiz" => $quiz->toArray(),
    "questions" => $questions
]);