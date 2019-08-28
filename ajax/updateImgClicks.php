<?php 
    include "../config.php";
    if (isset($_POST['src'])) {
        $stmt = $dbcon->prepare("UPDATE `images` SET clicks = clicks + 1 WHERE `imageurl` = :src");
        $stmt->bindParam(':src', $_POST['src']);
        $stmt->execute();
        echo $_POST['src'];
    } else {
        echo "There Is No Image To Increse It's Clicks";
    }
?>