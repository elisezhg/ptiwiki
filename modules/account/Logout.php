<?php

function logout()
{
  $_SESSION['idUser'] = "";
  $_SESSION['username'] = "";
}
