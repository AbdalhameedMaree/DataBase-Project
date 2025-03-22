<?php
session_start();

$redirectPage = 'login.html';  // Login page to redirect if session fails

if (!isset($_SESSION['user_id'])) {
    header("Location: $redirectPage");
    exit();
}

if (isset($_GET['action'])) {
    switch ($_GET['action']) {
        case 'buy':
            header("Location: cart_buy_process.html?id=" . $_GET['id']);
            break;
        case 'see_similar':
            header("Location: " . $_GET['category'] . ".html");
            break;
        case 'account':
            header("Location: profile.html");
            break;
        default:
            header("Location: index.html");
            break;
    }
    exit();
}
?>
