<?php 
session_start();
require_once("pdo.php");
include_once("util.php");

if ( ! isset($_SESSION['name']) ) {
    die('ACCESS DENIED');
    return;
}

if ( isset($_POST['cancel']) ) {
    unset($_SESSION['error']);
    header("Location: index.php");
    return;
}

if (isset($_POST['first_name']) && isset($_POST['last_name']) && isset($_POST['email']) && isset($_POST['headline']) && isset($_POST['summary'])) {

    $first_name = htmlentities($_POST['first_name']);
    $last_name = htmlentities($_POST['last_name']);
    $email = htmlentities($_POST['email']);
    $headline = htmlentities($_POST['headline']);
    $summary = htmlentities($_POST['summary']);

    // Validate Profile
    $msg = validateProfile();
    if( is_string($msg)){
        $_SESSION['error'] = $msg;
        header("Location: add.php");
        return;
    }

    // Validate Position
    $msg = validatePos();
    if( is_string($msg)){
        $_SESSION['error'] = $msg;
        header("Location: add.php");
        return;
    }

    // Insert Profile
    $sql = 'INSERT INTO Profile (user_id, first_name, last_name, email, headline, summary) VALUES ( :uid, :fn, :ln, :em, :he, :su)';
    $stmt = $pdo->prepare($sql);
    $stmt->execute(array(
        ':uid' => $_SESSION['user_id'],
        ':fn' => $first_name,
        ':ln' => $last_name,
        ':em' => $email,
        ':he' => $headline,
        ':su' => $summary)
    );
    $profile_id = $pdo->lastInsertId();

    // Insert Position
    $rank = 1;
    for($i=1; $i<=9; $i++){
        if(!isset($_POST["year".$i])) continue;
        if(!isset($_POST["desc".$i])) continue;
        $year = $_POST["year".$i];
        $desc = $_POST["desc".$i];

        $sql = 'INSERT INTO position (profile_id, rank, year, description) VALUES ( :pid, :rank, :year, :desc)';
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(
            ":pid" => $profile_id,
            ":rank" => $rank,
            ":year" => $year,
            ":desc" => $desc)
        );
        $rank++;
    }

    $_SESSION['success'] = "Profile added";
    header("Location: index.php");
    return;

    // if (strlen($first_name) < 1 || strlen($last_name) < 1 || strlen($email) < 1 || strlen($headline) < 1 || strlen($summary) < 1) {
    //     $_SESSION['error'] = "All fields are required";
    //     header("Location: add.php");
    //     return;
    // }
    // elseif ( strpos($email, "@") === false){
    //     $_SESSION['error'] = "Email address must contain @";
    //     header("Location: add.php");
    //     return;
    // }
    // else {
        // $sql = 'INSERT INTO Profile (user_id, first_name, last_name, email, headline, summary) VALUES ( :uid, :fn, :ln, :em, :he, :su)';
        // $stmt = $pdo->prepare($sql);
        // $stmt->execute(array(
        //     ':uid' => $_SESSION['user_id'],
        //     ':fn' => $first_name,
        //     ':ln' => $last_name,
        //     ':em' => $email,
        //     ':he' => $headline,
        //     ':su' => $summary)
        // );
        // $_SESSION['success'] = "Profile added";
        // header("Location: index.php");
        // return;
    // }
}
?>

<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8">
        <title>Kaung Sat Hein's Add Page</title>
        <?php include_once("head.php") ?>
    </head>

    <body>
        <div class="container">
            <h1 class="my-3">Adding Profile for <?= htmlentities($_SESSION["name"]) ?></h1>

            <?php
            // Flash pattern
                flashMessages();
            ?>
            
            <form method="post">
                <p>
                    <label for="first_name">First Name: </label>
                    <input type="text" id="first_name" name="first_name" size="60"/>
                </p>
                <p>
                    <label for="last_name">Last Name: </label>
                    <input type="text" id="last_name" name="last_name" size="60"/>
                </p>
                <p>
                    <label for="email">Email: </label>
                    <input type="text" id="email" name="email" size="40"/>
                </p>
                <p>
                    <label for="headline">Headline: </label><br>
                    <input type="text" id="headline" name="headline" size="80"/>
                </p>
                <p>
                    Summary: <br>
                    <textarea name="summary" cols="80" rows="8"></textarea>
                </p>
                <p>
                    Position: <button id="addPos" class="btn btn-light border"><i class="fa-regular fa-plus"></i></button>
                    <div id="positionContainer"></div>
                </p>
                <input type="submit" value="Add">
                <input type="submit" name="cancel" value="Cancel">
            </form>
        </div>
    </body>
    <?php include_once("foot.php") ?>
    <script>
        var countPos = 0;

        $(document).ready(function(){
            $("#addPos").click(function(event){
                event.preventDefault();     // Prevent a submit button from submitting a form, Prevent a link from following the URL
                if(countPos >=9){
                    alert("Maximum of nine position entries exceeded");
                    return;
                }
                countPos++;
                console.log("Adding position " + countPos);
                $("#positionContainer").append(
                    `<div id="position`+countPos+`">
                        <p>
                            Year: <input type="text" name="year`+countPos+`" value="">
                            <input type="button" value="-" onclick="$('#position`+countPos+`').remove(); return false;">
                        </p>
                        <textarea name="desc`+countPos+`" rows="8" cols="80"></textarea>
                    </div>`
                );
            });
        });
    </script>
</html>