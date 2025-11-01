<?php
class UserModel extends GuestModel {

  public $id;
  public $username;
  public $role;
  
  public function __construct($id, $username, $role) {
    $this->id = $id;
    $this->username = $username;
    $this->role = $role;
  }

  public function post($title, $body, $conn) {
    $query = "INSERT INTO `blog_posts` (`title`, `content`, `user_id`) VALUES ('$title', '$body', '$this->id');";
    $conn->query($query);
  }

  public function searchPosts($searchfor, $conn) {
    $query = "SELECT b.id, b.title, u.username, b.user_id 
              FROM blog_posts AS b 
              LEFT JOIN users AS u ON b.user_id = u.id 
              WHERE b.title REGEXP '$searchfor'
              ORDER BY b.created_at DESC";
    $results = $conn->query($query);
    return $results;
  }
  
  public function searchUsers($serachfor, $conn) {
    $query = "SELECT id, username FROM `users` WHERE username REGEXP '$serachfor'";
    $results = $conn->query($query);
    return $results;
  }

  public function isOwner($blogpostid, $conn) {
    $query = "SELECT * FROM `blog_posts` WHERE blog_posts.id = $blogpostid AND blog_posts.user_id = $this->id;";
    $results = $conn->query($query);
    if ($results && $results->num_rows === 1) {
      return 1;
    }
    return 0;
  }

  public function deletePost($blogpostid, $conn) {
    $query = "DELETE FROM `blog_posts` WHERE id = $blogpostid;";
    $conn->query($query);
  }

  public function log($act) {
    $timestamp = date('Y-m-d H:i:s');

    $logEntry = "[$timestamp] User $this->username: $act";

    file_put_contents('logs/logfile.txt', $logEntry . PHP_EOL, FILE_APPEND);
  }
}
?>
