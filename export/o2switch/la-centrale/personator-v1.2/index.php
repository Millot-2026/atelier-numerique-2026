<?php
// Point d'entrée - Redirection vers le générateur principal
ob_start();
header("Location: generator.php");
exit;
?>