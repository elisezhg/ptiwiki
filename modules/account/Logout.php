<?php

function logout()
{
  $_SESSION['idUser'] = "";
  $_SESSION['username'] = "";

  // Stay on same page after logout
  $location = str_replace("&action=logout", "", basename($_SERVER['REQUEST_URI']));
  header('location: ' . $location);
}
