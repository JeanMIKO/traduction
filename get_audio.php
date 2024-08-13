<?php 
$targetDir = "uploads/";
$files = glob($targetDir . "audio_*.wav");
if($files){
    $latestFile = end($files);
    if(file_exists($latestFile)){
        header('Content-Type: audio/wav');
        readfile($latestFile);
    } else{
        echo json_encode(['error' => 'Fichier audio non trouvé']);
    }
} else{
    echo json_encode(['error' => 'Aucun fichier audio trouvé']);
}




?>