<nav>
  <?php
  echo "Logged in as $user->username";
  ?>
  <br>
  <a href="index.php?action=profile">profile</a>
  <a href="index.php?action=post">post</a>
  <a href="index.php?action=search">search</a>
  <a href="index.php?action=logout">logout</a>
  <?php
    if ($user->role == 'moderator' || $user->role == 'admin') {
      echo '<a href="index.php?action=activity">activity</a>';
    }
    if ($user->role == 'admin') {
      echo '<a href="index.php?action=report">report</a>';
    }
  ?>
</nav>
