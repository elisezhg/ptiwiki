<?php

function logout()
{
  $_SESSION['idUser'] = "";
  $_SESSION['username'] = "";

  // Stay on same page after logout
  header('location: ' . getURLWithoutActionParam());
}
