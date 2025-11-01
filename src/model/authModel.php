<?php
class AuthModel {
    public function registerUser($uname, $password, $conn) {
        $query = "INSERT INTO users(username, role, password)
        VALUES('$uname', 'user', '$password');";

        $conn->query($query);
    }
    public function loginUser ($uname, $password, $conn) {
        $query = "SELECT * FROM `users` WHERE username = '$uname' AND password = '$password';";
        $results = $conn->query($query);
        return $results;
    }
}
?>
