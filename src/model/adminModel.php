<?php
class AdminModel extends ModeratorModel {
  

  function blogpostCountByUser($conn) {
    $query = "SELECT user_id, COUNT(*) AS post_count FROM blog_posts GROUP BY user_id";
    $results = $conn->query($query);
    return $results;
  }

  function userCountByRole($conn) {
    $query = "SELECT role, COUNT(*) AS user_count FROM users GROUP BY role";
    $results = $conn->query($query);
    return $results;
  }

  function makeMod($userid, $conn) {
    $userid = intval($userid);
    if ($userid <= 0) {
        throw new Exception("Invalid user ID");
    }
    
    $query = "UPDATE users SET role = 'moderator' WHERE id = $userid;";
    $conn->query($query);
  }
}

?>
