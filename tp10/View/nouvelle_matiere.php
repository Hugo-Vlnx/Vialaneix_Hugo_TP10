<?php
session_start();
require('../Model/pdo.php');

if (!isset($_SESSION['user_id'])) {
    header('Location: ../login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['libelle'])) {
    $libelle = htmlspecialchars($_POST['libelle']);
    
    try {
        $requete = $dbPDO->prepare("INSERT INTO matiere (lib) VALUES (:libelle)");
        $requete->execute([':libelle' => $libelle]);
        $success = "Matière ajoutée avec succès !";
    } catch (PDOException $e) {
        $error = "Erreur : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Ajouter une matière</title>
    <style>
        
        body { font-family: 'Arial', sans-serif; background-color: #f0f2f5; padding: 20px; }
        .form-container { max-width: 500px; margin: 30px auto; padding: 25px; background: white; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Ajouter une nouvelle matière</h1>
        
        <?php if (isset($success)): ?>
            <div class="message success"><?= $success ?></div>
        <?php endif; ?>
        
        <?php if (isset($error)): ?>
            <div class="message error"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="libelle">Nom de la matière</label>
                <input type="text" id="libelle" name="libelle" required>
            </div>
            
            <button type="submit">Enregistrer</button>
        </form>
        
        <a href="../index.php" class="back-link">Retour à l'accueil</a>
    </div>
</body>
</html>