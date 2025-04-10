<?php include 'database.php'; ?>

<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>M: Registrera</title>
</head>

<?php
    if (isset($_POST['submit']))
    {
        $username = filter_input(INPUT_POST, 'username', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $password = filter_input(INPUT_POST, 'password', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        $sql = "SELECT * FROM user WHERE user.username = '$username'";

        $result = mysqli_query($conn, $sql);

        if (mysqli_num_rows($result) == 1)
        {
            // Generate error if user already exist in database
            $error = "&quot$username&quot är redan taget, snälla välj ett annat!";
        }
        else
        {
            $sql = "INSERT INTO user (username, password) VALUES ('$username','$hashed_password')";

            if (mysqli_query($conn, $sql))
            {
                header('Location: /5-Slutprojekt/501/login.php?registered');
                exit();
            }
            else
            {
                $error = "Databasen har fått ett error: " . mysqli_error($conn);
            }
        }
    }
?>


<body>
    <h1>Registera dig</h1>
    <form method="POST">
        <p><?php if (isset($error)) echo $error; ?></p>
        <p><label>Användarnamn:</label></p>
        <p><input type="text" name="username" required pattern="[A-Öa-ö0-9\s]+" placeholder="Använd bara bokstäver och siffror" style="width:210px"></p>
        <p><label>Lösenord:</label></p>
        <p><input type="text" name="password" required minlength="8" placeholder="Minst 8 tecken"></p>
        <p><input type="submit" name="submit"></p>
        <p>Har du redan ett konto? <a href="/5-Slutprojekt/501/login.php">Logga in här</a></p>
    </form>
</body>
</html>