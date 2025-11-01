<?php
class ModeratorModel extends UserModel {

  public function deleteUser($userid, $conn) {
    $query = "DELETE FROM `blog_posts` WHERE user_id = $userid;";
    $conn->query($query);
    $query = "DELETE FROM `users` WHERE id = $userid;";
    $conn->query($query);
  }
}
?>
