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
    } else {
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
// Fonction pour obtenir les informations de localisation à partir de l'adresse IP
function getGeoInfo($ip)
{
    $apiKey = "19236d529bbd2e"; // Remplacez par votre clé API ipinfo.io
    $url = "https://ipinfo.io/{$ip}/json?token={$apiKey}";

    $curl = curl_init();
    curl_setopt($curl, CURLOPT_URL, $url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
    $response = curl_exec($curl);
    curl_close($curl);

    return json_decode($response, true);
}

// Fonction pour enregistrer les informations de localisation dans la base de données
function saveGeoInfo($ip, $geoInfo)
{
    include("db.php");
    $stmt = $db->prepare("INSERT INTO ip_locations (ip_address, country, city, region, latitude, longitude) VALUES (?, ?, ?, ?, ?, ?)");

    $latitude = isset($geoInfo['loc']) ? explode(',', $geoInfo['loc'])[0] : NULL;
    $longitude = isset($geoInfo['loc']) ? explode(',', $geoInfo['loc'])[1] : NULL;
    var_dump($geoInfo);
    // Utilisez des variables temporaires pour passer les valeurs à bind_param()
    $ip_address = $ip;
    $country = $geoInfo['country'] ?? NULL;
    $city = $geoInfo['city'] ?? NULL;
    $region = $geoInfo['region'] ?? NULL;

    $stmt->bind_param(
        "ssssdd",
        $ip_address,
        $country,
        $city,
        $region,
        $latitude,
        $longitude
    );

    $stmt->execute();
    $stmt->close();
}


// Fonction pour récupérer toutes les IP non présentes dans ip_locations et les ajouter
function updateMissingGeoInfo()
{
    include("db.php");
    // Requête pour récupérer les adresses IP distinctes de la table visiteurs qui ne sont pas dans ip_locations
    $sql = "SELECT DISTINCT v.ip_address
            FROM visiteurs v
            LEFT JOIN ip_locations l ON v.ip_address = l.ip_address
            WHERE l.ip_address IS NULL";
    $result = $db->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $ip_address = $row['ip_address'];
            if ($ip_address == "127.0.0.1") {
                continue; // Skip local IP address
            }
            $geoInfo = getGeoInfo($ip_address);
            if ($geoInfo) {
                saveGeoInfo($ip_address, $geoInfo);
            }
        }
    } else {
        echo "Aucune nouvelle adresse IP trouvée.";
    }
}
