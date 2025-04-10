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
        $postMessage = $_POST['postMessage'];
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

    if (isset($_POST['follow']))
    {
        $followUsername = $_POST['followUsername'];
        $followUserID = $_POST['followUserID'];
        $userID = $_SESSION['userID'];
        $sql = "INSERT INTO follow (followUserID, userID) VALUES ('$followUserID', '$userID')";
        if (mysqli_query($conn, $sql))
        {
            $message = "Followed $followUsername";
        }
        else
        {
            $error = "Databasen har fått ett error: " . mysqli_error($conn);
        }
    }

    if (isset($_POST['unfollow']))
    {
        $unfollowID = $_POST['unfollowID'];
        $unfollowUsername = $_POST['unfollowUsername'];
        $sql = "DELETE FROM follow WHERE followID = '$unfollowID'";
        if (mysqli_query($conn, $sql))
        {
            $message = "Unfollowed $unfollowUsername";
        }
        else
        {
            $error = "Databasen har fått ett error: " . mysqli_error($conn);
        }
    }

    $username = $_SESSION['username'];

    $sql = "SELECT * FROM post, user WHERE post.userID = user.userID";
    $postResult = mysqli_query($conn, $sql);
    $postResult_array = mysqli_fetch_all($postResult, MYSQLI_ASSOC);

    $sql = "SELECT * FROM follow";
    $followResult = mysqli_query($conn, $sql);
    $followResult_array = mysqli_fetch_all($followResult, MYSQLI_ASSOC);
?>

<body>
    <h1>M</h1>
    <h2>Välkommen <?php echo $username; ?></h2>
    <p> <?php if(isset($message)) echo $message; ?> </p>
    <form method="POST">
        <p><input type="text" name="postMessage" placeholder="Vad händer?" autocomplete="off" required>
        <input type="submit" name="postSubmit" value="Posta"></p>
    </form>

    <table>
        <tr>
            <th>Användare</th>
            <th>Meddelande</th>
            <th>Datum</th>
        </tr>
        <?php foreach ($postResult_array as $post)
        {
            $postUsername = $post['username'];
            $postUserID = $post['userID'];
            $postMessage = $post['message'];
            $postDate = $post['date'];
            
            echo "<tr><td>$postUsername</td> <td>$postMessage</td><td>$postDate</td>";
            if ($postUsername != $username)
            {
                $userID = $_SESSION['userID'];
                $isFollowingUser = false;
                foreach ($followResult_array as $follow)
                {
                    if ($follow['followUserID'] == $postUserID && $follow['userID'] == $userID)
                    {
                        $followID = $follow['followID'];
                        echo "<td> <form method='POST'> <input type='hidden' name='unfollowID' value='$followID'>
                        <input type='hidden' name='unfollowUsername' value='$postUsername'>
                        <input type='submit' name='unfollow' value='Unfollow'></form> <td>";
                        $isFollowingUser = true;
                    }
                }
                if ($isFollowingUser == false)
                {
                    echo "<td> <form method='POST'> <input type='hidden' name='followUserID' value='$postUserID'>
                    <input type='hidden' name='followUsername' value='$postUsername'>
                    <input type='submit' name='follow' value='Follow'></form> <td>";
                }
            }
            echo "</tr>";
        }
        ?>
    </table>

    <form method="POST">
        <p><input type="submit" name="logout" value="Logga Ut"></p>
    </form>
</body>
</html>