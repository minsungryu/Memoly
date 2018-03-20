<?php
session_start();

if(isset($_SESSION['is_login'])){
    if (isset($_SESSION['is_admin'])) {
        header('Location: ./user.php');
    } else {
        header('Location: ./memo.php');
    }
}

require_once 'SignInController.php';

new SignInController();

?>