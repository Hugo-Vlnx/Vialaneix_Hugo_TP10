<?php
session_start();


if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

require('Model/pdo.php');


$admin_error = '';
$admin_success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_admin'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $admin_error = 'Les mots de passe ne correspondent pas';
    } else {
        
        $check_email = $dbPDO->prepare("SELECT * FROM user WHERE email = :email");
        $check_email->bindParam(':email', $email);
        $check_email->execute();

        if ($check_email->rowCount() > 0) {
            $admin_error = 'Cet email est déjà utilisé';
        } else {
           
            $query = $dbPDO->prepare("INSERT INTO user (email, password) VALUES (:email, :password)");
            $query->bindParam(':email', $email);
            $query->bindParam(':password', $password);

            if ($query->execute()) {
                $admin_success = 'Administrateur ajouté avec succès';
            } else {
                $admin_error = 'Erreur lors de l\'ajout de l\'administrateur';
            }
        }
    }
}


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
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panneau d'Administration</title>
    <style>
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
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .action-buttons {
            display: flex;
            justify-content: center;
            margin-bottom: 2rem;
            gap: 20px;
        }

        .action-btn {
            background-color: #1a365d;
            color: white;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }

        .action-btn:hover {
            background-color: #0a1f38;
        }

        .logout-btn {
            background-color: white;
            color: #1a365d;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            transition: background-color 0.3s, color 0.3s;
        }

        .logout-btn:hover {
            background-color: #e9ecef;
            color: #0a1f38;
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

        .action-column {
            text-align: center;
        }

        .action-column a {
            margin: 0 5px;
            color: #1a365d;
            text-decoration: none;
        }

        footer {
            background-color: #1a365d;
            color: white;
            text-align: center;
            padding: 1.5rem;
            margin-top: 2rem;
        }
   
        .admin-form {
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }

        .admin-form h2 {
            color: #1a365d;
            margin-bottom: 15px;
        }

        .admin-form .form-group {
            margin-bottom: 15px;
        }

        .admin-form label {
            display: block;
            margin-bottom: 5px;
            color: #1a365d;
        }

        .admin-form input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .admin-form .submit-btn {
            width: 100%;
            padding: 10px;
            background-color: #1a365d;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .admin-form .submit-btn:hover {
            background-color: #0a1f38;
        }

        .error {
            color: red;
            margin-bottom: 15px;
        }

        .success {
            color: green;
            margin-bottom: 15px;
        }
    </style>
</head>
<body>
    <header>
        <h1>Panneau d'Administration</h1>
        <a href="logout.php" class="logout-btn">Déconnexion</a>
    </header>

    <div class="container">
        
        <div class="admin-form">
            <h2>Ajouter un Administrateur</h2>
            <?php if (!empty($admin_error)): ?>
                <div class="error"><?php echo htmlspecialchars($admin_error); ?></div>
            <?php endif; ?>
            <?php if (!empty($admin_success)): ?>
                <div class="success"><?php echo htmlspecialchars($admin_success); ?></div>
            <?php endif; ?>
            <form method="post" action="">
                <input type="hidden" name="add_admin" value="1">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                </div>
                <div class="form-group">
                    <label for="password">Mot de passe</label>
                    <input type="password" id="password" name="password" required>
                </div>
                <div class="form-group">
                    <label for="confirm_password">Confirmer le mot de passe</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                </div>
                <button type="submit" class="submit-btn">Ajouter un Administrateur</button>
            </form>
        </div>


        <div class="action-buttons">
            <a href="../tp10/View/nouvel_etudiant.php" class="action-btn">Ajouter un Étudiant</a>
            <a href="../tp10/View/nouvelle_matiere.php" class="action-btn">Ajouter une Matière</a>
        </div>
        
        <section class="table-section">
            <h2>Liste des Étudiants</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Prénom</th>
                        <th>Nom</th>
                        <th>Classe</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($etudiants as $e): ?>
                    <tr>
                        <td><?= htmlspecialchars($e['id']) ?></td>
                        <td><?= htmlspecialchars($e['prenom']) ?></td>
                        <td><?= htmlspecialchars($e['nom']) ?></td>
                        <td><?= htmlspecialchars($e['classe_nom']) ?></td>
                        <td class="action-column">
                            <a href="../tp10/View/modif_etudiant.php?id=<?= $e['id'] ?>">Modifier</a>
                            <a href="../tp10/View/suppression_etudiant.php?id=<?= $e['id'] ?>" onclick="return confirm('Voulez-vous vraiment supprimer cet étudiant ?');">Supprimer</a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </section>
    </div>

    <footer>
        <p>&copy 2025-Hugo Vialaneix</p>
    </footer>
</body>
</html>