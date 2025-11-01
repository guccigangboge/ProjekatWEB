<?php
echo "Mod tools:";
echo "<a href=index.php?action=deleteuser&userid=".$profileid.">";
echo " Delete user";
echo "</a>";

if ($user->role == 'admin') {
  echo "<a href=index.php?action=makemod&userid=".$profileid.">";
  echo " Make moderator";
  echo "</a>";
}
echo "<br>"

?>
