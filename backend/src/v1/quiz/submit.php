<?php

use Modules\Login\Permission;
use Modules\Quiz\Quiz;
use function Modules\Utils\Api\init;
use function Modules\Utils\database;
use function Modules\Utils\Json\error;
use function Modules\Utils\Json\ok;

include_once "../../Modules/Autoload.php";

$user = init(Permission::DoQuiz);

$body = json_decode(file_get_contents('php://input'));
$quiz = Quiz::byId(intval($body->quiz_id));
if ($quiz == null) {
    error("Quiz not found");
}

$overall_score = 0;
for ($i = 0; $i < count($body->answers); $i++) {
    $answer = $body->answers[$i]->answer;
    $overall_score += $answer;
}

$overall_score = $overall_score / count($body->answers);
$overall_score = round($overall_score);
$overall_score = max(-2, $overall_score);
$overall_score = min(2, $overall_score);

$db = database();
// First, create a new submission
$stmt = $db->prepare("INSERT INTO QuizAnswer (quiz_id, user_id, overall_score) VALUES (?, ?, ?)");
$stmt->execute([$quiz->id, $user->id, $overall_score]);
$submission_id = $db->lastInsertId();

// Then, insert all the answers
for ($i = 0; $i < count($body->answers); $i++) {
    $answer = $body->answers[$i]->answer;
    $question_id = $body->answers[$i]->question_id;
    $stmt = $db->prepare("INSERT INTO QuizQuestionAnswer (quiz_answer_id, quiz_question_id, answer_score) VALUES (?, ?, ?)");
    $stmt->execute([$submission_id, $question_id, $answer]);
}

ok([]);
