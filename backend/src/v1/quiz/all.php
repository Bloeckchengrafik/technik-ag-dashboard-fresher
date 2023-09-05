<?php

use Modules\Login\Permission;
use Modules\Quiz\Quiz;
use function Modules\Utils\Api\init;
use function Modules\Utils\Api\map;
use function Modules\Utils\database;
use function Modules\Utils\Json\ok;

include_once "../../Modules/Autoload.php";

$user = init(Permission::DoQuiz);
$q = map(Quiz::all(), function ($quiz) {
    return $quiz->toArray();
});

$pdo = database();
$st = $pdo->prepare("SELECT quiz_id, overall_score FROM QuizAnswer WHERE user_id = ? and id IN (
    SELECT MAX(id) FROM QuizAnswer WHERE user_id = ? GROUP BY quiz_id
)");
$st->execute([$user->id, $user->id]);
$answers = $st->fetchAll(PDO::FETCH_ASSOC);

for ($i = 0; $i < count($q); $i++) {
    $q[$i]["done"] = false;
    $q[$i]["score"] = 0;
    for ($j = 0; $j < count($answers); $j++) {
        if ($q[$i]["id"] == $answers[$j]["quiz_id"]) {
            $q[$i]["done"] = true;
            $q[$i]["score"] = $answers[$j]["overall_score"];
            break;
        }
    }
}

ok($q);