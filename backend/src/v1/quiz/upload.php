<?php

use Modules\Login\Permission;
use Shuchkin\SimpleXLSX;
use function Modules\Utils\Api\init;
use function Modules\Utils\database;
use function Modules\Utils\Json\ok;

include_once "../../Modules/Autoload.php";
init(Permission::UploadQuiz);

$xlsx_data = file_get_contents($_FILES['file']['tmp_name']);
$data = SimpleXLSX::parseData($xlsx_data);
$quiz = $data->rows();

error_log(json_encode($quiz));

$db = database();
$db->exec("DELETE FROM QuizQuestionAnswer WHERE 1");
$db->exec("DELETE FROM QuizAnswer WHERE 1");
$db->exec("DELETE FROM QuizQuestion WHERE 1");
$db->exec("DELETE FROM Quiz WHERE 1");
$db->exec("DELETE FROM QuizCategory WHERE 1");

#Re-Sync
#quiz data format [["category","quiz","question"],...]

$category_names = [];
$category_dict = [];
$quiz_names = [];
$quiz_dict = [];
$quiz_category_dict = [];

foreach ($quiz as $row) {
    $category_name = $row[0];
    $quiz_name = $row[1];

    if (!in_array($category_name, $category_names)) {
        $category_names[] = $category_name;
    }

    if (!in_array($quiz_name, $quiz_names)) {
        $quiz_names[] = $quiz_name;
    }

    $quiz_category_dict[$quiz_name] = $category_name;
}

$category_names = array_unique($category_names);
$quiz_names = array_unique($quiz_names);

foreach ($category_names as $category_name) {
    $stmt = $db->prepare("INSERT INTO QuizCategory (name) VALUES (?)");
    $stmt->execute([$category_name]);
    $category_dict[$category_name] = $db->lastInsertId();
}

foreach ($quiz_names as $quiz_name) {
    $stmt = $db->prepare("INSERT INTO Quiz (category_id, name) VALUES (?, ?)");
    $stmt->execute([$category_dict[$quiz_category_dict[$quiz_name]], $quiz_name]);
    $quiz_dict[$quiz_name] = $db->lastInsertId();
}

foreach ($quiz as $row) {
    $quiz_name = $row[1];
    $question = $row[2];

    $db->prepare("INSERT INTO QuizQuestion (quiz_id, question) VALUES (?, ?)")->execute([$quiz_dict[$quiz_name], $question]);
}

ok([]);