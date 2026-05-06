<?php
include("auth_guard.php");
include("../config/database.php");

$filter_lang = intval($_GET['lang'] ?? 0);
$filter_cat  = intval($_GET['cat']  ?? 0);
$q_id        = intval($_GET['q']    ?? 0);
$result_msg  = '';

// Submit answer
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['answer_id'])) {
    $ans_id = intval($_POST['answer_id']);
    $ans = mysqli_fetch_assoc(mysqli_query($conn,"SELECT * FROM answers WHERE id=$ans_id"));
    $result_msg = $ans['is_correct'] ? 'correct' : 'wrong';
    $correct_ans = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT answer_text FROM answers WHERE question_id={$ans['question_id']} AND is_correct=1 LIMIT 1"
    ));
}

$languages  = mysqli_query($conn,"SELECT * FROM languages ORDER BY name");
$categories = mysqli_query($conn,"SELECT * FROM categories ORDER BY name");

// Re-fetch for filters
$lang_opts = mysqli_query($conn,"SELECT * FROM languages ORDER BY name");
$cat_opts  = mysqli_query($conn,"SELECT * FROM categories ORDER BY name");

// Get question
$where_q = [];
if ($filter_lang) $where_q[] = "q.language_id=$filter_lang";
if ($filter_cat)  $where_q[] = "q.category_id=$filter_cat";
$wq = $where_q ? "AND ".implode(" AND ",$where_q) : "";

if ($q_id) {
    $question = mysqli_fetch_assoc(mysqli_query($conn,
        "SELECT q.*,l.name lang_name,c.name cat_name FROM questions q
         JOIN languages l ON l.id=q.language_id
         JOIN categories c ON c.id=q.category_id
         WHERE q.id=$q_id"
    ));
    if ($question) {
        $answers = mysqli_query($conn,"SELECT * FROM answers WHERE question_id=$q_id ORDER BY RAND()");
    }
}

// Question list
$q_list = mysqli_query($conn,
    "SELECT q.*,l.name lang_name,c.name cat_name FROM questions q
     JOIN languages l ON l.id=q.language_id
     JOIN categories c ON c.id=q.category_id
     WHERE 1 $wq ORDER BY RAND() LIMIT 20"
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Quiz – QuizSystem</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<link href="../assets/css/style.css" rel="stylesheet">
<style>
.answer-option {
    background: var(--pink-50);
    border: 1.5px solid var(--border);
    border-radius: 12px;
    padding: 14px 18px;
    cursor: pointer;
    transition: all .2s;
    display: flex; align-items: center; gap: 12px;
    margin-bottom: 10px;
}
.answer-option:hover { border-color: var(--pink-400); background: #fff0f8; }
.answer-option input { accent-color: var(--pink-500); width:18px; height:18px; }
.q-item {
    padding: 12px 16px;
    border-bottom: 1px solid var(--border);
    cursor: pointer;
    transition: background .15s;
    font-size: .88rem;
}
.q-item:hover { background: var(--pink-50); }
.q-item.active { background: var(--pink-100); }
</style>
</head>
<body>
<?php include("sidebar.php"); ?>
<div class="content">
    <div class="topbar">
        <h5><i class="bi bi-patch-question-fill me-2"></i>Take a Quiz</h5>
        <div class="user-chip"><div class="avatar"><?php echo strtoupper(substr($_SESSION['username'],0,1)); ?></div><?php echo htmlspecialchars($_SESSION['username']); ?></div>
    </div>
    <div class="page-body">

        <!-- Filter bar -->
        <div class="card-box mb-3">
            <div class="card-body-p" style="padding:14px 20px;">
                <form method="GET" class="d-flex gap-2 flex-wrap align-items-center">
                    <span style="font-size:.85rem;font-weight:600;color:var(--pink-700);">Filter:</span>
                    <select name="lang" class="form-select form-select-sm" style="width:150px;" onchange="this.form.submit()">
                        <option value="0">All Languages</option>
                        <?php while($l=mysqli_fetch_assoc($lang_opts)): ?>
                        <option value="<?php echo $l['id']; ?>" <?php if($filter_lang==$l['id']) echo 'selected'; ?>><?php echo htmlspecialchars($l['name']); ?></option>
                        <?php endwhile; ?>
                    </select>
                    <select name="cat" class="form-select form-select-sm" style="width:150px;" onchange="this.form.submit()">
                        <option value="0">All Difficulties</option>
                        <?php while($c=mysqli_fetch_assoc($cat_opts)): ?>
                        <option value="<?php echo $c['id']; ?>" <?php if($filter_cat==$c['id']) echo 'selected'; ?>><?php echo htmlspecialchars($c['name']); ?></option>
                        <?php endwhile; ?>
                    </select>
                    <a href="quiz.php" class="btn-outline-pink" style="font-size:.82rem;padding:6px 14px;text-decoration:none;border-radius:10px;">Reset</a>
                </form>
            </div>
        </div>

        <div class="row g-3">
            <!-- Question list -->
            <div class="col-md-4">
                <div class="card-box">
                    <div class="card-head"><h6><i class="bi bi-list me-2"></i>Questions</h6></div>
                    <?php
                    $qrows=[];
                    while($qr=mysqli_fetch_assoc($q_list)) $qrows[]=$qr;
                    if(empty($qrows)): ?>
                    <div style="padding:30px;text-align:center;color:var(--text-muted);font-size:.85rem;">No questions found.</div>
                    <?php else: foreach($qrows as $qr):
                        $diff=strtolower($qr['cat_name'])==='easy'?'easy':(strtolower($qr['cat_name'])==='hard'?'hard':'diff');
                    ?>
                    <div class="q-item <?php echo $q_id==$qr['id']?'active':''; ?>"
                         onclick="location.href='quiz.php?q=<?php echo $qr['id']; ?>&lang=<?php echo $filter_lang; ?>&cat=<?php echo $filter_cat; ?>'">
                        <div class="d-flex gap-2 align-items-start">
                            <span class="badge-pill badge-<?php echo $diff; ?>" style="font-size:.65rem;white-space:nowrap;margin-top:2px;"><?php echo htmlspecialchars($qr['cat_name']); ?></span>
                            <div>
                                <div style="font-size:.82rem;font-weight:500;line-height:1.4;"><?php echo htmlspecialchars(substr($qr['question'],0,60)).(strlen($qr['question'])>60?'…':''); ?></div>
                                <div style="font-size:.72rem;color:var(--text-muted);margin-top:2px;"><?php echo htmlspecialchars($qr['lang_name']); ?></div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; endif; ?>
                </div>
            </div>

            <!-- Question panel -->
            <div class="col-md-8">
                <?php if ($q_id && isset($question) && $question): ?>

                    <!-- Result feedback -->
                    <?php if ($result_msg === 'correct'): ?>
                    <div class="alert alert-success-pink mb-3">
                        <i class="bi bi-check-circle-fill me-2"></i><strong>Correct!</strong> Great job! 🎉
                    </div>
                    <?php elseif ($result_msg === 'wrong'): ?>
                    <div class="alert alert-pink mb-3">
                        <i class="bi bi-x-circle-fill me-2"></i><strong>Incorrect.</strong>
                        The correct answer is: <strong><?php echo htmlspecialchars($correct_ans['answer_text'] ?? ''); ?></strong>
                    </div>
                    <?php endif; ?>

                    <div class="card-box">
                        <div class="card-head">
                            <div class="d-flex gap-2">
                                <span class="badge-pill badge-feature"><?php echo htmlspecialchars($question['lang_name']); ?></span>
                                <span class="badge-pill badge-<?php echo strtolower($question['cat_name'])==='easy'?'easy':(strtolower($question['cat_name'])==='hard'?'hard':'diff'); ?>">
                                    <?php echo htmlspecialchars($question['cat_name']); ?>
                                </span>
                            </div>
                        </div>
                        <div class="card-body-p">
                            <h5 style="color:var(--pink-700);margin-bottom:24px;line-height:1.5;">
                                <i class="bi bi-patch-question me-2" style="color:var(--pink-400);"></i>
                                <?php echo htmlspecialchars($question['question']); ?>
                            </h5>

                            <?php if(!$result_msg): ?>
                            <form method="POST">
                                <input type="hidden" name="q_id" value="<?php echo $q_id; ?>">
                                <?php while($a=mysqli_fetch_assoc($answers)): ?>
                                <label class="answer-option" for="ans<?php echo $a['id']; ?>">
                                    <input type="radio" name="answer_id" id="ans<?php echo $a['id']; ?>" value="<?php echo $a['id']; ?>" required>
                                    <span><?php echo htmlspecialchars($a['answer_text']); ?></span>
                                </label>
                                <?php endwhile; ?>
                                <button type="submit" class="btn-pink mt-2 w-100 py-2">
                                    <i class="bi bi-send me-1"></i> Submit Answer
                                </button>
                            </form>
                            <?php else: ?>
                            <!-- Next question button -->
                            <?php
                            $next = mysqli_fetch_assoc(mysqli_query($conn,
                                "SELECT q.id FROM questions q WHERE q.id!=$q_id $wq ORDER BY RAND() LIMIT 1"
                            ));
                            ?>
                            <a href="quiz.php?q=<?php echo $next['id']??''; ?>&lang=<?php echo $filter_lang; ?>&cat=<?php echo $filter_cat; ?>"
                               class="btn-pink d-block text-center text-decoration-none py-2 mt-2">
                                <i class="bi bi-arrow-right me-1"></i> Next Question
                            </a>
                            <?php endif; ?>
                        </div>
                    </div>

                <?php else: ?>
                <div class="card-box">
                    <div class="card-body-p text-center" style="padding:80px;color:var(--text-muted);">
                        <i class="bi bi-arrow-left-circle fs-1 d-block mb-3" style="color:var(--pink-200);"></i>
                        <h5 style="color:var(--pink-700);">Pick a question to start</h5>
                        <p style="font-size:.88rem;">Select any question from the list on the left.</p>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
