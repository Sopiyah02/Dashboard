<?php $cur = basename($_SERVER['PHP_SELF']); ?>
<div class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon"><i class="bi bi-mortarboard-fill"></i></div>
        <h4>QuizSystem</h4>
        <small>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</small>
    </div>
    <nav class="sidebar-nav">
        <div class="nav-label">Menu</div>
        <a href="home.php"     class="<?php echo $cur==='home.php'?'active':''; ?>">
            <i class="bi bi-house-fill"></i> Dashboard
        </a>
        <a href="quiz.php"     class="<?php echo $cur==='quiz.php'?'active':''; ?>">
            <i class="bi bi-patch-question-fill"></i> Take a Quiz
        </a>
        <a href="profile.php"  class="<?php echo $cur==='profile.php'?'active':''; ?>">
            <i class="bi bi-person-fill"></i> My Profile
        </a>
        <div class="nav-label mt-2">Support</div>
        <a href="feedback.php" class="<?php echo $cur==='feedback.php'?'active':''; ?>">
            <i class="bi bi-chat-heart-fill"></i> Send Feedback
        </a>
    </nav>
    <div class="sidebar-footer">
        <a href="../logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
    </div>
</div>
