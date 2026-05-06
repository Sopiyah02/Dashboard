<?php
include("auth_guard.php");
include("../config/database.php");

$msg = $msg_type = '';

// Add question
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['add_q'])) {
    $lang_id = intval($_POST['language_id']);
    $cat_id  = intval($_POST['category_id']);
    $q_text  = trim(mysqli_real_escape_string($conn, $_POST['question']));
    if ($lang_id && $cat_id && $q_text) {
        mysqli_query($conn,"INSERT INTO questions (language_id,category_id,question) VALUES ($lang_id,$cat_id,'$q_text')");
        $msg='Question added!'; $msg_type='success';
    } else { $msg='All fields required.'; $msg_type='error'; }
}

// Delete question
if (isset($_GET['delete'])) {
    mysqli_query($conn,"DELETE FROM questions WHERE id=".intval($_GET['delete']));
    header("Location: questions.php"); exit;
}

// Filters
$filter_lang = intval($_GET['lang'] ?? 0);
$filter_cat  = intval($_GET['cat']  ?? 0);
$where_parts = [];
if ($filter_lang) $where_parts[] = "q.language_id=$filter_lang";
if ($filter_cat)  $where_parts[] = "q.category_id=$filter_cat";
$where = $where_parts ? "WHERE ".implode(" AND ",$where_parts) : "";

$questions = mysqli_query($conn,
    "SELECT q.*,l.name lang_name,c.name cat_name FROM questions q
     JOIN languages l ON l.id=q.language_id
     JOIN categories c ON c.id=q.category_id
     $where ORDER BY q.created_at DESC"
);

$languages  = mysqli_query($conn,"SELECT * FROM languages ORDER BY name");
$categories = mysqli_query($conn,"SELECT * FROM categories ORDER BY name");
// Re-fetch for dropdowns
$lang_opts = mysqli_query($conn,"SELECT * FROM languages ORDER BY name");
$cat_opts  = mysqli_query($conn,"SELECT * FROM categories ORDER BY name");
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>Admin – Questions</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
<link href="../assets/css/style.css" rel="stylesheet">
</head>
<body>
<?php include("sidebar.php"); ?>
<div class="content">
    <div class="topbar">
        <h5><i class="bi bi-patch-question-fill me-2"></i>Questions</h5>
        <div class="user-chip"><div class="avatar"><?php echo strtoupper(substr($_SESSION['username'],0,1)); ?></div><?php echo htmlspecialchars($_SESSION['username']); ?></div>
    </div>
    <div class="page-body">

        <?php if($msg): ?>
        <div class="alert alert-<?php echo $msg_type==='success'?'success-pink':'pink'; ?> mb-3 py-2 px-3 small">
            <i class="bi bi-<?php echo $msg_type==='success'?'check-circle':'exclamation-circle'; ?> me-2"></i><?php echo $msg; ?>
        </div>
        <?php endif; ?>

        <!-- Add Question -->
        <div class="card-box mb-3">
            <div class="card-head"><h6><i class="bi bi-plus-circle me-2"></i>Add New Question</h6></div>
            <div class="card-body-p">
                <form method="POST">
                    <input type="hidden" name="add_q" value="1">
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Language</label>
                            <select name="language_id" class="form-select" required>
                                <option value="">Select language…</option>
                                <?php while($l=mysqli_fetch_assoc($lang_opts)): ?>
                                <option value="<?php echo $l['id']; ?>"><?php echo htmlspecialchars($l['name']); ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Category / Difficulty</label>
                            <select name="category_id" class="form-select" required>
                                <option value="">Select category…</option>
                                <?php while($c=mysqli_fetch_assoc($cat_opts)): ?>
                                <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button class="btn-pink w-100">Add Question</button>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Question Text</label>
                            <textarea name="question" class="form-control" rows="2" placeholder="Type the question here…" required></textarea>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Filter & List -->
        <div class="card-box">
            <div class="card-head">
                <h6><i class="bi bi-list me-2"></i>All Questions</h6>
                <form method="GET" class="d-flex gap-2" style="margin:0;">
                    <select name="lang" class="form-select form-select-sm" style="width:140px;">
                        <option value="0">All Languages</option>
                        <?php while($l=mysqli_fetch_assoc($languages)): ?>
                        <option value="<?php echo $l['id']; ?>" <?php if($filter_lang==$l['id']) echo 'selected'; ?>><?php echo htmlspecialchars($l['name']); ?></option>
                        <?php endwhile; ?>
                    </select>
                    <select name="cat" class="form-select form-select-sm" style="width:140px;">
                        <option value="0">All Categories</option>
                        <?php while($c=mysqli_fetch_assoc($categories)): ?>
                        <option value="<?php echo $c['id']; ?>" <?php if($filter_cat==$c['id']) echo 'selected'; ?>><?php echo htmlspecialchars($c['name']); ?></option>
                        <?php endwhile; ?>
                    </select>
                    <button class="btn-pink" style="padding:6px 14px;font-size:.82rem;"><i class="bi bi-filter"></i></button>
                    <a href="questions.php" class="btn-outline-pink" style="padding:6px 14px;font-size:.82rem;text-decoration:none;border-radius:10px;">Reset</a>
                </form>
            </div>
            <div class="table-responsive">
            <table class="table table-pink mb-0">
                <thead><tr><th>#</th><th>Question</th><th>Language</th><th>Difficulty</th><th>Added</th><th>Actions</th></tr></thead>
                <tbody>
                <?php $i=1; while($q=mysqli_fetch_assoc($questions)):
                    $diff = strtolower($q['cat_name'])==='easy'?'easy':(strtolower($q['cat_name'])==='hard'?'hard':'diff');
                ?>
                <tr>
                    <td style="color:var(--text-muted);"><?php echo $i++; ?></td>
                    <td style="max-width:320px;"><?php echo htmlspecialchars($q['question']); ?></td>
                    <td><span class="badge-pill badge-feature"><?php echo htmlspecialchars($q['lang_name']); ?></span></td>
                    <td><span class="badge-pill badge-<?php echo $diff; ?>"><?php echo htmlspecialchars($q['cat_name']); ?></span></td>
                    <td style="font-size:.78rem;color:var(--text-muted);"><?php echo date('M j, Y',strtotime($q['created_at'])); ?></td>
                    <td>
                        <a href="answers.php?q=<?php echo $q['id']; ?>" class="btn btn-sm btn-outline-secondary me-1" style="border-radius:8px;font-size:.78rem;">
                            <i class="bi bi-check2-square me-1"></i>Answers
                        </a>
                        <a href="?delete=<?php echo $q['id']; ?>" class="btn btn-sm btn-outline-danger" style="border-radius:8px;" onclick="return confirm('Delete this question and all its answers?')">
                            <i class="bi bi-trash"></i>
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
            </div>
        </div>

    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
