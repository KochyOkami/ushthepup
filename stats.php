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

        body {
            font-family: Arial, sans-serif;
            background-color: #121212;
            color: #ffffff;
            margin: 0;
            padding: 0;
        }

        h1 {
            text-align: center;
        }

        .card-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }

        .card {
            background-color: #000;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            width: 250px;
            padding: 20px;
            margin: 10px;
            text-align: center;
        }

        .card h2 {
            margin: 0 0 10px;
            font-size: 1.5em;
            color: #4B0082;
        }

        .card p {
            margin: 0;
            font-size: 1.2em;
        }
    </style>
</head>

<body>
    <?php include("includes/header.php"); ?>
    <h1 style="text-align:center;">Statistiques de Visite</h1>
    <div class="center">
        <div class="graph">
            <canvas id="visitsChart" style="display: block; box-sizing: border-box; height: 400; width: 400;"></canvas>
        </div>
        <div class="graph">
            <canvas id="avgDurationChart" style="display: block; box-sizing: border-box; height: 400; width: 400;"></canvas>
        </div>
    </div>

    <div class="card-container">
        <?php
        include("includes/db.php");

        $sql = "SELECT action, COUNT(DISTINCT session_id) AS sessions_count
                FROM visiteurs
                GROUP BY action";
        $result = $db->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Remplacer "page_visit_" par une chaîne vide si elle existe dans l'action
                $cleaned_action = str_replace("page_visit_", "", $row["action"]);

                // Afficher l'action nettoyée et le nombre de sessions
                echo "<div class='card'>";
                echo "<h2>" . ucfirst(htmlspecialchars($cleaned_action)) . "</h2>";
                echo "<p>Nombre: " . htmlspecialchars($row["sessions_count"]) . "</p>";
                echo "</div>";
            }
        } else {
            echo "<p>Aucun résultat trouvé</p>";
        }

        $db->close();
        ?>
    </div>
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
                    backgroundColor: 'rgba(75, 192, 192, 0.2)',
                    borderColor: 'rgba(75, 192, 192, 1)',
                    borderWidth: 1,
                    fill: false,
                }]
            },
            options: {
                scales: {
                    y: {
                        ticks: {
                            beginAtZero: true,
                            color: 'white' // Couleur des graduations de l'axe y
                        },
                        title: {
                            display: true,
                            text: 'Nombre de Visites',
                            color: 'white' // Couleur du titre de l'axe y
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.2)' // Couleur de la grille de l'axe y
                        }
                    },
                    x: {
                        ticks: {
                            color: 'white' // Couleur des graduations de l'axe x
                        },
                        title: {
                            display: true,
                            text: 'Date',
                            color: 'white' // Couleur du titre de l'axe x
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.2)' // Couleur de la grille de l'axe x
                        }
                    }
                },
                plugins: {
                    legend: {
                        labels: {
                            color: 'white' // Couleur de la légende
                        }
                    }
                }
            }
        });

        // Récupérer les données PHP en JSON
        const avgDurations = <?php echo json_encode($avgDurations); ?>;
        var unite = 's'
        // Préparer les données pour le graphique
        const labels2 = avgDurations.map(data => data.date);
        const durations = avgDurations.map(data => {
            const duration = data.avg_duration;
            if (duration >= 120) {
                // Convertir en minutes si la durée est supérieure ou égale à 120 secondes
                const minutes = Math.floor(duration / 60);
                unite = 'min';
                return minutes;
            } else {
                // Sinon, laisser la durée en secondes
                return duration;
            }
        });

        // Configuration du graphique
        const ctx2 = document.getElementById('avgDurationChart').getContext('2d');
        const avgDurationChart = new Chart(ctx2, {
            type: 'line', // Utiliser un graphique en ligne pour les moyennes
            data: {
                labels: labels2,
                datasets: [{
                    label: 'Durée Moyenne en ' + unite,
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
                            text: 'Durée Moyenne en ' + unite,
                            color: 'white' // Couleur du titre de l'axe y
                        },
                        ticks: {
                            color: 'white' // Couleur des graduations de l'axe y
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.2)' // Couleur de la grille de l'axe y
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Date',
                            color: 'white' // Couleur du titre de l'axe y
                        },
                        ticks: {
                            color: 'white' // Couleur des graduations de l'axe y
                        },
                        grid: {
                            color: 'rgba(255, 255, 255, 0.2)' // Couleur de la grille de l'axe y
                        }
                    }
                },
                plugins: {
                    legend: {
                        labels: {
                            color: 'white' // Couleur de la légende
                        }
                    }
                }
            }
        });
    </script>
</body>

</html>