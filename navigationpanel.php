<?php
  // Generowanie menu nawigacyjnego.
 
  if (isset($_SESSION['id'])) {
	echo '<hr />';
	echo'<p class="header">';
    echo '<a href="index.php">  Main page  </a>  ';
    echo '<a href="viewprofile.php">  Send message  </a>  ';
    echo '<a href="logout.php">  Logout </a>';
	echo'</p>';
	echo '<hr />';
  }
?>