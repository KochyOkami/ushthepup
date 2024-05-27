<?php

function updateDuration($page)
{
    include("db.php");
    $session_id = session_id();
    $time_end = time();
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    $time_start = -1;
    $id = -1;

    // Récupérer l'enregistrement de la visite de la page
    $action = "page_visit_" . $page;
    $stmt = $db->prepare("SELECT id, time_start FROM visiteurs WHERE session_id = ? AND action = ? AND ip_address = ? AND user_agent = ?");
    $stmt->bind_param("ssss", $session_id, $action, $ip_address, $user_agent);
    $stmt->execute();
    $stmt->bind_result($id, $time_start);
    $stmt->fetch();
    $stmt->close();

    if ($id != -1 && $time_start != -1) {
        $duration = $time_end - $time_start;

        // Mettre à jour la durée dans la base de données
        $stmt = $db->prepare("UPDATE visiteurs SET duration = ? WHERE id = ?");
        $stmt->bind_param("ii", $duration, $id);
        $stmt->execute();
        $stmt->close();
    }else {
        recordPageVisit($page);
    }

    $db->close();
}
// Function to record when a user arrives on the page
function recordPageVisit($page)
{
    include("db.php");
    $ip_address = $_SERVER['REMOTE_ADDR'];
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    $session_id = session_id();
    $action = "page_visit_" . $page;
    $time_start = time();

    // Enregistrer l'action dans la base de données
    $stmt = $db->prepare("INSERT INTO visiteurs (ip_address, user_agent, action, session_id, time_start) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $ip_address, $user_agent, $action, $session_id, $time_start);
    $stmt->execute();
    $stmt->close();

    $db->close();
}

function addEmailNewsLetter($email)
{
    include("db.php");
    // Vérifier si l'email existe déjà dans la base de données
    $stmt = $db->prepare("SELECT COUNT(*) FROM newsletter WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->bind_result($count);
    $stmt->fetch();
    $stmt->close();

    if ($count == 0) {
        // Insérer l'email dans la base de données
        $stmt = $db->prepare("INSERT INTO newsletter (email) VALUES (?)");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->close();
    }

    $db->close();

}