<?php
session_start();

include("includes/functions.php");

// Si c'est la première visite de la session, enregistre l'heure de début
if (!isset($_SESSION['start_time'])) {
    $_SESSION['start_time'] = time();
    recordPageVisit('reseau');

} else {
    // Si la session a déjà commencé, vérifie si la durée de visite doit être mise à jour
    updateDuration('reseau');
}

?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>My Social Links</title>
    <link rel="stylesheet" href="css/reseau.css">
    <link rel="icon" href="img/logo.png">
</head>

<body>
    <h1> <img class="logo-ush" src="img/ush.png" alt="Instagram Logo">My Social Links</h1>
    <div class="center">
        <a class="button" href="click.php?platform=insta&from=reseau">
            <img class="logo" src="img/logo-insta.png" alt="Instagram Logo">
            <span>Instagram</span>
        </a>
        <a class="button" onclick="toggleDropdown()">
            <img class="logo" src="img/logo-x.png" alt="X Logo">
            <span>Twitter/X</span>
        </a>

        <div id="dropdown">
            <div class="center">
                <p style="margin-bottom:5px;">Attention: Ce contenu peut être NSFW (Not Safe for Work)<img class="moins-18" src="img/-18.png" alt="Instagram Logo"></p>
            </div>
            <button class="valider" onclick="redirectToLink(true)">J'ai +18 ans</button>
            <button class="refuser" onclick="redirectToLink(false)">J'ai-18 ans</button>
        </div>

        <a class="button" href="click.php?platform=ngl&from=reseau">
            <img class="logo" src="img/logo-ngl.png" alt="NGL Logo" style="border-radius: 15px;">
            <span>NGL</span>
        </a>
        <a class="button" href="click.php?platform=site&from=reseau">
            Site Web
        </a>

    </div>

    <script>
        function toggleDropdown() {
            var dropdown = document.getElementById("dropdown");
            dropdown.classList.toggle("visible");
        }

        function redirectToLink(isOver18) {
            if (isOver18) {
                window.location.href = "click.php?platform=x&from=reseau";
            } else {
                var dropdown = document.getElementById("dropdown");
                dropdown.classList.remove("visible");
            }
        }
    </script>
</body>

</html>