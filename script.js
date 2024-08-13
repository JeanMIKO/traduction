let mediaRecorder;
let audioChunks= [];
let currentIndex = 0;
let examples = [];
let audioPlayed = false; 
let hasRecorded = false; 


//Fonction pour la traduction
function translateText(targetLang){
    const text = document.getElementById('textInput').value;

    fetch('http://localhost:8081/Dictionary1/translate.php', {
        method: 'POST',
        headers: {'Content-Type': 'application/json'},
        body: JSON.stringify({text: text, targetLang: targetLang })
    })
    .then(response => response.json())
    .then(data => {
        console.log('Réponse du serveur:', data); // Pour déboguer
        document.getElementById('resultText').value= data.translatedText || "Erreur de traduction.";
    })
    .catch(error => {
        console.error('Erreur de traduction:', error);

        document.getElementById('resultText').value = "Erreur de traduction.";
    });
}

//Fonction pour commencer l'audio
function startRecording(){
    if(navigator.mediaDevices && navigator.mediaDevices.getUserMedia){
        navigator.mediaDevices.getUserMedia({
            audio: true })
            .then(stream => {
                mediaRecorder= new MediaRecorder(stream);

                mediaRecorder.ondataavailable= event => {
                    audioChunks.push(event.data);
                };

                mediaRecorder.start();
                hasRecorded = true; // Marque qu'un enregistrement a été fait
                document.getElementById('playButton').disabled = false;
                audioPlayed = false;
                
            })
            .catch(error => console.error('Erreur d\'accès au microphone', error));
    } else {
        console.error('Les dispositifs multimédia ne sont pas pris en charge');
    }
}

//Fonction pour arrêter l'enregistrement et envoyer le fichier audio
function stopRecording(){
    if(mediaRecorder){
        mediaRecorder.stop();
        mediaRecorder.onstop = () => {
            const audioBlob = new Blob(audioChunks, { type: 'audio/wav' });
            const formData = new FormData();
            formData.append('audio', audioBlob);

            fetch('http://localhost:8081/Dictionary1/record.php', {
                method: 'POST',
                body:formData
            })
            .then(response => response.json())
            .then (data => {
                document.getElementById('resultText').value = data.transcription;
                audioChunks = [];
            });
        };
    }
}

//Fonction pour lire le dernier fichier audio enregistré

function playAudio(){
    // Vérifiez si un enregistrement a été fait dans la session actuelle
    if (!hasRecorded) {
        alert("Veuillez d'abord enregistrer un audio avant de l'écouter.");
        return;
    }

    // Jouer l'audio si l'utilisateur a enregistré
    if(!audioPlayed){
        fetch('http://localhost:8081/Dictionary1/get_audio.php')
        .then(response => response.blob())
        .then(blob => {
            const url = URL.createObjectURL(blob);
            const audio = new Audio(url);
            audio.play();
            audioPlayed = true;
        });

    }
   
}

//Fonction pour valider et enregistrer l'audio dans la base de données et désactiver le button Ecouter
function validate(){
    fetch('http://localhost:8081/Dictionary1/validate.php', {
        method: 'POST'
    })
    .then(response => response.json())
    .then(data => {
        if(data.success){
            console.log('Audio validé et enregistré.');
        } else{
            console.error('Erreur lors de la validation de l\'audio:', data.error);
        }
    })
    .catch(error => console.error('Erreur lors de la validation de l\'audio:', error));

    document.getElementById('playButton').disabled = true;
    hasRecorded = false; // Réinitialiser après la validation

}

//charger les examples depuis la base de données
function loadExamples(){
    fetch('http://localhost:8081/Dictionary1/get_examples.php')
    .then(response => response.json())
    .then(data => {
        examples = data.examples;
        if(examples.length > 0){
            showNextExample(); //Afficher le premier exemple
        } else{
            document.getElementById('exampleText').value = "Aucune phrase disponible.";
        }
    })
    .catch(error => {
        console.error('Erreur lors du chargement des phrases:', error);
    });
}

//Afficher l'exemple suivant
function showNextExample(){
    if(examples.length > 0){
        document.getElementById('exampleText').value = examples[currentIndex];
        currentIndex = (currentIndex + 1) % examples.length;
    }
}

//Charger les exemples au chargement de la page
window.onload = loadExamples;



























































