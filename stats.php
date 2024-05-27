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
            width: 50%;
            margin: 0 auto;
        }
    </style>
</head>

<body>
    <h1>Statistiques de Visite</h1>
    <div class="graph">
        <canvas id="visitsChart" width="800" height="400" style="display: block; box-sizing: border-box; height: 800; width: 400;"></canvas>
    </div>
    <?php
    include("includes/db.php");

    $sql = "SELECT action, COUNT(DISTINCT session_id) AS sessions_count
        FROM visiteurs
        GROUP BY action";

    $result = $db->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            echo "Action: " . $row["action"] . ": " . $row["sessions_count"] . "<br>";
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
    </script>
</body>

</html>