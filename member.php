<?php session_start(); include 'database.php'; ?>

<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>M: Hemsida</title>
</head>

<?php
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] == false)
    {
        header("Location: /5-Slutprojekt/501/login.php");
    }
    setcookie("active", true, time() + 3600); // 3600 s = 1 h

    if (isset($_POST['logout']) || !isset($_COOKIE['active']))
    {
        $_SESSION['loggedin'] = false;
        setcookie("active", true, time() - 3600);
        $_SESSION['username'] = "";
        header("Location: /5-Slutprojekt/501/login.php?logout");
    }

    if (isset($_POST['postSubmit']))
    {
        $userID = $_SESSION['userID'];
        $postMessage = $_POST['postText'];
        $sql = "INSERT INTO post (userID, message) VALUES ('$userID', '$postMessage')";
        if (mysqli_query($conn, $sql))
        {
            $message = "Message posted";
        }
        else
        {
            $error = "Databasen har fått ett error: " . mysqli_error($conn);
        }
    }

    $username = $_SESSION['username'];

    $sql = "SELECT * FROM post, user WHERE post.userID = user.userID";
    $result = mysqli_query($conn, $sql);
    $result_array = mysqli_fetch_all($result, MYSQLI_ASSOC);
?>

<body>
    <h1>M</h1>
    <h2>Välkommen <?php echo $username; ?></h2>
    <p> <?php if(isset($message)) echo $message; ?> </p>
    <form method="POST">
        <p><input type="text" name="postText" placeholder="Vad händer?" autocomplete="off" required>
        <input type="submit" name="postSubmit" value="Posta"></p>
    </form>

    <table>
        <tr>
            <th>Användare</th>
            <th>Meddelande</th>
            <th>Datum</th>
        </tr>
        <?php foreach ($result_array as $post)
        {
            $username = $post['username'];
            $postMessage = $post['message'];
            $date = $post['date'];
            
            echo "<tr><td>$username</td> <td>$postMessage</td><td>$date</td></tr>";
        }
        ?>
    </table>

    <form method="POST">
        <p><input type="submit" name="logout" value="Logga Ut"></p>
    </form>
</body>
</html>