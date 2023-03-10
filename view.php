<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kaung Sat Hein's View Page</title>
    <?php include_once("head.php") ?>
</head>
<body>
    <?php
        session_start(); 
        require_once("pdo.php");
        include_once("util.php");

        if ( ! isset($_GET['profile_id']) ) {
            $_SESSION['error'] = "Missing profile_id";
            header('Location: index.php');
            return;
        }

        $profile_id = htmlentities($_GET['profile_id']);
        $stmt = $pdo->prepare("SELECT * FROM profile WHERE profile_id = :id");
        $stmt->execute(array(":id" => $profile_id));
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ( $row === false ) {
            $_SESSION['error'] = 'Bad value for profile_id';
            header( 'Location: index.php' ) ;
            return;
        }

        $fn = htmlentities($row['first_name']);
        $ln = htmlentities($row['last_name']);
        $em = htmlentities($row['email']);
        $he = htmlentities($row['headline']);
        $su = htmlentities($row['summary']);


    ?>

    <div class="container">
        <h1>Profile information</h1>
        <b>First Name: </b><?= $fn ?><br><br>
        <b>Last Name: </b><?= $ln ?><br><br>
        <b>Email: </b><?= $em ?><br><br>
        <b>Headline: </b><br><?= $he ?><br><br>
        <b>Summary: </b><br><?= $su ?><br><br>
        <b>Position</b><br>
        <ul>
            <?php
                // function loadPos($pdo, $profile_id){
                //     $sql = "SELECT * FROM position WHERE profile_id = :prof ORDER BY rank";
                //     $stmt = $pdo->prepare($sql);
                //     $stmt->execute(array(":prof" => $profile_id));
                //     $position = [];
                //     $i=0;
                //     while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
                //         $position[] = $row;
                //         echo "<li>{$position[$i]['year']}: {$position[$i]['description']}</li>";
                //         $i++;
                //     }
                // }
                $position = loadPos($pdo, $profile_id);
                foreach($position as $item){
                    echo "<li>{$item['year']}: {$item['description']}</li>";
                }
                
            ?>
        </ul>
        <a href="index.php">Done</a>
    </div>
</body>
    <?php include_once("foot.php") ?>
</html>