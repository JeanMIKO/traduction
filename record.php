<?php 
$targetDir = "uploads/";
if(!file_exists($targetDir)){
    mkdir($targetDir, 0777, true);
}
$audioFile = $targetDir . "audio_" . time() . ".wav";

if (move_uploaded_file($_FILES['audio'] ['tmp_name'], $audioFile)){
    try{
        $host= 'localhost';
        $dbname = 'dictionnaire';
        $user = 'root';
        $pass = '';
        $pdo = new PDO("mysql:host=$host;dbname=$dbname; charset=utf8", $user, $pass);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt= $pdo->prepare("INSERT INTO audios (nomfichier, filepath) VALUES (:nomfichier, :filepath)");
        $stmt->execute(['nomfichier' => basename($audioFile), 'filepath' => $audioFile]);

        $transcription = "Votre vocale a été bien enregistré";
        echo json_encode(['transcription' => $transcription]);
    } catch(PDOException $e){
        echo json_encode(['error' => $e->getMessage()]);
    }
    
} else {
    echo json_encode(['error' => 'Echec de l\'enregistrement de l\'audio']);
}

?>