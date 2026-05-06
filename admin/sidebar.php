<?php
// Count unread feedback for badge
$unread_fb = mysqli_fetch_assoc(mysqli_query($conn,"SELECT COUNT(*) as c FROM feedback WHERE is_read=0"))['c'];
$cur = basename($_SERVER['PHP_SELF']);
?>
<div class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon"><i class="bi bi-mortarboard-fill"></i></div>
        <h4>QuizSystem</h4>
        <small>Admin Panel</small>
    </div>
    <nav class="sidebar-nav">
        <div class="nav-label">Main</div>
        <a href="home.php"     class="<?php echo $cur==='home.php'?'active':''; ?>">
            <i class="bi bi-speedometer2"></i> Dashboard
        </a>
        <a href="users.php"    class="<?php echo $cur==='users.php'?'active':''; ?>">
            <i class="bi bi-people-fill"></i> Users
        </a>

        <div class="nav-label mt-2">Content</div>
        <a href="languages.php" class="<?php echo $cur==='languages.php'?'active':''; ?>">
            <i class="bi bi-translate"></i> Languages
        </a>
        <a href="categories.php" class="<?php echo $cur==='categories.php'?'active':''; ?>">
            <i class="bi bi-tag-fill"></i> Categories
        </a>
        <a href="questions.php" class="<?php echo $cur==='questions.php'?'active':''; ?>">
            <i class="bi bi-patch-question-fill"></i> Questions
        </a>
        <a href="answers.php"   class="<?php echo $cur==='answers.php'?'active':''; ?>">
            <i class="bi bi-check2-square"></i> Answers
        </a>

        <div class="nav-label mt-2">Community</div>
        <a href="feedback.php"  class="<?php echo $cur==='feedback.php'?'active':''; ?>">
            <i class="bi bi-chat-heart-fill"></i> Feedback
            <?php if ($unread_fb > 0): ?>
            <span class="badge-nav"><?php echo $unread_fb; ?></span>
            <?php endif; ?>
        </a>
    </nav>
    <div class="sidebar-footer">
        <a href="../logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
    </div>
</div>
