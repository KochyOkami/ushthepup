<?php
if (!isset($_GET['pswd']) || $_GET['pswd'] != "ushthepup") {
    header("Location: index.php");
    die("Accès refusé");
}
include("includes/db.php");

$sql = "SELECT DATE(FROM_UNIXTIME(time_start)) AS date, COUNT(DISTINCT session_id) AS visitor FROM visiteurs GROUP BY DATE(FROM_UNIXTIME(time_start));";
$result = $db->query($sql);

$visitData = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $visitData[] = $row;
    }
}

// Requête SQL pour obtenir la moyenne du temps passé par jour en excluant les durées de 0 secondes
$sql = "SELECT DATE(FROM_UNIXTIME(time_start)) AS date, AVG(duration) AS avg_duration
        FROM visiteurs
        WHERE duration > 0
        GROUP BY DATE(FROM_UNIXTIME(time_start))";

$result = $db->query($sql);

$avgDurations = [];
while ($row = $result->fetch_assoc()) {
    $avgDurations[] = $row;
}

$db->close();
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Statistiques de Visite</title>
    <!-- Inclure la bibliothèque de graphiques -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .graph {
            width: 40%;
        }

        .center {
            display: flex;
            justify-content: space-around;
        }
    </style>
</head>

<body>
    <h1 style="text-align:center;">Statistiques de Visite</h1>
    <div class="center">
        <div class="graph">
            <canvas id="visitsChart" style="display: block; box-sizing: border-box; height: 400; width: 400;"></canvas>
        </div>
        <div class="graph">
            <canvas id="avgDurationChart" style="display: block; box-sizing: border-box; height: 400; width: 400;"></canvas>
        </div>
    </div>

    <?php
    include("includes/db.php");

    $sql = "SELECT action, COUNT(DISTINCT session_id) AS sessions_count
        FROM visiteurs
        GROUP BY action";

    $result = $db->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            while ($row = $result->fetch_assoc()) {
                // Remplacer "page_visit_" par une chaîne vide si elle existe dans l'action
                $cleaned_action = str_replace("page_visit_", "", $row["action"]);

                // Afficher l'action nettoyée et le nombre de sessions
                echo "Action: " . $cleaned_action . ": " . $row["sessions_count"] . "<br>";
            }
        }
    } else {
        echo "Aucun résultat trouvé";
    }
    $db->close();
    ?>

    <script>
        // Récupérer les données des visites depuis PHP (à remplacer par votre propre méthode)
        let visitData = <?php echo json_encode($visitData); ?>;

        // Prétraiter les données pour le graphique
        let labels = [];
        let data = [];
        visitData.forEach(entry => {
            labels.push(entry.date);
            data.push(entry.visitor);
        });

        // Créer le graphique
        var ctx = document.getElementById('visitsChart').getContext('2d');
        var visitsChart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Nombre de Visites',
                    data: data,
                    borderColor: 'blue',
                    borderWidth: 1,
                    fill: false
                }]
            },
            options: {
                scales: {
                    yAxes: [{
                        ticks: {
                            beginAtZero: true
                        }
                    }]
                }
            }
        });

        // Récupérer les données PHP en JSON
        const avgDurations = <?php echo json_encode($avgDurations); ?>;

        // Préparer les données pour le graphique
        const labels2 = avgDurations.map(data => data.date);
        const durations = avgDurations.map(data => data.avg_duration / 1000);

        // Configuration du graphique
        const ctx2 = document.getElementById('avgDurationChart').getContext('2d');
        const avgDurationChart = new Chart(ctx2, {
            type: 'line', // Utiliser un graphique en ligne pour les moyennes
            data: {
                labels: labels2,
                datasets: [{
                    label: 'Durée Moyenne (secondes)',
                    data: durations,
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Durée Moyenne (secondes)'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Date'
                        }
                    }
                }
            }
        });
    </script>
</body>

</html>