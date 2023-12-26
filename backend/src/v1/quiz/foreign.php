<?php

use Modules\Login\Permission;
use Modules\Login\User;
use function Modules\Utils\Api\init;
use function Modules\Utils\database;
use function Modules\Utils\Json\error;
use function Modules\Utils\Json\ok;

include_once "../../Modules/Autoload.php";

init(Permission::UserAdmin);

$user_id = intval($_GET['id']);
$user = User::fromId($user_id);
if ($user == null) {
    error("User not found");
}

$db = database();

$quiz = $db->prepare("
SELECT Quiz.id                  AS quiz_id,
       Quiz.name                AS quiz_name,
       QuizCategory.name        AS category_name,
       QuizAnswer.overall_score AS score,
       QuizAnswer.id            AS qid
FROM Quiz,
     QuizAnswer,
     QuizCategory
WHERE Quiz.id = QuizAnswer.quiz_id
  AND Quiz.category_id = QuizCategory.id
  AND QuizAnswer.id IN (SELECT MAX(id)
                        FROM QuizAnswer
                        WHERE user_id = ?
                        GROUP BY quiz_id)
  ");

$quiz->execute([$user_id]);
$quiz = $quiz->fetchAll(PDO::FETCH_ASSOC);

for($i = 0; $i < count($quiz); $i++) {
  $datum = $db->prepare("SELECT * FROM QuizQuestionAnswer, QuizQuestion WHERE quiz_answer_id = ? AND QuizQuestion.id = QuizQuestionAnswer.quiz_question_id");

  $datum->execute([$quiz[$i]["qid"]]);
  $datum = $datum->fetchAll(PDO::FETCH_ASSOC);
  $quiz[$i]["dataset"] = $datum;
}

ok($quiz);
