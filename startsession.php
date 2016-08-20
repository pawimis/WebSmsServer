<?php
  session_start();

  if (!isset($_SESSION['id'])) {
    if (isset($_COOKIE['id'])&& isset($_COOKIE['user_login']) ) {
      $_SESSION['id'] = $_COOKIE['id'];
      $_SESSION['user_login'] = $_COOKIE['user_login'];
    }
  }
?>
