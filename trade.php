<?php
include_once("utils.php");
session_start();

$auth = authenticate();

$userID = $auth->authenticated_user()["id"];
$us = new UserStorage();
$user = $us->findById($userID);

$trade = $_GET["trade"];

if (isset($_GET["id"])) {
    $cardID = $_GET["id"];
}
$cs = new CardStorage();
$errors = [];

if ($trade === "buy") {
    if ($cs->buyCard($cardID, $user, $us, $errors)) {
        redirect("index.php");
    } else {
        redirect("index.php?error=" . implode($errors));
    }
} elseif ($trade === "sell") {
    if ($cs->sellCard($cardID, $user, $us, $errors)) {
        redirect("profil.php");
    }
} elseif ($trade === "random") {

    if ($cs->buyRandomCard($user, $us, $errors)) {
        redirect("index.php");
    } else {
        redirect("index.php?error=" . implode($errors));
    }
} else {
    redirect("index.php?error=Ismeretlen ügylet!");
}
