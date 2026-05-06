<?php
include("auth_guard.php");
include("../config/database.php");

$msg = $msg_type = '';
$q_id = intval($_GET['q'] ?? 0);

// Add answer
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['add_ans'])) {
    $q_id      = intval($_POST['question_id']);
    $ans_text  = trim(mysqli_real_escape_string($conn, $_POST['answer_text']));
    $is_correct= isset($_POST['is_correct']) ? 1 : 0;
    if ($q_id && $ans_text) {
        mysqli_query($conn,"INSERT INTO answers (question_id,answer_text,is_correct) VALUES ($q_id,'$ans_text',$is_correct)");
        $msg='Answer added!'; $msg_type='success';
    } else { $msg='Fill in answer text.'; $msg_type='error'; }
    $q_id = intval($_POST['question_id']);
}

// Delete answer
if (isset($_GET['delete_ans'])) {
    $aid = intval($_GET['delete_ans']);
    $row = mysqli_fetch_assoc(mysqli_query($conn,"SELECT question_id FROM answers WHERE id=$aid"));
    mysqli_query($conn,"DELETE FROM answers WHERE id=$aid");
    header("Location: answers.php?q=".($row['question_id']??0)); exit;
}

// Get question
$question = $q_id ? mysqli_fetch_assoc(mysqli_query($conn,
    "SELECT q.*,l.name lang_name,c.name cat_name FROM questions q
     JOIN languages l ON l.id=q.language_id
     JOIN categories c ON c.id=q.category_id
     WHERE q.id=$q_id")) : null;

// Get all questions for dropdown
$all_questions = mysqli_query($conn,
    "SELECT q.id,q.question,l.name lang FROM questions q JOIN languages l ON l.id=q.language_id ORDER BY l.name,q.id");

// Get answers for selected question
$answers = $q_id ? mysqli_query($conn,"SELECT * FROM answers WHERE question_id=$q_id ORDER BY id") : null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Admin – Answers</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
<?php include("sidebar.php"); ?>
<div class="content">
    <div class="topbar">
        <h5><i class="bi bi-check2-square me-2"></i>Answers</h5>
        <div class="user-chip"><div class="avatar"><?php echo strtoupper(substr($_SESSION['username'],0,1)); ?></div><?php echo htmlspecialchars($_SESSION['username']); ?></div>
    </div>
    <div class="page-body">

        <?php if($msg): ?>
        <div class="alert alert-<?php echo $msg_type==='success'?'success-pink':'pink'; ?> mb-3 py-2 px-3 small">
            <i class="bi bi-<?php echo $msg_type==='success'?'check-circle':'exclamation-circle'; ?> me-2"></i><?php echo $msg; ?>
        </div>
        <?php endif; ?>

        <!-- Question selector -->
        <div class="card-box mb-3">
            <div class="card-head"><h6><i class="bi bi-patch-question me-2"></i>Select Question</h6></div>
            <div class="card-body-p">
                <form method="GET" class="d-flex gap-2">
                    <select name="q" class="form-select" style="max-width:500px;" onchange="this.form.submit()">
                        <option value="0">— Choose a question —</option>
                        <?php while($aq=mysqli_fetch_assoc($all_questions)): ?>
                        <option value="<?php echo $aq['id']; ?>" <?php if($q_id==$aq['id']) echo 'selected'; ?>>
                            [<?php echo htmlspecialchars($aq['lang']); ?>] <?php echo htmlspecialchars(substr($aq['question'],0,80)).(strlen($aq['question'])>80?'…':''); ?>
                        </option>
                        <?php endwhile; ?>
                    </select>
                </form>
            </div>
        </div>

        <?php if ($question): ?>
        <!-- Question info -->
        <div class="card-box mb-3" style="background:var(--pink-50);">
            <div class="card-body-p">
                <div class="d-flex gap-2 align-items-start">
                    <i class="bi bi-patch-question-fill" style="color:var(--pink-400);font-size:1.3rem;margin-top:2px;"></i>
                    <div>
                        <div style="font-weight:600;color:var(--pink-700);"><?php echo htmlspecialchars($question['question']); ?></div>
                        <div class="mt-1">
                            <span class="badge-pill badge-feature me-1"><?php echo htmlspecialchars($question['lang_name']); ?></span>
                            <span class="badge-pill badge-diff"><?php echo htmlspecialchars($question['cat_name']); ?></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-3">
            <!-- Add Answer -->
            <div class="col-md-4">
                <div class="card-box">
                    <div class="card-head"><h6><i class="bi bi-plus-circle me-2"></i>Add Answer</h6></div>
                    <div class="card-body-p">
                        <form method="POST">
                            <input type="hidden" name="add_ans" value="1">
                            <input type="hidden" name="question_id" value="<?php echo $q_id; ?>">
                            <div class="mb-3">
                                <label class="form-label">Answer Text</label>
                                <textarea name="answer_text" class="form-control" rows="3" placeholder="Type answer option…" required></textarea>
                            </div>
                            <div class="mb-3 form-check">
                                <input type="checkbox" name="is_correct" id="is_correct" class="form-check-input" style="border-color:var(--pink-400);">
                                <label for="is_correct" class="form-check-label" style="color:var(--pink-700);font-weight:600;">Mark as Correct Answer</label>
                            </div>
                            <button class="btn-pink w-100">Add Answer</button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Answers list -->
            <div class="col-md-8">
                <div class="card-box">
                    <div class="card-head"><h6><i class="bi bi-list-check me-2"></i>Answer Options</h6></div>
                    <?php if ($answers && mysqli_num_rows($answers) > 0): ?>
                    <table class="table table-pink mb-0">
                        <thead><tr><th>#</th><th>Answer</th><th>Correct</th><th>Action</th></tr></thead>
                        <tbody>
                        <?php $i=1; while($a=mysqli_fetch_assoc($answers)): ?>
                        <tr>
                            <td><?php echo $i++; ?></td>
                            <td><?php echo htmlspecialchars($a['answer_text']); ?></td>
                            <td>
                                <?php if($a['is_correct']): ?>
                                <span class="badge-pill" style="background:#e8f5e9;color:#2e7d32;"><i class="bi bi-check2 me-1"></i>Correct</span>
                                <?php else: ?>
                                <span class="badge-pill" style="background:#f5f5f5;color:#999;">Wrong</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="?delete_ans=<?php echo $a['id']; ?>&q=<?php echo $q_id; ?>" class="btn btn-sm btn-outline-danger" style="border-radius:8px;" onclick="return confirm('Delete this answer?')"><i class="bi bi-trash"></i></a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <div class="card-body-p text-center" style="color:var(--text-muted);padding:40px;">
                        <i class="bi bi-inbox fs-2 d-block mb-2"></i>No answers yet. Add one on the left.
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php else: ?>
        <div class="card-box">
            <div class="card-body-p text-center" style="color:var(--text-muted);padding:60px;">
                <i class="bi bi-arrow-up-circle fs-1 d-block mb-2" style="color:var(--pink-200);"></i>
                Select a question above to manage its answers.
            </div>
        </div>
        <?php endif; ?>

    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
