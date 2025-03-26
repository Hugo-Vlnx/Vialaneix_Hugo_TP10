<?php 
session_start();
require('../Model/pdo.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nom = htmlspecialchars($_POST['nom']);
    $prenom = htmlspecialchars($_POST['prenom']);
    $classe_id = intval($_POST['classe_id']);

    try {
        $requete = $dbPDO->prepare("INSERT INTO etudiants (nom, prenom, classe_id) VALUES (:nom, :prenom, :classe_id)");
        $requete->execute([
            ':nom' => $nom,
            ':prenom' => $prenom,
            ':classe_id' => $classe_id
        ]);
        
        $success = "Étudiant ajouté avec succès !";
    } catch (PDOException $e) {
        $error = "Erreur : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter un étudiant</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f0f2f5;
            padding: 20px;
        }
        .form-container {
            max-width: 500px;
            margin: 30px auto;
            padding: 25px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #1a365d;
            text-align: center;
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
        input, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
            box-sizing: border-box;
        }
        button {
            background-color: #1a365d;
            color: white;
            padding: 12px 20px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 100%;
            font-size: 16px;
        }
        button:hover {
            background-color: #0a1f38;
        }
        .message {
            padding: 10px;
            margin: 20px 0;
            border-radius: 4px;
            text-align: center;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
        }
        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #1a365d;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Ajouter un nouvel étudiant</h1>
        
        <?php if (isset($success)): ?>
            <div class="message success"><?= $success ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="message error"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="prenom">Prénom</label>
                <input type="text" id="prenom" name="prenom" required>
            </div>
            
            <div class="form-group">
                <label for="nom">Nom</label>
                <input type="text" id="nom" name="nom" required>
            </div>
            
            <div class="form-group">
                <label for="classe_id">Classe</label>
                <select id="classe_id" name="classe_id" required>
                    <?php 
                    $classes = $dbPDO->query("SELECT * FROM classes")->fetchAll();
                    foreach ($classes as $classe): 
                    ?>
                        <option value="<?= $classe['id'] ?>"><?= htmlspecialchars($classe['libelle']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <button type="submit">Enregistrer</button>
        </form>
        
        <a href="../index.php" class="back-link">Retour à l'accueil</a>
    </div>
</body>
</html>