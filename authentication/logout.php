<?php
session_start();
include_once("..\utils.php");

$auth = new Auth(new UserStorage());

$auth->logout();
redirect("../index.php");
