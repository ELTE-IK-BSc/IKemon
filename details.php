<?php
include_once("utils.php");
session_start();
$us = new UserStorage();
$adminID = $us->findOne(["username" => "admin"])["id"];

$cardID = $_GET["id"];

$auth = new Auth($us);
$authenticated = $auth->is_authenticated();
if ($authenticated) {
    $userID = $auth->authenticated_user()["id"];
    $user = $us->findById($userID);
    $username = $user["username"];
    $tokens = $user["token"];
}


$cs = new CardStorage();
$card = $cs->findById($cardID);

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IKémon | Pikachu</title>
    <link rel="stylesheet" href="styles/main.css">
    <link rel="stylesheet" href="styles/details.css">
</head>

<body>
    <header>
        <h1><a href="index.php">IKémon</a> > <?= $card["name"] ?></h1>

        <div id="logger">
            <?php if ($authenticated) : ?>
                Hello, <a href="profil.php"><?= $username ?></a>!
                <a class="buttonlike" href="authentication/logout.php">Logout</a> <br>
                <span class="card-price">Tokens <?= $tokens ?><span class="icon">💰</span> </span>

            <?php else : ?>
                <a class="buttonlike" href="authentication/login.php">Login</a> /
                <a class="buttonlike" href="authentication/register.php">Registration</a>
            <?php endif ?>
        </div>
        <?php if ($authenticated) : ?>
            <nav>
                <ul>
                    <li>
                        <a href="profil.php">Felhasználó részletek</a>

                    </li>
                    <?php if ($adminID === $userID && $adminID === $card["ownerID"]) : ?>
                        <li>
                            <a href="editCard.php?id=<?= $cardID ?>">Szerkeztés</a>
                        </li>
                    <?php endif ?>

                </ul>
            </nav>
        <?php endif ?>

    </header>


    <div id="content" class=" clr-<?= $card["type"] ?>">
        <div id="details">
            <div class="image clr-<?= $card["type"] ?>">
                <img src=" <?= $card["image"] ?>" alt="The <?= $card["name"] ?> pokemons picture.">
            </div>
            <div class="info">
                <div class="description"> <?= $card["description"] ?></div>
                <span class="card-type"><span class="icon">🏷</span> Type: <?= $card["type"] ?></span>
                <div class="attributes">
                    <div class="card-hp"><span class="icon">❤</span> Health: <?= $card["hp"] ?></div>
                    <div class="card-attack"><span class="icon">⚔</span> Attack: <?= $card["attack"] ?></div>
                    <div class="card-defense"><span class="icon">🛡</span> Defense: <?= $card["defense"] ?></div>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <p>IKémon | ELTE IK Webprogramozás</p>
    </footer>
</body>

</html>