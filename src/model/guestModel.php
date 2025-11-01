<?php
class GuestModel {
public $role = "guest";

    public function getBlogpostTitles($conn) {
        $query = "SELECT b.id, b.title, u.username, b.user_id FROM blog_posts AS b LEFT JOIN users AS u ON b.user_id = u.id ORDER BY b.created_at DESC;";
        $results = $conn->query($query);

        return $results;
    }

    public function getProfilePosts($userid, $conn) {
        $query = "SELECT b.id, b.title, u.username FROM blog_posts AS b LEFT JOIN users AS u ON b.user_id = u.id WHERE u.id = '$userid' ORDER BY b.created_at DESC;";
        $results = $conn->query($query);

        return $results;
    }

    public function getBlogPost($conn, $blogpostID) {
        $query = "SELECT b.title, b.content, u.username FROM blog_posts AS b LEFT JOIN users AS u ON b.user_id = u.id WHERE b.id = $blogpostID;";
        $results = $conn->query($query);

        return $results;
    }

     public function isOwner($blogpostID, $conn) {
        return false;
    }
}
