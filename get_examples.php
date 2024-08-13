<?php 
$host= 'localhost';
$dbname = 'dictionnaire';
$user = 'root';
$pass = '';

try{
    $pdo = new PDO("mysql:host=$host;dbname=$dbname; charset=utf8", $user, $pass);

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT example_francais, example_bariba FROM traductions");
    $stmt->execute();
    $examples = [];

    while($row = $stmt->fetch(PDO::FETCH_ASSOC)){
        if(!empty($row['example_francais'])){
            $examples[] = $row['example_francais'];
        }
    }

    echo json_encode(['examples' => $examples]);
} catch(PDOException $e){
    echo json_encode(['error' => $e->getMessage()]);
}

?>









