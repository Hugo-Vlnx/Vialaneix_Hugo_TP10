<?php
session_start();
require('../Model/pdo.php');


if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: ../index.php');
    exit();
}

$id = intval($_GET['id']);


$etudiant = $dbPDO->prepare("SELECT * FROM etudiants WHERE id = ?");
$etudiant->execute([$id]);
$etudiant = $etudiant->fetch();

if (!$etudiant) {
    header('Location: ../index.php');
    exit();
}


$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = htmlspecialchars(trim($_POST['nom']));
    $prenom = htmlspecialchars(trim($_POST['prenom']));
    $classe_id = intval($_POST['classe_id']);

    if (empty($nom) || empty($prenom)) {
        $error = "Le nom et le prénom sont obligatoires";
    } else {
        try {
            $update = $dbPDO->prepare("UPDATE etudiants SET nom = ?, prenom = ?, classe_id = ? WHERE id = ?");
            $update->execute([$nom, $prenom, $classe_id, $id]);
            $success = "Étudiant modifié avec succès";
            
            
      $requete = $dbPDO->prepare("SELECT * FROM etudiants WHERE id = :id");


      $requete->bindParam(':id', $id, PDO::PARAM_INT);


      $requete->execute();
 

         $etudiant = $requete->fetch(PDO::FETCH_ASSOC);  
        } catch (PDOException $e) {
            $error = "Erreur lors de la modification : " . $e->getMessage();
        }
    }
}


$classes = $dbPDO->query("SELECT * FROM classes")->fetchAll();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un étudiant</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f0f2f5;
            padding: 20px;
            margin: 0;
        }
        .container {
            max-width: 800px;
            margin: 30px auto;
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #1a365d;
            text-align: center;
            margin-bottom: 30px;
        }
        .form-group {
            margin-bottom: 20px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            color: #1a365d;
            font-weight: bold;
        }
        input[type="text"],
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 16px;
        }
        button {
            background-color: #1a365d;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            width: 100%;
            margin-top: 10px;
        }
        button:hover {
            background-color: #0a1f38;
        }
        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            text-align: center;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #1a365d;
            text-decoration: none;
            text-align: center;
            width: 100%;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Modifier l'étudiant</h1>
        
        <?php if (!empty($error)): ?>
            <div class="message error"><?= $error ?></div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="message success"><?= $success ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="prenom">Prénom</label>
                <input type="text" id="prenom" name="prenom" value="<?= htmlspecialchars($etudiant['prenom']) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="nom">Nom</label>
                <input type="text" id="nom" name="nom" value="<?= htmlspecialchars($etudiant['nom']) ?>" required>
            </div>
            
            <div class="form-group">
                <label for="classe_id">Classe</label>
                <select id="classe_id" name="classe_id" required>
                    <?php foreach ($classes as $classe): ?>
                        <option value="<?= $classe['id'] ?>" <?= $classe['id'] == $etudiant['classe_id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($classe['libelle']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <button type="submit">Mettre à jour</button>
        </form>
        
        <a href="../index.php" class="back-link">Retour à la liste des étudiants</a>
    </div>
</body>
</html>