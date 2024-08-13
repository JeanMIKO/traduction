<?php 
$host= 'localhost';
$dbname = 'dictionnaire';
$user = 'root';
$pass = '';

$jsonFilePath = 'dictionnaire.json';

try{
    $pdo = new PDO("mysql:host=$host;dbname=$dbname; charset=utf8", $user, $pass);

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    //Vérication de l'existence du fichier json
    if(!file_exists($jsonFilePath)){
        throw new Exception("Fichier JSON non trouvé : $jsonFilePath");
    }

    //Lecture du fichier json

    $json = file_get_contents($jsonFilePath);
    if($json === false){
        throw new Exception("Impossible de lire le fichier JSON");
    }
    //Décodage du fichier json
    $data = json_decode($json, true);
    if($data === null){
        throw new Exception("Erreur de décodage JSON: " . json_last_error_msg());
    }

    //Importation des données du fichier dans la base de données

    foreach($data as $entry) {
        $word = $pdo->quote($entry['word']);
        $phonetic = $pdo->quote($entry['phonetic']);
        $part_of_speech = $pdo->quote($entry['part_of_speech']);
        $definitions = $pdo->quote($entry['definition']);
        $example_bariba = is_array($entry['example_bariba'])
        ? $pdo->quote(implode(', ', $entry['example_bariba'])): $pdo->quote($entry['example_bariba']);
        $example_francais = is_array($entry['example_francais']) 
        ? $pdo->quote(implode(', ', $entry['example_francais'])): $pdo->quote($entry['example_francais']);

        $sql = "INSERT INTO traductions (word, phonetic, part_of_speech, definitions, example_bariba, example_francais) VALUES
        ($word, $phonetic, $part_of_speech, $definitions, $example_bariba, $example_francais)";

        $pdo-> exec($sql);

    }

    echo "Données importées avec succès";

} catch(PDOException $e){
    echo "Erreur PDO: " . $e->getMessage();
} catch(Exception $e){
    echo "Erreur:" . $e->getMessage();
}

?>











