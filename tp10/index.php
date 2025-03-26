<?php 
error_reporting(E_ALL); 
ini_set('display_errors', 1);
require('Model/pdo.php');

try {
    $etudiant = $dbPDO->prepare("
    SELECT etudiants.*, classes.libelle as classe_nom 
    FROM etudiants
    JOIN classes ON etudiants.classe_id = classes.id
");
    $etudiant->execute();
    $etudiants = $etudiant->fetchAll();
    $classe = $dbPDO->prepare("SELECT * FROM classes");
    $classe->execute();
    $classes = $classe->fetchAll(PDO::FETCH_ASSOC);
    $professeur = $dbPDO->prepare("
    SELECT professeurs.nom, professeurs.prenom, matiere.lib as matiere, classes.libelle as classe_nom
    FROM professeurs
    JOIN matiere ON professeurs.id_matiere = matiere.id
    JOIN classes ON professeurs.id_classe = classes.id
    ");
    $professeur->execute();
    $professeurs = $professeur->fetchAll(PDO::FETCH_ASSOC);
    
} catch(PDOException $e) {
    die("Erreur SQL : " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liste des Étudiants</title>
    <style>
    /* <!--le link au css ne voulait pas se faire obliger de le mettre dans le ccode  --!> */
       body {
            background-color:rgb(228, 237, 244); 
            font-family: 'Arial', sans-serif;
            margin: 2rem;
        }
        header {
            background-color: #1a365d;
            color: white;
            padding: 1.5rem;
            text-align: center;
            margin-bottom: 2rem;
        }
        
        .table-container {
            overflow-x: auto;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
        }

       
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
        }

        
        thead {
            background-color: #1a365d; 
            color: white;
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border: 1px solid #ddd;
        }

        th {
            font-weight: bold;
            text-transform: uppercase;
            font-size: 0.9em;
        }

       
        tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        
        tbody tr:hover {
            background-color: #e9ecef;
        }

        
        td {
            border: 1px solid #dee2e6;
        }
        footer {
            background-color: #1a365d;
            color: white;
            text-align: center;
            padding: 1.5rem;
            margin-top: 2rem;
        }
    </style>
</head>
<body>
    <header>
        <h1>AFFICHAGE</h1>
    </header>
    
    <div class="container">
        <!-- Tableau des Étudiants -->
        <section class="table-section">
            <h2>Liste des Étudiants</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Prénom</th>
                        <th>Nom</th>
                        <th>Classe</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($etudiants as $e): ?>
                    <tr>
                        <td><?= htmlspecialchars($e['id']) ?></td>
                        <td><?= htmlspecialchars($e['prenom']) ?></td>
                        <td><?= htmlspecialchars($e['nom']) ?></td>
                        <td><?= htmlspecialchars($e['classe_nom']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
        
        <!-- Tableau des Classes -->
        <section class="table-section">
            <h2>Liste des Classes</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Libellé</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($classes as $c): ?>
                    <tr>
                        <td><?= htmlspecialchars($c['id']) ?></td>
                        <td><?= htmlspecialchars($c['libelle']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
        
        <!-- Tableau des Professeurs -->
        <section class="table-section">
            <h2>Liste des Professeurs</h2>
            <table>
                <thead>
                    <tr>
                        <th>Nom</th>
                        <th>Prénom</th>
                        <th>Matière</th>
                        <th>Classe</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($professeurs as $p): ?>
                    <tr>
                        <td><?= htmlspecialchars($p['nom']) ?></td>
                        <td><?= htmlspecialchars($p['prenom']) ?></td>
                        <td><?= htmlspecialchars($p['matiere']) ?></td>
                        <td><?= htmlspecialchars($p['classe_nom']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </div>
    
    <footer>
        <p>&copy 2025-Hugo Vialaneix</
    </footer>
</body>

</html>