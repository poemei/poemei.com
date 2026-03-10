<nav class="sw-nav">
  <div class="sw-nav-list">
    <a href="/">Home</a>
    <a href="/about">About Me</a>
    <a href="/posts">Posts</a>
    <a href="/projects">Projects</a>
    <a href="/resume">Resume</a>
            
    <?php if (isset($_SESSION['user_id'])): ?>
    <?php if (isset($_SESSION['user_level']) && $_SESSION['user_level'] >= 9): ?>
    <a href="/admin" class="btn btn-sm btn-outline-primary">Admin</a>
    <?php endif; ?>
                
    <a href="/logout" style="color: #666; text-decoration: none;">Logout</a>
    <?php else: ?>
    <a href="/login">Login</a>
    <a href="/signup">Register</a>
    <?php endif; ?>
  </div>
</nav>
