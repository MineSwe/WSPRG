<?php session_start(); include 'database.php'; ?>

<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>M: Logga in</title>
</head>

<?php
    if (isset($_GET['registered']))
    {
        $message = "Du har registrerat! Logga in här:";
    }

    if (isset($_GET['logout']))
    {
        $message = "Du har loggats ut, logga in här:";
    }

    if (isset($_POST['submit']))
    {
        $username = $_POST['username'];
        $password = $_POST['password'];

        filter_input(INPUT_POST, $username, FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        filter_input(INPUT_POST, $password, FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $sql = "SELECT * FROM user";
        $result = mysqli_query($conn, $sql);

        $result_array = mysqli_fetch_all($result, MYSQLI_ASSOC);
        foreach ($result_array as $user)
        {
            if ($user['username'] == $username && password_verify($password, $user['password']))
            {
                $_SESSION['userID'] = $user->userID;
                $_SESSION['username'] = $username;
                $_SESSION['loggedin'] = true;
                setcookie("active", true, time() + 3600); // 3600 s = 1 h
                header('Location: /5-Slutprojekt/501/member.php');
                exit();
            }
            else
            {
                $error = 'Ditt användarnamn eller lösenord är fel, försök igen!';
            }
        }
    }
?>

<body>
    <h1>Logga in</h1>
    <p><?php if(isset($message)) echo $message; ?></p>
    <form method="POST">
        <p><?php if (isset($error)) echo $error; ?></p>
        <p><label>Användarnamn:</label></p>
        <p><input type="text" name="username" required></p>
        <p><label>Lösenord:</label></p>
        <p><input type="text" name="password" required></p>
        <p><input type="submit" name="submit"></p>
        <p>Har du inget konto? <a href="/5-Slutprojekt/501/register.php">Registrera till M</a></p>
        
    </form>
</body>
</html>