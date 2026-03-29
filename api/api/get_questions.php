<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
include("../config/database.php");

$lang = $_GET['language'] ?? '';
$diff = $_GET['difficulty'] ?? '';

$sql = "SELECT q.*, l.language_name FROM questions q JOIN languages l ON q.language_id = l.language_id WHERE 1=1";
if($lang) $sql .= " AND l.language_name = '$lang'";
if($diff) $sql .= " AND q.difficulty = '$diff'";

$res = mysqli_query($conn, $sql);
$questions = [];

while($q = mysqli_fetch_assoc($res)) {
    $q_id = $q['question_id'];
    $ans_res = mysqli_query($conn, "SELECT * FROM answers WHERE question_id = $q_id");
    $answers = [];
    while($a = mysqli_fetch_assoc($ans_res)) {
        $answers[] = ['text' => $a['answer_text'], 'correct' => (bool)$a['is_correct']];
    }
    $questions[] = [
        'question' => $q['question_text'],
        'difficulty' => $q['difficulty'],
        'answers' => $answers
    ];
}
echo json_encode(['success' => true, 'questions' => $questions]);
?>