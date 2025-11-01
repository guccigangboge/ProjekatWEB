<?php
echo "<a href='index.php?action=showblogpost&blogpostid=".$blogid."'>";
echo $title;
echo "</a>";

echo "<p>- ";
if (isset($uid)) {
    echo "<a href='index.php?action=viewprofile&profileid=".$uid."'>";
    echo $by;
    echo "</a>";
} else {
    echo $by;
}
echo "</p><br><br>";
?>
