<?php
include_once("..\utils.php");
include_once("..\data\userStorage.php");
session_start();

function validate($input, &$data, &$errors)
{
  if (!isset($input["username"])) {
    $errors["username"] = "Username is required!";
  } else if (trim($input["username"]) === "") {
    $errors["username"] = "Username is required!";
  } else {
    $data["username"] = $input["username"];
  }

  if (!isset($input["email"])) {
    $errors["emails"] = "Email is required!";
  } else if (trim($input["email"]) === "") {
    $errors["email"] = "Email is required!";
  } else {
    if (!filter_var($input["email"], FILTER_VALIDATE_EMAIL)) {
      $errors["email"] = "The email has invalid format!";
    } else {
      $data["email"] = $input["email"];
    }
  }
  if (!isset($input["password"])) {
    $errors["password"] = "Password is required!";
  } else if (trim($input["password"]) === "") {
    $errors["password"] = "Password is required!";
  }
  if (!isset($input["affirmation"])) {
    $errors["affirmation"] = "Password affirmation is required!";
  } else if (trim($input["affirmation"]) === "") {
    $errors["affirmation"] = "Password affirmation is required!";
  } else if ($input["password"] !== $input["affirmation"]) {
    $errors["password"] = "The passwords are not matching!";
  } else {
    $data["password"] = $input["password"];
  }



  // print_r($data);
  // print_r($errors);
  return count($errors) === 0;
}

$user_storage = new UserStorage();
$auth = new Auth($user_storage);

$errors = [];
$data = [];

if (count($_POST) > 0) {
  if (validate($_POST, $data, $errors)) {
    if ($auth->user_exists($data['username'])) {
      $errors['global'] = "User already exists";
    } else {
      $auth->register($data);
      $auth_user = $auth->authenticate($data['username'], $data['password']);
      if (!$auth_user) {
        $errors['global'] = "Login error";
      } else {
        $auth->login($auth_user);
        redirect("../index.php");
      }
    }
  }

}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Document</title>
  <link rel="stylesheet" href="../styles/main.css">
  <link rel="stylesheet" href="../styles/forms.css">

</head>

<body>
  <header>
    <h1>Registration</h1>
  </header>
  <?php if (isset($errors['global'])) : ?>
    <p id="globalerror"><span class="error"><?= $errors['global'] ?></span></p>
  <?php endif; ?>
  <main>
    <form action="" method="post" novalidate>
      <div>
        <label for="username">Username: </label><br>
        <input type="text" name="username" id="username" value="<?= $_POST['username'] ?? "" ?>">
        <?php if (isset($errors['username'])) : ?>
          <span class="error"><?= $errors['username'] ?></span>
        <?php endif; ?>
      </div>
      <div>
        <label for="email">Email: </label><br>
        <input type="email" name="email" value="<?= $_POST["email"] ?? "" ?>">
        <?php if (isset($errors['email'])) : ?>
          <span class="error"><?= $errors['email'] ?></span>
        <?php endif; ?>
      </div>
      <div>
        <label for="password">Password: </label><br>
        <input type="password" name="password" id="password">
        <?php if (isset($errors['password'])) : ?>
          <span class="error"><?= $errors['password'] ?></span>
        <?php endif; ?>
      </div>
      <div>
        <label for="affirmation">Password affirmation: </label><br>
        <input type="password" name="affirmation" id="affirmation">
        <?php if (isset($errors['affirmation'])) : ?>
          <span class="error"><?= $errors['affirmation'] ?></span>
        <?php endif; ?>
      </div>

      <div>
        <a href="login.php">Login</a><button type="submit">Register</button>
      </div>
    </form>
  </main>

  <footer>Foo</footer>
</body>

</html>