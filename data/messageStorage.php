<?php
include_once("storage.php");

class MessageStorage extends Storage
{
  public function __construct()
  {
    parent::__construct(new JsonIO(dirname(__DIR__, 1) . "\data\messages.json"));
  }

  public function newMessage($userID, &$us, $msgData, &$ms)
  {
    $msgID = $this->add($msgData);
    $user = $us->findById($userID);
    $userData = $user;
    $userData["messages"][] = $msgID;
    $us->update($userID, $userData);
  }

  public function readMessage($msgID, &$ms)
  {
    $msg = $this->findById($msgID);
    $msgData = $msg;
    $msgData["isRead"] = 1;
    $this->update($msgID, $msgData);
  }

  public function deleteMessage($userID, &$us, $msgID, &$ms)
  {
    $this->delete($msgID);
    $user = $us->findById($userID);
    $userData = $user;

    $key = array_search($msgID, $userData["messages"]);
    unset($userData["messages"][$key]);

    $us->update($userID, $userData);
  }
}
