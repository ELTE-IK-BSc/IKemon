<?php
include_once("utils.php");
session_start();
$us = new UserStorage();

$auth = new Auth($us);
$authenticated = $auth->is_authenticated();
if ($authenticated) {
    $userID = $auth->authenticated_user()["id"];
    $user = $us->findById($userID);
    $username = $user["username"];
    $tokens = $user["token"];
}

$adminID = $us->findOne(["username" => "admin"])["id"];

$cs = new CardStorage();
$deck = $cs->findAll();

$pagesNum = $cs->numOfPages();


$pages = [];
$currPage = 1;
$cardNum = 1;

foreach ($deck as $card) {
    $card["buyable"] = ($authenticated && $card["ownerID"] === $adminID) ? true : false;
    $card["changeable"] = ($authenticated && $card["ownerID"] !== $adminID && $card["ownerID"] !== $userID) ? true : false;

    $pages[$currPage]["card" . $cardNum] = $card;
    if ($cardNum % 9 === 0) {
        $currPage++;
    }
    $cardNum++;
}

$selectedPage = $_POST["selectedPage"];

$cardsData = $pages[$selectedPage];




$data = [
    'cardsData' => $cardsData,
];

header("Content-type: application/json");
echo json_encode($data);
