<?php
session_start();

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    // Basic validation
    if (empty($email) || empty($password)) {
        $message = "<p class='error'>All fields are required.</p>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "<p class='error'>Invalid email format.</p>";
    }

    // Authentication (Session-based for school project)
    elseif (
        isset($_SESSION["user"]) &&
        $email === $_SESSION["user"]["email"] &&
        password_verify($password, $_SESSION["user"]["password"])
    ) {

        // Secure session
        session_regenerate_id(true);

        $_SESSION["logged_in"] = true;

        header("Location: dashboard.php");
        exit();

    } else {
        $message = "<p class='error'>Invalid login credentials.</p>";
    }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Login | Job Application Portal</title>
    <link rel="stylesheet" href="css/style.css">
</head>

<body>

  <header class="header">
      <img src="images/logo_1.png" alt="Logo" class="logo">
      <h1>Job Application Portal</h1>
  </header>
    <nav>
        <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="register.php">Register</a></li>
            <li><a href="login.php">Login</a></li>
        </ul>
    </nav>

    <section>
        <h2>User Login</h2>

        <?php echo $message; ?>

        <form method="POST">

            <label>Email Address</label>
            <input type="email" name="email" required>

            <label>Password</label>
            <input type="password" name="password" required>

            <input type="submit" value="Login">

        </form>
    </section>

    <footer>
        <p>&copy; <?php echo date("Y"); ?> Job Application Portal</p>
    </footer>

</body>

</html>