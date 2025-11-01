<?php
require 'vendor/autoload.php';
require_once 'src/model/connection.php';
require_once 'src/model/guestModel.php';
require_once 'src/model/userModel.php';
require_once 'src/model/moderatorModel.php';
require_once 'src/model/adminModel.php';
require_once 'src/model/authModel.php';

$loader = new \Twig\Loader\FilesystemLoader('./src/templates');
$twig = new \Twig\Environment($loader, []);

// upravljanje session-om
session_start();
if (isset($_SESSION['user'])) {
  $user = unserialize($_SESSION['user']);
  include 'src/view/userStatus.php';
}

// ako ne postoji user napravi guest usera
else {
  $user = new GuestModel();
  include 'src/view/loginBox.php';
}

include 'src/view/header.php';

$action = $_GET['action'] ?? null;
$blogpostid = $_GET['blogpostid'] ?? null;
$userid = $_GET['userid'] ?? null;
$profileid = $_GET['profileid'] ?? null;
$searchfor = $_GET['query'] ?? null;
$type = $_GET['type'] ?? null;
$reportType = $_GET['report_type'] ?? null;


// ako nema akcije, prikazi blogpostove
if (!$action) {
  $action = "list";
}

// router
// na osnovu action promenljive odredjujemo koji kontroler da pozovemo
switch ($action) {

  // sluzi kao homepage, prikazuje nazive i autore blogpostova
  case "list": {
  $msg = "All posts";
  include 'src/view/title.php';
    $results = $user->getBlogpostTitles($conn);
    foreach ($results as $result) {
      $by = $result['username'];
      $blogid = $result['id'];
      $title = $result['title'];
      $uid = $result['user_id'];
      include 'src/view/listBlogposts.php';
    }
    break;
  }

  // prikazuje sadrzaj jednog blogposta
  // zajedno sa opcijama za moderatore da uklone taj blogpost
  case "showblogpost": {
    $results = $user->getBlogpost($conn, $blogpostid);
    $result = $results->fetch_assoc();
    
    $title = $result['title'] ?? null;
    $username = $result['username'] ?? null;
    $content = $result['content'] ?? null;

    if (isset($_SESSION['user'])) {
      $user->log("Viewed a post titled $title by $username");
    }

    include 'src/view/viewBlogpost.php';
  
    if ($user->isOwner($blogpostid, $conn)
          || $user->role == "moderator"
          || $user->role == "admin") {
        include 'src/view/deleteBox.php';
    }
    break;
  }
  
  case "deleteblogpost": {
    $user->deletePost($blogpostid, $conn);
    break;
  }
  
  case "deleteuser": {
    $userid = $_GET['userid'];
    echo "Delete user $userid";
    $user->deleteUser($userid, $conn);
    break;
  }

  case "login": {
    $msg = "Log in";
    include 'src/view/title.php';
    $aModel = new AuthModel();
    $loggedUsername = $_POST['loggedUsername'] ?? null;
    $loggedPassword = $_POST['loggedPassword'] ?? null;

    include "src/view/login.php";

    // proverava da li postoji korisnik
    if ($loggedUsername && $loggedPassword) {
      $results = $aModel->loginUser($loggedUsername, $loggedPassword, $conn);
      // ako postoji dodeljuje mu adekvatnu klasu
      if ($results && $results->num_rows === 1) {
        $row = $results->fetch_assoc();
        switch($row['role']) {
          case 'user': {
            $user = new UserModel($row["id"], $row["username"], $row["role"]);
            break;
          }
          case 'moderator': {
            $user = new ModeratorModel($row["id"], $row["username"], $row["role"]);
            break;
          }
          case 'admin': {
            $user = new AdminModel($row["id"], $row["username"], $row["role"]);
            break;
          }
        }
        $user->log("Logged in");
        $_SESSION['user'] = serialize($user);
        header("Location:index.php");
      }
      else {
        $msg = "Not valid login info";
        include 'src/view/message.php';
      }
    }
    break;
  }

  // registracija dodaje korisnika u bazu
  case "register": {
    $msg = "Register";
    include 'src/view/title.php';
    $aModel = new AuthModel();
    $regUsername = $_POST['regUsername'] ?? null;
    $regPassword = $_POST['regPassword'] ?? null;

    include "src/view/register.php";

    if ($regUsername && $regPassword) {
      $checkQuery = "SELECT id FROM users WHERE username = '$regUsername' LIMIT 1;";
    $checkResult = $conn->query($checkQuery);

    if ($checkResult && $checkResult->num_rows > 0) { // ne mogu biti 2 ista username-a
      $msg="User with that username already exists";
      include 'src/view/message.php';
    }else{
      $aModel->registerUser($regUsername, $regPassword, $conn);
      header("Location:index.php");
    }
      
    }
    break;
  }

  case "logout": {
    $user->log("Logged out");
    session_destroy();
    header("Location:index.php");
    break;
  }

  // dodaje post u bazu
  case "post": {
    $msg = "Post";
    include 'src/view/title.php';
    include "src/view/post.php";
    $title = $_POST['title'] ?? null;
    $body = $_POST['body'] ?? null;

    if ($title && $body) {
      $user->post($title, $body, $conn);
      $user->log("Made a post named $title");
      header("Location:index.php");
    }
    break;
  }

  case "profile": {
    $msg = "Your profile";
    include 'src/view/title.php';
    $results = $user->getProfilePosts($user->id, $conn);
    foreach ($results as $result) {
      $by = $result['username'];
      $blogid = $result['id'];
      $title = $result['title'];
      include 'src/view/listBlogposts.php';
    }
  break;
  }

  // prikazuje profil nekog korisnika
  case "viewprofile": {
    $profileid = $_GET['profileid'];
    $results = $user->getProfilePosts($profileid, $conn);
    $row = $results->fetch_assoc();
    if (!$row) {
    $msg = "This user has no posts yet";
} else {
    $msg = $row['username'] . "'s profile";
}
    
    include 'src/view/title.php';

    foreach ($results as $result) {
      $by = $result['username'];
      $blogid = $result['id'];
      $title = $result['title'];
      include 'src/view/listBlogposts.php';
    }
    // moderator i administrator imaju
    // mogucnost da izbrisu profil ili mu unaprede nivo pristupa
    if ($user->role == "moderator" || $user->role == "admin") {
      include 'src/view/deleteUserBox.php';
    }
  break;
  }

  // pretraga korisnika ili postova 
  case "search": {
    $msg = "Search";
    include 'src/view/title.php';
    $searchfor = $_GET['query'] ?? null;
    $type = $_GET['type'] ?? null;
    include 'src/view/search.php';

    if ($type == "blog") {
      echo "Searching for posts with $searchfor";
      echo "<br>";
      $results = $user->searchPosts($searchfor, $conn);
      foreach ($results as $result) {
        $by = $result['username'];
        $blogid = $result['id'];
        $title = $result['title'];
        $uid = $result['user_id'];
        include 'src/view/listBlogposts.php';
      }
    }

    if ($type == "user") {
      echo "Searching for users with $searchfor";
      echo "<br>";
      $results = $user->searchUsers($searchfor, $conn);
      foreach ($results as $result) {
        $by = $result['username'];
        $uid = $result['id'];
        include 'src/view/listUsers.php';
      }
    }

    break;
  }

  // pisanje izvestaja u csv formatu za pregled ili excel
  case "report": {
    $msg = "Report";
    include 'src/view/title.php';

    include 'src/view/report.php';
    $reportType = $_GET['report_type'] ?? null;
    switch($reportType) {
      // pise izvestaj o broju postova od strane svakog korisnika
      case "blog_post_count": {
        $results = $user->blogPostCountByUser($conn);
        $csvFile = fopen('reports/blog_post_count_by_user.csv', 'w');

        $header = array('User ID', 'Post Count');
        fputcsv($csvFile, $header);

        foreach ($results as $row) {
          fputcsv($csvFile, $row);
        }

        fclose($csvFile);
        break;
      }

      // pise izvestaj o broju korisnika koji imaju odredjene nivoe pristupa
      case "user_count_by_role": {
        $results = $user->userCountByRole($conn);
        $csvFile = fopen('reports/user_count_by_role.csv', 'w');

        $header = array('Role', 'User Count');
        fputcsv($csvFile, $header);

        foreach ($results as $row) {
          fputcsv($csvFile, $row);
        }

        fclose($csvFile);
        break;
      }
    }
    break;
  }
  
  case "activity": {
    $msg = "User activity";
    include 'src/view/title.php';
    $filename = 'logs/logfile.txt'; // putanja do fajl 

    $file = fopen($filename, 'r');

    if ($file) {
      while (($line = fgets($file)) !== false) {
        echo $line . "<br>";
      }
      fclose($file); 
    } else {
      echo "Error while opening file.";
    }
  }

  case "makemod": {
    if (!isset($_GET['userid']) || !is_numeric($_GET['userid'])) {
       // echo "Error: missing or invalid user id";
        break;
    }

    $uid = intval($_GET['userid']);
    try {
        $user->makeMod($uid, $conn);
        echo "User with ID $uid is now a moderator.";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
    break;
  }

  default: {
    echo "ERROR";
  }
}

// twig
echo $twig->render('footer.twig', [
  'project' => "project",
  'name' => "Bogdan Jovanovic"
]);

