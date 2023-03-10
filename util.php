<?php
    function flashMessages(){
        if( isset($_SESSION["error"]) ) {
            echo('<p style="color: red;">'.htmlentities($_SESSION['error'])."</p>\n");
            unset($_SESSION['error']);
        }
        if( isset($_SESSION["success"]) ) {
            echo('<p style="color: green;">'.htmlentities($_SESSION['success'])."</p>\n");
            unset($_SESSION['success']);
        }
    }

    function validateProfile(){
        if(strlen($_POST['first_name']) < 1 || strlen($_POST['last_name']) < 1 || strlen($_POST['email']) < 1 || strlen($_POST['headline']) < 1 || strlen($_POST['summary']) < 1) {
            return "All fields are required";
        }
        elseif( strpos($_POST['email'], "@") === false){
            return "Email address must contain @";
        }
        else{
            return true;
        }
    }

    function validatePos(){
        for($i=1; $i<=9; $i++){
            if(!isset($_POST["year".$i])) continue;
            if(!isset($_POST["desc".$i])) continue;
            $year = $_POST["year".$i];
            $desc = $_POST["desc".$i];
            if(strlen($year) == 0 || strlen($desc) == 0){
                return "All fields are required";
            }
            elseif(!is_numeric($year)){
                return "Year must be numeric";
            }
            else{
                return true;
            }
        }
    }

    function loadPos($pdo, $profile_id){
        $sql = "SELECT * FROM position WHERE profile_id = :prof ORDER BY rank";
        $stmt = $pdo->prepare($sql);
        $stmt->execute(array(":prof" => $profile_id));
        $position = [];
        while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
            $position[] = $row;
        }
        return $position;
    }
?>