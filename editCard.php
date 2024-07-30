<?php
include_once("utils.php");
session_start();

function validate($input, &$data, &$errors)
{
    if (!isset($input["name"]) || trim($input["name"]) === "") {
        $errors["name"] = "Name is required!";
    } else {
        $data["name"] = $input["name"];
    }

    if (!isset($input["type"]) || trim($input["type"]) === "") {
        $errors["type"] = "Type is required!";
    } else {
        $data["type"] = $input["type"];
    }

    if (!isset($input["hp"]) || trim($input["hp"]) === "") {
        $errors["hp"] = "Health is required!";
    } else if (filter_var($input["hp"], FILTER_VALIDATE_REGEXP, [
        "options" => [
            "regexp" => "/^[0-9]*/",
        ],
    ]) === false) {
        $errors["hp"] = "Invalid health format!";
    } elseif ($input["hp"] < 0) {
        $errors["hp"] = "Negative value!";
    } else {
        $data["hp"] = $input["hp"];
    }

    if (!isset($input["attack"]) || trim($input["attack"]) === "") {
        $errors["attack"] = "Attack is required!";
    } else if (filter_var($input["attack"], FILTER_VALIDATE_REGEXP, [
        "options" => [
            "regexp" => "/^[0-9]*/",
        ],
    ]) === false) {
        $errors["attack"] = "Invalid attack format!";
    } elseif ($input["attack"] < 0) {
        $errors["attack"] = "Negative value!";
    } else {
        $data["attack"] = $input["attack"];
    }

    if (!isset($input["defense"]) || trim($input["defense"]) === "") {
        $errors["defense"] = "Defense is required!";
    } else if (filter_var($input["defense"], FILTER_VALIDATE_REGEXP, [
        "options" => [
            "regexp" => "/^[0-9]*/",
        ],
    ]) === false) {
        $errors["defense"] = "Invalid defense format!";
    } elseif ($input["defense"] < 0) {
        $errors["defense"] = "Negative value!";
    } else {
        $data["defense"] = $input["defense"];
    }

    if (!isset($input["price"]) || trim($input["price"]) === "") {
        $errors["price"] = "Price is required!";
    } else if (filter_var($input["price"], FILTER_VALIDATE_REGEXP, [
        "options" => [
            "regexp" => "/^[0-9]*/",
        ],
    ]) === false) {
        $errors["price"] = "Invalid price format!";
    } elseif ($input["price"] < 0) {
        $errors["price"] = "Negative value!";
    } else {
        $data["price"] = $input["price"];
    }


    if (isset($input["description"]) && trim($input["description"]) !== "") {
        $data["description"] = $input["description"];
    }
    if (isset($input["image"]) && trim($input["image"]) !== "") {
        if (filter_var($input["image"], FILTER_VALIDATE_URL) === false) {
            $errors["image"] = "Invalid url format!";
        } else {
            $data["image"] = $input["image"];
        }
    }

    return count($errors) === 0;
}



$us = new UserStorage();
$adminID = $us->findOne(["username" => "admin"])["id"];

$auth = new Auth($us);
$authenticated = $auth->is_authenticated();
$userID = $auth->authenticated_user()["id"];

$cardID = $_GET["id"];

$cs = new CardStorage();
$card = $cs->findById($cardID);

if (($authenticated && $adminID !== $userID) || $adminID !== $card["ownerID"]) {
    redirect("authentication/login.php");
} else {
    $user = $us->findById($userID);
    $username = $user["username"];
    $tokens = $user["token"];
}






$errors = [];
$data = [];


if (count($_POST) > 0) {
    if (validate($_POST, $data, $errors)) {
        $data["ownerID"] = $adminID;
        $data["id"] = $cardID;

        $cs->update($cardID, $data);
        redirect("../index.php");
    }
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Új pokémon hozzáadása</title>
    <link rel="stylesheet" href="../styles/main.css">
    <link rel="stylesheet" href="../styles/forms.css">

</head>

<body>
    <header>
        <h1>IKémon szerkeztése</h1>
    </header>
    <?php if (isset($errors['global'])) : ?>
        <p><span class="error"><?= $errors['global'] ?></span></p>
    <?php endif; ?>
    <main class=" clr-<?= $card["type"] ?>">
        <form action="" method="post" novalidate>
            <div>
                <label for="name">Card name: </label><br>
                <input type="text" name="name" value="<?= $_POST['name'] ?? $card['name'] ?>">
                <?php if (isset($errors['name'])) : ?>
                    <span class="error"><?= $errors['name'] ?></span>
                <?php endif ?>
            </div>

            <div>
                <label for="type">Type: </label><br>
                <input type="text" name="type" value="<?= $_POST["type"] ?? $card["type"] ?>">
                <?php if (isset($errors['type'])) : ?>
                    <span class="error"><?= $errors['type'] ?></span>
                <?php endif ?>
            </div>

            <div>
                <label for="hp">Health: </label><br>
                <input type="number" name="hp" value="<?= $_POST["hp"] ?? $card["hp"] ?>">
                <?php if (isset($errors['hp'])) : ?>
                    <span class="error"><?= $errors['hp'] ?></span>
                <?php endif ?>
            </div>

            <div>
                <label for="attack">Attack: </label><br>
                <input type="number" name="attack" value="<?= $_POST["attack"] ?? $card["attack"] ?>">
                <?php if (isset($errors['attack'])) : ?>
                    <span class="error"><?= $errors['attack'] ?></span>
                <?php endif ?>
            </div>

            <div>
                <label for="defense">Defense: </label><br>
                <input type="number" name="defense" value="<?= $_POST["defense"] ?? $card["defense"] ?>">
                <?php if (isset($errors['defense'])) : ?>
                    <span class="error"><?= $errors['defense'] ?></span>
                <?php endif ?>
            </div>

            <div>
                <label for="price">Price: </label><br>
                <input type="number" name="price" value="<?= $_POST["price"] ?? $card["price"] ?>">
                <?php if (isset($errors['price'])) : ?>
                    <span class="error"><?= $errors['price'] ?></span>
                <?php endif ?>
            </div>
            <div>
                <label for="description">Description: </label><br>
                <textarea name="description" rows="6" cols="35"><?= $_POST["description"] ?? $card["description"] ?></textarea>
                <?php if (isset($errors['description'])) : ?>
                    <span class="error"><?= $errors['description'] ?></span>
                <?php endif ?>
            </div>
            <div>
                <label for="image">Image url: </label><br>
                <input type="url" name="image" value="<?= $_POST["image"] ?? $card["image"] ?>">
                <?php if (isset($errors['image'])) : ?>
                    <span class="error"><?= $errors['image'] ?></span>
                <?php endif ?>
            </div>
            <div>
                <button type="submit">Save</button>
            </div>
        </form>
    </main>

    <footer>
        <p>IKémon | ELTE IK Webprogramozás</p>
    </footer>
</body>

</html>