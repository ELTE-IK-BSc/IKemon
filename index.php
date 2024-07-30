<?php
include_once("utils.php");
session_start();
$us = new UserStorage();
$ms = new MessageStorage();

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

$searchType = $cs->getSearchTypes($_GET);



$hasError = false;
if (isset($_GET["error"])) {
   $error = $_GET["error"];
   $hasError = true;
}




$mesages = [];
$hasUnRead = false;

if (isset($user["messages"])) {
   foreach ($user["messages"] as $msgID) {
      $msg = $ms->findById($msgID);
      $mesages[] = $msg;
      if ($msg["isRead"] === 0) {
         $hasUnRead = true;
      }
   }
}

$pagesNum = $cs->numOfPages();

?>
<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>IKémon</title>
   <link rel="stylesheet" href="styles/cards.css">
   <link rel="stylesheet" href="styles/main.css">


</head>

<body>
   <header>
      <h1><a href="index.php">IKémon > Home</a></h1>
      <div id="logger">
         <?php if ($authenticated) : ?>
            <span class="icon" id="msgbutton"> <?= ($hasUnRead) ? "📩" : "✉️" ?></span>
            <div id="messages" hidden>
               <h2>Üzenetek</h2>
               <?php if (count($mesages) > 0) : ?>

                  <?php foreach ($mesages as $msg) : ?>
                     <div class="message">
                        <a href="exchange.php?visitor=receiver&messageID=<?= $msg["id"] ?>"><?= $us->findById($msg["from"])["username"] ?>: <?= $cs->findById($msg["for"])["name"] ?> <?= isset($msg["bonusCash"]) ? "és " . $msg["bonusCash"] . "<span class='icon'>💰</span>" : "" ?> csere <?= $cs->findById($msg["what"])["name"] ?> ért.</a>
                        <a href="exchange.php?visitor=receiver&messageID=<?= $msg["id"] ?>&exchange=true" class="buttonlikeDARK">OK</a>
                        <a href="exchange.php?visitor=receiver&messageID=<?= $msg["id"] ?>&exchange=false" class="buttonlikeDARK">Cancel</a>
                     </div>
                  <?php endforeach ?>
               <?php else : ?>
                  <div class="message">
                     <p>Nincsenek üzenetek.</p>
                  </div>

               <?php endif ?>

            </div>
            Hello, <a href="profil.php"><?= $username ?></a>!
            <a class="buttonlikeLIGHT" href="authentication/logout.php">Logout</a> <br>
            <span class="card-price">Tokens <?= $tokens ?><span class="icon">💰</span> </span>

         <?php else : ?>
            <a class="buttonlikeLIGHT" href="authentication/login.php">Login</a> /
            <a class="buttonlikeLIGHT" href="authentication/register.php">Registration</a>
         <?php endif ?>
      </div>
      <?php if ($authenticated) : ?>
         <nav>
            <ul>
               <li>
                  <a href="profil.php">Felhasználó részletek</a>

               </li>
               <?php if ($adminID === $userID) : ?>
                  <li>
                     <a href="addCard.php">Új pokémon hozzáadása</a>
                  </li>
               <?php endif ?>

            </ul>
         </nav>
      <?php endif ?>

      <?php if ($authenticated && $user["id"] !== $adminID) : ?>
         <div class="buy buttonlikeLIGHT" id="randomBuyButton">
            <a href="trade.php?trade=random">
               <span class="card-price">Buy a random Card <span class="icon">💰</span>50</span>
            </a>
         </div>
      <?php endif ?>


   </header>
   <main>
      <?php if (!$authenticated) : ?>

         <h2>IKémon</h2>
         <p>
            Ez egy kártya gyűjtögetős oldal! Légy te a legnagyobb IKémon mester és gyűjtsd össze mind!
            Lépjbe vagy regisztrálj és szerezd meg mind!
         </p>
         <a id="register" href="authentication/register.php">Registration</a>
      <?php endif ?>


      <form action="" method="get" id="typeseaech">
         <h3 id="searchTitle"> Search by Type: </h3> <br>

         <ul id="types">
            <?php foreach ($cs->getTypes() as $type) : ?>
               <li>
                  <label for="<?= $type ?>"><?= $type ?></label>
                  <input type="checkbox" name="<?= $type ?>" id="type-<?= $type ?>" <?= isset($_GET[$type]) ? "checked" : "" ?>>
               </li>
            <?php endforeach ?>
         </ul>
         <br>
         <?php if ($authenticated) : ?>
            <ul id="searchExtras">
               <li>
                  <label for="forbuy">Buy</label>
                  <input type="checkbox" name="forbuy" id="forbuy" <?= isset($_GET["forbuy"]) ? "checked" : "" ?>>
               </li>
               <li>
                  <label for="forexchange">Exchange</label>
                  <input type="checkbox" name="forexchange" id="forexchange" <?= isset($_GET["forexchange"]) ? "checked" : "" ?>>
               </li>
            </ul>

         <?php endif ?>

         <input type="reset" value="Reset" class="searchButton" id="resetSearch">
         <input type="submit" value="Search" class="searchButton">
      </form>




      <div id="content">
         <?php if (!empty($searchType) || isset($_GET["forbuy"]) || isset($_GET["forexchange"])) : ?>
            <div class="card-list">
               <?php foreach ($deck as $card) : ?>
                  <?php if (!empty($searchType)) : ?>
                     <?php if (in_array($card["type"], $searchType)) : ?>
                        <?php if (isset($_GET["forbuy"]) && !isset($_GET["forexchange"])) : ?>
                           <?php if ($authenticated && $card["ownerID"] === $adminID) : ?>
                              <div class="pokemon-card">
                                 <div class="image clr-<?= $card["type"] ?>">
                                    <img src="<?= $card["image"] ?>" alt="The <?= $card["name"] ?> pokemons picture.">
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
                                 <div class="buy">
                                    <a href="trade.php?trade=buy&id=<?= $card["id"] ?>">
                                       <span class="card-price"><span class="icon">💰</span> <?= $card["price"] ?></span>
                                    </a>
                                 </div>
                              </div>
                           <?php endif ?>
                        <?php elseif (!isset($_GET["forbuy"]) && isset($_GET["forexchange"])) : ?>
                           <?php if ($authenticated && $card["ownerID"] !== $adminID && $card["ownerID"] !== $userID) : ?>
                              <div class="pokemon-card">
                                 <div class="image clr-<?= $card["type"] ?>">
                                    <img src="<?= $card["image"] ?>" alt="The <?= $card["name"] ?> pokemons picture.">
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
                                 <div class="exchange">
                                    <a href="exchange.php?visitor=giver&id=<?= $card["id"] ?>">
                                       <span class="card-price"><span class="icon">🔄</span>Csere</span>
                                    </a>
                                 </div>
                              </div>
                           <?php endif ?>
                        <?php elseif (isset($_GET["forbuy"]) && isset($_GET["forexchange"])) : ?>
                           <?php if (($authenticated && $card["ownerID"] === $adminID) || ($authenticated && $card["ownerID"] !== $adminID && $card["ownerID"] !== $userID)) : ?>
                              <div class="pokemon-card">
                                 <div class="image clr-<?= $card["type"] ?>">
                                    <img src="<?= $card["image"] ?>" alt="The <?= $card["name"] ?> pokemons picture.">
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
                                 <?php if ($authenticated && $card["ownerID"] === $adminID) : ?>
                                    <div class="buy">
                                       <a href="trade.php?trade=buy&id=<?= $card["id"] ?>">
                                          <span class="card-price"><span class="icon">💰</span> <?= $card["price"] ?></span>
                                       </a>
                                    </div>
                                 <?php elseif ($authenticated && $card["ownerID"] !== $adminID && $card["ownerID"] !== $userID) : ?>
                                    <div class="exchange">
                                       <a href="exchange.php?visitor=giver&id=<?= $card["id"] ?>">
                                          <span class="card-price"><span class="icon">🔄</span>Csere</span>
                                       </a>
                                    </div>
                                 <?php endif ?>
                              </div>
                           <?php endif ?>

                        <?php elseif (!isset($_GET["forbuy"]) && !isset($_GET["forexchange"])) : ?>
                           <div class="pokemon-card">
                              <div class="image clr-<?= $card["type"] ?>">
                                 <img src="<?= $card["image"] ?>" alt="The <?= $card["name"] ?> pokemons picture.">
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
                              <?php if ($authenticated && $card["ownerID"] === $adminID) : ?>
                                 <div class="buy">
                                    <a href="trade.php?trade=buy&id=<?= $card["id"] ?>">
                                       <span class="card-price"><span class="icon">💰</span> <?= $card["price"] ?></span>
                                    </a>
                                 </div>
                              <?php elseif ($authenticated && $card["ownerID"] !== $adminID && $card["ownerID"] !== $userID) : ?>
                                 <div class="exchange">
                                    <a href="exchange.php?visitor=giver&id=<?= $card["id"] ?>">
                                       <span class="card-price"><span class="icon">🔄</span>Csere</span>
                                    </a>
                                 </div>
                              <?php endif ?>
                           </div>
                        <?php endif ?>
                     <?php endif ?>
                  <?php elseif (isset($_GET["forbuy"]) && !isset($_GET["forexchange"])) : ?>
                     <?php if ($authenticated && $card["ownerID"] === $adminID) : ?>
                        <div class="pokemon-card">
                           <div class="image clr-<?= $card["type"] ?>">
                              <img src="<?= $card["image"] ?>" alt="The <?= $card["name"] ?> pokemons picture.">
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
                           <div class="buy">
                              <a href="trade.php?trade=buy&id=<?= $card["id"] ?>">
                                 <span class="card-price"><span class="icon">💰</span> <?= $card["price"] ?></span>
                              </a>
                           </div>
                        </div>
                     <?php endif ?>
                  <?php elseif (!isset($_GET["forbuy"]) && isset($_GET["forexchange"])) : ?>
                     <?php if ($authenticated && $card["ownerID"] !== $adminID && $card["ownerID"] !== $userID) : ?>
                        <div class="pokemon-card">
                           <div class="image clr-<?= $card["type"] ?>">
                              <img src="<?= $card["image"] ?>" alt="The <?= $card["name"] ?> pokemons picture.">
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
                           <div class="exchange">
                              <a href="exchange.php?visitor=giver&id=<?= $card["id"] ?>">
                                 <span class="card-price"><span class="icon">🔄</span>Csere</span>
                              </a>
                           </div>
                        </div>
                     <?php endif ?>
                  <?php elseif (isset($_GET["forbuy"]) && isset($_GET["forexchange"])) : ?>
                     <?php if (($authenticated && $card["ownerID"] === $adminID) || ($authenticated && $card["ownerID"] !== $adminID && $card["ownerID"] !== $userID)) : ?>
                        <div class="pokemon-card">
                           <div class="image clr-<?= $card["type"] ?>">
                              <img src="<?= $card["image"] ?>" alt="The <?= $card["name"] ?> pokemons picture.">
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
                           <?php if ($authenticated && $card["ownerID"] === $adminID) : ?>
                              <div class="buy">
                                 <a href="trade.php?trade=buy&id=<?= $card["id"] ?>">
                                    <span class="card-price"><span class="icon">💰</span> <?= $card["price"] ?></span>
                                 </a>
                              </div>
                           <?php elseif ($authenticated && $card["ownerID"] !== $adminID && $card["ownerID"] !== $userID) : ?>
                              <div class="exchange">
                                 <a href="exchange.php?visitor=giver&id=<?= $card["id"] ?>">
                                    <span class="card-price"><span class="icon">🔄</span>Csere</span>
                                 </a>
                              </div>
                           <?php endif ?>
                        </div>
                     <?php endif ?>

                  <?php endif ?>

               <?php endforeach ?>

            </div>
         <?php else : ?>
            <div id="card-list">

            </div>
         <?php endif ?>

      </div>

      <div id="pagesDiv">
         <span class="icon" id="leftArrow">⬅️</span>
         <ul id="pages">
            <?php for ($i = 1; $i <= $pagesNum; $i++) : ?>
               <li><?= $i ?></li>
            <?php endfor ?>
         </ul>
         <span class="icon" id="rightArrow" data-maxpage="<?= $pagesNum ?>">➡️</span>
      </div>

      <?php if ($hasError) : ?>
         <div id="errorBox">
            <h2 id="errorMsg"><?= $error ?></h2>
            <a href="index.php" id="cancel" class="buttonlikeLIGHT">X</a>
         </div>
      <?php endif ?>

   </main>
   <footer>
      <p>IKémon | ELTE IK Webprogramozás</p>
   </footer>
   <script src="scripts/searchScript.js"></script>
   <script src="scripts/pages.js"></script>
   <script src="scripts/message.js"></script>
</body>

</html>