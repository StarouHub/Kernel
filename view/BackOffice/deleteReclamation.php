<?php
require '../../Verification.php';
require '../../Controller/ReclamationController.php';
if(isset($_GET['id'])){
    deleteReclamation($_GET['id']);
}
header("Location: bookList.php");
?>