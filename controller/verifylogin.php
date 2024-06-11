<?php
if(!isset($_SESSION["usuario"]) || !isset($_SESSION["permicao"])) {
    header("Location: ./login.php");
    exit;
}