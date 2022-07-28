<?php

function logout()
{
  $_SESSION['idUser'] = "";
  $_SESSION['username'] = "";

  // Stay on same page after logout
  $location =
    basename($_SERVER['REQUEST_URI']) == '?action=logout' ? '/' :
    str_replace("action=logout&", "", basename($_SERVER['REQUEST_URI']));
  header('location: ' . $location);
}
