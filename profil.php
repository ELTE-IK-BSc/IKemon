<?php
include_once("utils.php");
session_start();

$auth = authenticate();
$authenticated = $auth->is_authenticated();

$userID = $auth->authenticated_user()["id"];

$us = new UserStorage();
$adminID = $us->findOne(["username" => "admin"])["id"];
$user = $us->findById($userID);


$username = $user["username"];
$email = $user["email"];
$tokens = $user["token"];
$numOfCards = $user["numOfCards"];

$cs = new CardStorage();
$deck = $cs->findAll();



?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IKémon</title>
    <link rel="stylesheet" href="styles/main.css">
    <link rel="stylesheet" href="styles/cards.css">

</head>

<body>
    <header>
        <h1><a href="index.php">IKémon</a> > Profil <?= $username ?></h1>
        <div id="logger">
            Hello, <a href="profil.php"><?= $username ?></a>!
            <a class="buttonlikeLIGHT" href="authentication/logout.php">Logout</a> <br>
            <span class="card-price">Tokens <?= $tokens ?><span class="icon">💰</span> </span>

        </div>
        <?php if ($adminID === $userID) : ?>
            <div>
                <a href="addCard.php">Új pokémon hozzáadása</a>
            </div>
        <?php endif ?>

    </header>
    <main>
        <h2>User data</h2>
        <table>
            <thead>
                <th>Username</th>
                <th>Email</th>
                <th>Avaible tokens</th>
            </thead>
            <tbody>
                <td> <?= $username ?></a></td>
                <td> <?= $email ?></a></td>

                <td> <?= $tokens ?></a></td>

            </tbody>
        </table>
        <h2>IKémons</h2>
        <?php if ($numOfCards === 0) : ?>
            <p>Nincsenek IKémon kártyáid vegyél a <a href="index.php">főoldalon</a>!</p>
        <?php endif ?>

        <div id="card-list">
            <?php foreach ($deck as $card) : ?>
                <?php if ($authenticated && $card["ownerID"] === $userID) : ?>
                    <div class="pokemon-card">
                        <div class="image clr-<?= $card["type"] ?>">
                            <img src="<?= $card["image"] ?>" alt="">
                        </div>
                        <div class="details">
                            <h2><a href="details.php?id=<?= $card["id"] ?>"><?= $card["name"] ?></a></h2>
                            <span class="card-type"><span class="icon">🏷</span> <?= $card["type"] ?></span>
                            <span class="attributes">
                                <span class="card-hp"><span class="icon">❤</span> <?= $card["hp"] ?></span>
                                <span class="card-attack"><span class="icon">⚔</span> <?= $card["attack"] ?></span>
                                <span class="card-defense"><span class="icon">🛡</span> <?= $card["defense"] ?></span>
                            </span>
                        </div>
                        <a href="trade.php?trade=sell&id=<?= $card["id"] ?>">
                            <div class="sell">
                                <span class="card-price"><span class="icon">💰</span> <?= $card["price"] * 0.9 ?></span>
                            </div>
                        </a>
                    </div>
                <?php endif ?>

            <?php endforeach ?>

        </div>

    </main>
    <footer>
        <p>IKémon | ELTE IK Webprogramozás</p>
    </footer>
</body>

</html>