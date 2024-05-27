<?php
session_start();

include("includes/functions.php");
// Vérifier si le paramètre 'platform' est présent dans l'URL
if (isset($_GET['redirect'])) {
    $platform = $_GET['redirect'];

    if ($platform == "insta") {
        recordPageVisit('insta');
        header("Location: https://www.instagram.com/ushthepup/");
    } else if ($platform == "twitter") {
        recordPageVisit('twitter');
        header("Location: https://x.com/ushthepup");
    } else if ($platform == "ngl") {
        recordPageVisit('ngl');
        header("Location: https://ngl.link/ushthepup");
    } else if ($platform == "site") {
        recordPageVisit('site');
        header("Location: https://ushthepup.fr");
    } else if ($platform == "newsletter") {
        addEmailNewsLetter($_POST['email']);
        $_SESSION['registered'] = true; // Set a session variable to indicate successful registration
        header("Location: index.php");
    } else if ($platform == "insta-pro") {
        recordPageVisit('insta-pro');
        header("Location: https://www.instagram.com/ushthepup/");
    } else if ($platform == "x-pro") {
        recordPageVisit('x-pro');
        header("Location: https://x.com/ushthepup");
    } else {
        header("Location: index.php");
    }
} else {
}
if (isset($_GET['from'])) {
    $from = $_GET['from'];
    updateDuration($from);
} else {
    header("Location: index.php");
}
