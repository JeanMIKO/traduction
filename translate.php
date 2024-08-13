<?php 
header("Access-Control-Allow-Origin:*");
header("Content-Type: application/json");

$host= 'localhost';
$dbname = 'dictionnaire';
$user = 'root';
$pass = '';

try{
    $pdo = new PDO("mysql:host=$host;dbname=$dbname; charset=utf8", $user, $pass);

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $data = json_decode(file_get_contents('php://input'), true);
    $text = $data['text'];
    $targetLang = $data['targetLang'];

    
    if($targetLang == 'francais'){
        //Rechercher dans la colonne definitions 
        $query = "SELECT definitions FROM traductions WHERE word LIKE :text";
       
    } else {
        //Rechercher dans la colonne word 
        $query = "SELECT word FROM traductions WHERE definitions LIKE  :text ";
    }

    
    $stmt = $pdo->prepare($query);
    $stmt->execute(['text' => "%$text%"]);
    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $translatedText = $result ? array_values($result)[0] : "Aucune traduction trouvé";

    echo 
    json_encode(['translatedText' => $translatedText]);
} catch(PDOException $e){
    echo json_encode(['error' => $e->getMessage()]);
}


?>


