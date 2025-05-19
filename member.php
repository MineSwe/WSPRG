<?php session_start(); include 'database.php';?>

<!DOCTYPE html>
<html lang="sv">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>M: Hemsida</title>
    <link rel="icon" type="image/x-icon" href="icon.png">
    <style>
    <?php
    if (isset($_POST['darkmode']))
    {
        if (!isset($_SESSION['darkmode']))
        {
            $_SESSION['darkmode'] = true;
        }
        else
        {
            $_SESSION['darkmode'] = !$_SESSION['darkmode'];
        }
    }

    if (isset($_SESSION['darkmode']) && $_SESSION['darkmode'] == true)
    {
        $textColor = 'white';
        $bgColor = 'black';
        $darkmodeMessage = 'Lightmode';
    }
    else
    {
        $color = '';
        $bgColor = '';
        $darkmodeMessage = 'Darkmode';
    }
    ?>
    body, textarea, input {
        color: <?php echo $textColor ?>;
        background-color: <?php echo $bgColor ?>;
        font-family:'Trebuchet MS', 'Lucida Sans Unicode', 'Lucida Grande', 'Lucida Sans', Arial, sans-serif;
    }
    th, td {
        padding: 5px;
        border-bottom: 1px solid <?php $textColor ?>;
    }
    .tableButton form {
        display: flex;
        align-items: center;
        justify-content: center;
    }
    </style>
</head>

<?php
    if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] == false)
    {
        header("Location: login.php");
    }
    setcookie("active", true, time() + 3600); // 3600 s = 1 h

    if (isset($_POST['logout']) || !isset($_COOKIE['active']))
    {
        $_SESSION['loggedin'] = false;
        setcookie("active", true, time() - 3600);
        $_SESSION['username'] = "";
        header("Location: login.php?logout");
    }

    if (isset($_GET['success']))
    {
        $message = "Meddelandet har skickats!";
    }

    if (isset($_POST['postSubmit']))
    {
        $userID = $_SESSION['userID'];
        $postMessage = $_POST['postMessage'];
        $sql = "INSERT INTO post (userID, message) VALUES ('$userID', '$postMessage')";
        if (mysqli_query($conn, $sql))
        {
            header("Location: member.php?success");
        }
        else
        {
            $error = "Databasen har fått ett error: " . mysqli_error($conn);
        }
        unset($_POST['postMessage']);
        $_POST['postMessage'] = array();
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

    if (isset($_POST['deletePost']))
    {
        $postID = $_POST['postID'];
        $sql = "DELETE FROM post WHERE postID = '$postID'";
        if (mysqli_query($conn, $sql))
        {
            $message = "Deleted post";
        }
        else
        {
            $error = "Databasen har fått ett error: " . mysqli_error($conn);
        }
    }

    $username = $_SESSION['username'];

    $sql = "SELECT * FROM post, user WHERE post.userID = user.userID ORDER by post.postID DESC";
    $postResult = mysqli_query($conn, $sql);
    $postResult_array = mysqli_fetch_all($postResult, MYSQLI_ASSOC);

    $sql = "SELECT * FROM follow";
    $followResult = mysqli_query($conn, $sql);
    $followResult_array = mysqli_fetch_all($followResult, MYSQLI_ASSOC);

    $sql = "SELECT * FROM user";
    $usernameResult = mysqli_query($conn, $sql);
    $username_array = mysqli_fetch_all($followResult, MYSQLI_ASSOC);
?>

<body>
    <img src="icon.png" alt="M" width="100px">
    <h2>Välkommen <?php echo $username; ?></h2>
    <form method="POST">
        <p><input type="submit" name="darkmode" value="<?php echo$darkmodeMessage ?>"></p>
    </form>
    <p> <?php if(isset($message)) echo $message; ?> </p>
    <form method="POST">
        <p><textarea rows="100" style="width:80%; height:150px" name="postMessage" required>Vad händer?</textarea></p>
        <p><input type="submit" name="postSubmit" value="Posta"></p>
    </form>

    <table>
        <tr>
            <th>Användare</th>
            <th>Meddelande</th>
            <th></th>
        </tr>
        <?php foreach ($postResult_array as $post)
        {
            $postUsername = $post['username'];
            $postUserID = $post['userID'];
            $postMessage = $post['message'];
            $postDate = $post['date'];
            $postDate = date_create($post['date']);
            if (date('dmo', time()) == date_format($postDate,'dmo'))
            {
                $postDate = 'Idag ' . date_format(date_create($post['date']),'H:i');
            }
            else if (date('z', time()) - date_format($postDate,'z') == 1)
            {
                $postDate = 'Igår ' . date_format(date_create($post['date']),'H:i');
            }
            else
            {
                $postDate = date_format(date_create($post['date']),'F jS o H:i');
            }
            $postID = $post['postID'];
            
            echo "<tr style='white-space: pre-line'><td>$postUsername <br>$postDate</td> <td>$postMessage</td>";
            if ($postUsername != $username)
            {
                $userID = $_SESSION['userID'];
                $isFollowingUser = false;
                foreach ($followResult_array as $follow)
                {
                    if ($follow['followUserID'] == $postUserID && $follow['userID'] == $userID)
                    {
                        $followID = $follow['followID'];
                        echo "<td class='tableButton'> <form method='POST'> <input type='hidden' name='unfollowID' value='$followID'>
                        <input type='hidden' name='unfollowUsername' value='$postUsername'>
                        <input type='submit' name='unfollow' value='Unfollow'></form> <td>";
                        $isFollowingUser = true;
                    }
                }
                if ($isFollowingUser == false)
                {
                    echo "<td class='tableButton'> <form method='POST'> <input type='hidden' name='followUserID' value='$postUserID'>
                    <input type='hidden' name='followUsername' value='$postUsername'>
                    <input type='submit' name='follow' value='Follow'></form> <td>";
                }
            }
            else
            {
                echo "<td class='tableButton'> <form method='POST'> <input type='hidden' name='postID' value='$postID'>
                <input type='submit' name='deletePost' value='Delete'></form> <td>";
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