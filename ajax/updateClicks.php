<?php 
    if (isset($_POST['id'])) {
        include '../config.php';
        $stmt = $dbcon->prepare("UPDATE `sites` SET clicks = clicks + 1 WHERE id = :id");
        $stmt->bindParam(':id', $_POST['id']);
        $stmt->execute();
        echo "Done";
    } else {
        echo "There Is No Id To Increase Clicks";
    }
?>