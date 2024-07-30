<?php
include_once("..\utils.php");

// functions
function validate($input, &$data, &$errors, &$auth)
{
  if (!isset($input["username"]) || trim($input["username"]) === "") {
    $errors["username"] = "Username is empty!";
  } else if (!$auth->user_exists($input['username'])) {
    $errors["username"] = "Username is not exists!";
  } else {
    $data["username"] = $input["username"];
  }
  if (!isset($input["password"]) || trim($input["password"]) === "") {
    $errors["password"] = "Password is empty!";
  } else if (!$auth->authenticate($input['username'], $input['password'])) {
    $errors["password"] = "Wrong password!";
  } else {
    $data["password"] = $input["password"];
  }




  return count($errors) === 0;
}

// main
session_start();
$user_storage = new UserStorage();
$auth = new Auth($user_storage);
$data = [];
$errors = [];
if (count($_POST) > 0) {
  if (validate($_POST, $data, $errors, $auth)) {
    $auth_user = $auth->authenticate($data['username'], $data['password']);
    if (!$auth_user) {
      $errors['global'] = "Login error";
    } else {
      $auth->login($auth_user);
      redirect("../index.php");
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
    <h1>Login</h1>

  </header>
  <main>
    <?php if (isset($errors['global'])) : ?>
      <p><span class="error"><?= $errors['global'] ?></span></p>
    <?php endif ?>
    <form action="" method="post">
      <div>
        <label for="username">Username: </label><br>
        <input type="text" name="username" id="username" value="<?= $_POST['username'] ?? "" ?>">
        <?php if (isset($errors['username'])) : ?>
          <span class="error"><?= $errors['username'] ?></span>
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
        <a href="register.php">Registration</a>
        <button type="submit">Login</button>
      </div>
    </form>
  </main>
  <footer>Foter</footer>
</body>

</html>