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

$ms = new MessageStorage();

$cs = new CardStorage();
$deck = $cs->findAll();
if (isset($_GET["visitor"])) {
    $visitor = $_GET["visitor"];
} elseif (isset($_POST["visitor"])) {
    $visitor = $_POST["visitor"];
}
$errors = [];

function validatBonusCash($bc, $user, &$errors)
{
    if ((int)$bc > $user["token"]) {
        $errors["notenough"] = "Has not got enough token!";
    }
    if ((int)$bc < 0) {
        $errors["negativevalue"] = "Negative token not allowed!";
    }
    return count($errors) === 0;
}

if ($user["id"] === $adminID) {
    $errors["admin"] = "You are the admin and can not change cards!";
    redirect("index.php?error=" . implode($errors));
}

if (count($_GET) === 2 && $visitor === "giver") {
    $cardID = $_GET["id"];
    $changeCard = $cs->findById($cardID);
} elseif (count($_GET) === 2 && $visitor === "receiver") {
    $msgID = $_GET["messageID"];
    $msg = $ms->findById($msgID);
    $changeCardID = $msg["what"];
    $changeCard = $cs->findById($changeCardID);

    $selectedCardID = $msg["for"];
    $selectedCard = $cs->findById($selectedCardID);

    $ms->readMessage($msgID, $ms);
}

if (count($_POST) === 4 && $visitor === "giver") {

    if (validatBonusCash($_POST["bonusCash"], $user, $errors)) {
        $changeCardID = $_POST["changeCard"];
        $selectedCardID = $_POST["selectCards"];

        $changeCard = $cs->findById($changeCardID);
        $receiverUserID = $changeCard["ownerID"];

        $data = [];
        $data["from"] = $userID;
        $data["to"] = $receiverUserID;
        $data["what"] = $changeCardID;
        $data["for"] = $selectedCardID;
        if ((int)$_POST["bonusCash"] !== 0) {
            $data["bonusCash"] = (int)$_POST["bonusCash"];
        }
        $data["isRead"] = 0;
        // new messagw
        $ms->newMessage($receiverUserID, $us, $data, $ms);
        redirect("index.php");
    }
} elseif (count($_POST) === 4 && $visitor === "receiver") {
    if (validatBonusCash($_POST["bonusCash"], $user, $errors)) {

        $msgID = $_POST["messageID"];
        $msg = $ms->findById($msgID);

        $changeCardID = $msg["what"];
        $selectedCardID = $msg["for"];
        $card = $cs->findById($changeCardID);

        $exchange = $_POST["exchange"];



        if ($userID === $card["ownerID"] && $exchange === "true") {

            if ((int)$_POST["bonusCash"] > 0 || isset($msg["bonusCash"])) {
                $cs->chageCardsAndMoney($msg, $_POST["bonusCash"], $us);
            } else {
                $cs->chageCards($changeCardID, $selectedCardID);
            }
        }

        $ms->deleteMessage($userID, $us, $msgID, $ms);

        redirect("index.php");
    }
}

if (count($_GET) === 3 && $visitor === "receiver") {

    $msgID = $_GET["messageID"];
    $msg = $ms->findById($msgID);

    $changeCardID = $msg["what"];
    $selectedCardID = $msg["for"];
    $card = $cs->findById($changeCardID);

    $exchange = $_GET["exchange"];



    if ($userID === $card["ownerID"] && $exchange === "true") {

        if (isset($msg["bonusCash"])) {
            $cs->chageCardsAndMoney($msg, $_POST["bonusCash"], $us);
        } else {
            $cs->chageCards($changeCardID, $selectedCardID);
        }
    }

    $ms->deleteMessage($userID, $us, $msgID, $ms);

    redirect("index.php");
}




?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IKémon</title>
    <link rel="stylesheet" href="styles/main.css">
    <link rel="stylesheet" href="styles/change.css">
    <link rel="stylesheet" href="styles/cards.css">
    <link rel="stylesheet" href="styles/forms.css">

</head>

<body>
    <header>
        <h1><a href="index.php">IKémon</a> > Kártya csere</h1>
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

        <?php if ($visitor === "giver") : ?>
            <form action="" method="post">
                <input type="text" value="giver" name="visitor" hidden>
                <div id="changeCard">
                    <h2>Csere Kártya</h2>
                    <div class="card-list">

                        <div class="pokemon-card">
                            <div class="image clr-<?= $changeCard["type"] ?>">
                                <img src="<?= $changeCard["image"] ?>" alt="">
                            </div>
                            <div class="details">
                                <h2><a href="details.php?id=<?= $changeCard["id"] ?>"><?= $changeCard["name"] ?></a></h2>
                                <span class="card-type"><span class="icon">🏷</span> <?= $changeCard["type"] ?></span>
                                <span class="attributes">
                                    <span class="card-hp"><span class="icon">❤</span> <?= $changeCard["hp"] ?></span>
                                    <span class="card-attack"><span class="icon">⚔</span> <?= $changeCard["attack"] ?></span>
                                    <span class="card-defense"><span class="icon">🛡</span> <?= $changeCard["defense"] ?></span>
                                </span>
                            </div>
                        </div>
                        <input type="radio" id="<?= $changeCard["name"] ?>" name="changeCard" value="<?= $changeCard["id"] ?>" checked hidden>


                    </div>
                </div>

                <div id="changeIcon">
                    <div id="money">
                        <label for="bonusCash">Plusz pénz a cseréhez </label><br>
                        <input type="number" name="bonusCash" value="<?= $_GET["bonusCash"] ?? "0" ?>">

                        <?php if (isset($errors['notenough'])) : ?>
                            <span class="error"><?= $errors['notenough'] ?></span>
                        <?php endif; ?>
                        <?php if (isset($errors['negativevalue'])) : ?>
                            <span class="error"><?= $errors['negativevalue'] ?></span>
                        <?php endif; ?>
                    </div>

                    <input type="submit" value="🔄">
                </div>

                <div id="IKémonCards">
                    <h2>IKémons</h2>
                    <?php if ($numOfCards === 0) : ?>
                        <p>Nincsenek IKémon kártyáid vegyél a <a href="index.php">főoldalon</a>!</p>
                    <?php endif ?>

                    <div id="card-list">

                        <?php foreach ($deck as $card) : ?>
                            <?php if ($authenticated && $card["ownerID"] === $userID) : ?>
                                <div class="pokemon-card" data-cardname="<?= $card["name"] ?>">
                                    <div class="image clr-<?= $card["type"] ?>" data-cardname="<?= $card["name"] ?>">
                                        <img src="<?= $card["image"] ?>" alt="" data-cardname="<?= $card["name"] ?>">
                                    </div>
                                    <div class="details" data-cardname="<?= $card["name"] ?>">
                                        <h2 data-cardname="<?= $card["name"] ?>"><a href="details.php?id=<?= $card["id"] ?>"><?= $card["name"] ?></a></h2>
                                        <span class="card-type" data-cardname="<?= $card["name"] ?>"><span class="icon" data-cardname="<?= $card["name"] ?>">🏷</span> <?= $card["type"] ?></span>
                                        <span class="attributes" data-cardname="<?= $card["name"] ?>">
                                            <span class="card-hp" data-cardname="<?= $card["name"] ?>"><span class="icon" data-cardname="<?= $card["name"] ?>">❤</span> <?= $card["hp"] ?></span>
                                            <span class="card-attack" data-cardname="<?= $card["name"] ?>"><span class="icon" data-cardname="<?= $card["name"] ?>">⚔</span> <?= $card["attack"] ?></span>
                                            <span class="card-defense" data-cardname="<?= $card["name"] ?>"><span class="icon" data-cardname="<?= $card["name"] ?>">🛡</span> <?= $card["defense"] ?></span>
                                        </span>
                                    </div>
                                </div>
                                <input type="radio" id="<?= $card["name"] ?>" name="selectCards" value="<?= $card["id"] ?>" data-cardname="<?= $card["name"] ?>" hidden required>




                            <?php endif ?>

                        <?php endforeach ?>


                    </div>
                </div>
            </form>
        <?php elseif ($visitor === "receiver") : ?>

            <div id="changeCard">
                <h2>A kártyám</h2>


                <div class="pokemon-card">
                    <div class="image clr-<?= $changeCard["type"] ?>">
                        <img src="<?= $changeCard["image"] ?>" alt="">
                    </div>
                    <div class="details">
                        <h2><a href="details.php?id=<?= $changeCard["id"] ?>"><?= $changeCard["name"] ?></a></h2>
                        <span class="card-type"><span class="icon">🏷</span> <?= $changeCard["type"] ?></span>
                        <span class="attributes">
                            <span class="card-hp"><span class="icon">❤</span> <?= $changeCard["hp"] ?></span>
                            <span class="card-attack"><span class="icon">⚔</span> <?= $changeCard["attack"] ?></span>
                            <span class="card-defense"><span class="icon">🛡</span> <?= $changeCard["defense"] ?></span>
                        </span>
                    </div>
                </div>

            </div>

            <div id="changeIcon">
                <form action="" method="post">
                    <input type="text" value="receiver" name="visitor" hidden>
                    <input type="text" value="<?= $msgID ?>" name="messageID" hidden>


                    <div id="money">
                        <label for="bonusCash">Plusz pénz a cseréhez </label><br>
                        <input type="number" name="bonusCash" value="<?= $_GET["bonusCash"] ?? "0" ?>">

                        <?php if (isset($errors['notenough'])) : ?>
                            <span class="error"><?= $errors['notenough'] ?></span>
                        <?php endif; ?>
                        <?php if (isset($errors['negativevalue'])) : ?>
                            <span class="error"><?= $errors['negativevalue'] ?></span>
                        <?php endif; ?>
                    </div>
                    <label for="exchange">Csere elfogadása </label>
                    <select name="exchange" id="exchange">
                        <option value="true"> Elfogadás</option>

                        <option value="false">Elutasítás</option>
                    </select> <br>
                    <input type="submit" value="OK" class="buttonlikeDARK">
                </form>
            </div>

            <div id="IKémonCards">
                <h2>Csere Kártya</h2>

                <div class="pokemon-card">
                    <div class="image clr-<?= $selectedCard["type"] ?>">
                        <img src="<?= $selectedCard["image"] ?>" alt="">
                    </div>
                    <div class="details">
                        <h2><a href="details.php?id=<?= $selectedCard["id"] ?>"><?= $selectedCard["name"] ?></a></h2>
                        <span class="card-type"><span class="icon">🏷</span> <?= $selectedCard["type"] ?></span>
                        <span class="attributes">
                            <span class="card-hp"><span class="icon">❤</span> <?= $selectedCard["hp"] ?></span>
                            <span class="card-attack"><span class="icon">⚔</span> <?= $selectedCard["attack"] ?></span>
                            <span class="card-defense"><span class="icon">🛡</span> <?= $selectedCard["defense"] ?></span>
                        </span>
                    </div>
                </div>

                <?php if (isset($msg["bonusCash"])) : ?>
                    <span class="icon">➕</span>
                    <div class="pokemon-card">
                        <div class="clr-<?= $selectedCard["type"] ?>">
                            <span><?= $msg["bonusCash"] ?> <span class="icon">💰</span></span>
                        </div>
                    </div>
                <?php endif ?>
            </div>



        <?php else : ?>
            <h3>Nincs csere folyamatban</h3>
        <?php endif ?>

    </main>

    <footer>
        <p>IKémon | ELTE IK Webprogramozás</p>
    </footer>
    <script src="scripts/selectScript.js"></script>
</body>

</html>