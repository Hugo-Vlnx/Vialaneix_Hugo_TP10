<?php
require('../Model/pdo.php');


if (isset($_GET['id'])) {
    $id = $_GET['id'];

   
    $requete = $dbPDO->prepare("SELECT * FROM etudiants WHERE id = :id");
    $requete->bindParam(':id', $id);
    $requete->execute();
    $etudiant = $requete->fetch();

    if ($etudiant) {
     
        $delete = $dbPDO->prepare("DELETE FROM etudiants WHERE id = :id");
        $delete->bindParam(':id', $id);
        
        if ($delete->execute()) {
            echo"<br>"."Suppression de l'étudiant réussie.";
        } else {
            echo "Erreur lors de la suppression : ";
            print_r($delete->errorInfo());
        }
    }
    
}
?>

<br>
<a href="../index.php">Retourner à l'index</a>
