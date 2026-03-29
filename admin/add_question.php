<?php
include("../config/database.php");

$message = "";
$messageType = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $language_id = $_POST['language_id'];
    $question_text = $_POST['question_text'];
    $difficulty = $_POST['difficulty'];
    $answers = [
        ['text' => $_POST['answer_1'], 'is_correct' => ($_POST['correct_answer'] == '1' ? 1 : 0)],
        ['text' => $_POST['answer_2'], 'is_correct' => ($_POST['correct_answer'] == '2' ? 1 : 0)],
        ['text' => $_POST['answer_3'], 'is_correct' => ($_POST['correct_answer'] == '3' ? 1 : 0)],
        ['text' => $_POST['answer_4'], 'is_correct' => ($_POST['correct_answer'] == '4' ? 1 : 0)]
    ];

    $sql = "INSERT INTO questions (language_id, question_text, difficulty) VALUES ('$language_id', '$question_text', '$difficulty')";
    if (mysqli_query($conn, $sql)) {
        $q_id = mysqli_insert_id($conn);
        foreach ($answers as $a) {
            mysqli_query($conn, "INSERT INTO answers (question_id, answer_text, is_correct) VALUES ('$q_id', '{$a['text']}', {$a['is_correct']})");
        }
        $message = "Question added successfully!";
        $messageType = "success";
    }
}
$languages = mysqli_query($conn, "SELECT * FROM languages");
?>
<!DOCTYPE html>
<html>
<head>
    <title>Add Question</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h2>Add New Question</h2>
    <?php if($message ): ?> <div class="alert alert-<?php echo $messageType; ?>"><?php echo $message; ?></div> <?php endif; ?>
    <form method="POST">
        <div class="mb-3">
            <label>Language</label>
            <select name="language_id" class="form-control" required>
                <?php while($l = mysqli_fetch_assoc($languages)): ?>
                    <option value="<?php echo $l['language_id']; ?>"><?php echo $l['language_name']; ?></option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="mb-3">
            <label>Difficulty</label>
            <select name="difficulty" class="form-control">
                <option value="beginner">Beginner</option>
                <option value="normal">Normal</option>
                <option value="expert">Expert</option>
            </select>
        </div>
        <div class="mb-3">
            <label>Question</label>
            <textarea name="question_text" class="form-control" required></textarea>
        </div>
        <?php for($i=1; $i<=4; $i++): ?>
            <div class="mb-2">
                <input type="text" name="answer_<?php echo $i; ?>" placeholder="Answer <?php echo $i; ?>" class="form-control" required>
                <input type="radio" name="correct_answer" value="<?php echo $i; ?>" required> Correct
            </div>
        <?php endfor; ?>
        <button type="submit" class="btn btn-primary">Save Question</button>
        <a href="index.php" class="btn btn-secondary">Back</a>
    </form>
</body>
</html>