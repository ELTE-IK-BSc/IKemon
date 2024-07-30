<?php

include_once("storage.php");

class CardStorage extends Storage
{
   public function __construct()
   {
      parent::__construct(new JsonIO(dirname(__DIR__, 1) . "\data\cards.json"));
   }

   public function getTypes()
   {
      $types = [];
      foreach ($this->findAll() as $card) {
         if (!in_array($card["type"], $types)) {
            $types[] =  $card["type"];
         }
      }
      return $types;
   }

   public function getSearchTypes($isOn)
   {
      $types = [];
      foreach ($this->findAll() as $card) {
         if (!in_array($card["type"], $types) && isset($isOn[$card["type"]])) {
            $types[] =  $card["type"];
         }
      }
      return $types;
   }

   public function cardCount($userID)
   {

      return count($this->findAll(["ownerID" => $userID]));
   }

   public function numOfPages($cardPerPage = 9)
   {
      $deck = $this->findAll();
      if (count($deck) % $cardPerPage === 0) {
         $numOfPages = ((count($deck) - count($deck) % $cardPerPage) / $cardPerPage);
      } else {
         $numOfPages = ((count($deck) - count($deck) % $cardPerPage) / $cardPerPage) + 1;
      }

      return $numOfPages;
   }

   public function buyCard($cardID, $user, &$us, &$errors)
   {

      $card = $this->findById($cardID);
      $adminID = $us->findOne(["username" => "admin"])["id"];

      if ($user["numOfCards"] >= 5) {
         $errors["numOfCards"] = "You have the maximum 5 cards!";
         return count($errors) === 0;
      }
      if ($card["price"] > $user["token"]) {
         $errors["token"] = "You have not got enough token!";
         return count($errors) === 0;
      }
      if ($user["id"] === $adminID) {
         $errors["admin"] = "You are the admin and can not buy cards!";
         return count($errors) === 0;
      }

      // CardStorage update
      $cardData = $card;
      $cardData["ownerID"] = $user["id"];
      $this->update($cardID, $cardData);

      // UserStorage update
      $userData = $user;
      $userData["token"] = $user["token"] - $card["price"];
      $userData["numOfCards"] = $this->cardCount($user["id"]);
      $us->update($user["id"], $userData);

      $adminID = $us->findOne(["username" => "admin"])["id"];
      $adminData = $us->findById($adminID);
      $adminData["numOfCards"] = $this->cardCount($adminID);
      $us->update($adminID, $adminData);



      return count($errors) === 0;
   }


   public function buyRandomCard($user, &$us, &$errors)
   {
      $adminID = $us->findOne(["username" => "admin"])["id"];
      $cards = $this->findAll(["ownerID" => $adminID]);

      if ($user["numOfCards"] >= 5) {
         $errors["numOfCards"] = "You have the maximum 5 cards!";
      } elseif (50 > $user["token"]) {
         $errors["token"] = "You have not got enough token!";
      } elseif (count($cards) === 0) {
         $errors["admincard"] = "The admin has not got cards!";
      } else {
         // Select random card 
         $card = $cards[array_rand($cards, 1)];
         $cardID = $card["id"];

         // CardStorage update
         $cardData = $card;
         $cardData["ownerID"] = $user["id"];
         $this->update($cardID, $cardData);

         // UserStorage update
         $userData = $user;
         $userData["token"] = $user["token"] - 50;
         $userData["numOfCards"] = $this->cardCount($user["id"]);
         $us->update($user["id"], $userData);

         $adminData = $us->findById($adminID);
         $adminData["numOfCards"] = $this->cardCount($adminID);
         $us->update($adminID, $adminData);
      }

      return count($errors) === 0;
   }

   public function sellCard($cardID, $user, &$us)
   {

      $card = $this->findById($cardID);
      $adminID = $us->findOne(["username" => "admin"])["id"];

      // CardStorage update
      $cardData = $card;
      $cardData["ownerID"] = $adminID;
      $this->update($cardID, $cardData);

      // UserStorage update
      $userData = $user;
      $userData["token"] = $user["token"] + ($card["price"] * 0.9);
      $userData["numOfCards"] = $this->cardCount($user["id"]);
      $us->update($user["id"], $userData);

      $adminData = $us->findById($adminID);
      $adminData["numOfCards"] = $this->cardCount($adminID);
      $us->update($adminID, $adminData);

      return true;
   }

   public function chageCards($changeCardID, $selectedCardID)
   {
      $changeCard = $this->findById($changeCardID);
      $selectedCard = $this->findById($selectedCardID);

      $changeCardData = $changeCard;
      $selectedCardData = $selectedCard;

      $userID = $changeCard["ownerID"];
      $partnerID = $selectedCard["ownerID"];


      $changeCardData["ownerID"] = $partnerID;
      $selectedCardData["ownerID"] = $userID;



      $this->update($changeCardID, $changeCardData);
      $this->update($selectedCardID, $selectedCardData);
   }

   public function chageCardsAndMoney($message, $recieverMoney, &$us)
   {
      $giver = $us->findById($message["from"]);
      $reciever = $us->findById($message["to"]);

      $changeCard = $this->findById($message["what"]);
      $selectedCard = $this->findById($message["for"]);

      $changeCardData = $changeCard;
      $selectedCardData = $selectedCard;

      $userID = $changeCard["ownerID"];
      $partnerID = $selectedCard["ownerID"];


      $changeCardData["ownerID"] = $partnerID;
      $selectedCardData["ownerID"] = $userID;


      $us->moneyUpdate($recieverMoney, $us->findById($message["from"]));
      $us->moneyUpdate(-$recieverMoney, $us->findById($message["to"]));


      if (isset($message["bonusCash"])) {
         $us->moneyUpdate(-$message["bonusCash"], $us->findById($message["from"]));
         $us->moneyUpdate($message["bonusCash"], $us->findById($message["to"]));
      }


      $this->update($message["what"], $changeCardData);
      $this->update($message["for"], $selectedCardData);
   }
}
