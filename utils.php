<?php
include_once("authentication\auth.php");
include_once("data\userStorage.php");
include_once("data\cardStorage.php");
include_once("data\messageStorage.php");



function redirect($page)
{
  header("Location: {$page}");
  exit();
}

function authenticate()
{
  $auth = new Auth(new UserStorage());
  if (!$auth->is_authenticated()) {
    redirect("authentication/login.php");
  }
  return $auth;
}
