<?php
include_once("storage.php");

class UserStorage extends Storage
{
  public function __construct()
  {
    parent::__construct(new JsonIO(dirname(__DIR__, 1) . "\data\users.json"));
  }

  function moneyUpdate($change, &$user){
    $userData = $user;
    $userData["token"] = $user["token"] + $change;
    $this->update($user["id"], $userData);

  }

  function IKemonCardNumUpdate(&$user, &$cs){
    $userData = $user;
    $userData["numOfCards"] = &$cs->cardCount($user["id"]);
    $this->update($user["id"], $userData);
  }
}
