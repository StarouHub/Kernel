<?php
require_once '../../controller/userController.php';

$controller = new UserController();
$controller->logout();
// La redirection est gérée par le contrôleur
?>