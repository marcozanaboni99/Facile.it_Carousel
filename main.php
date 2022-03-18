<?php

    define("TARGET_MONTH_FILE", "CarouselTargetMonth.json");
    define("CAROUSEL_DATA_FILE", "CarouselData.json");
    define("CAROUSEL_TARGET_CONFIG_FILE", "CarouselTargetConfig.json");
    define("CAROUSEL_RECOMPENSE_FILE", "CarouselRecompense.json");

    // Carico i dati dai file Json
    $carouselData = getJsonContent(CAROUSEL_DATA_FILE);
    $carouselTargetConfig = getJsonContent(CAROUSEL_TARGET_CONFIG_FILE);
    $carouselTargetMonth = getJsonContent(TARGET_MONTH_FILE);
    // Creo l'array contenente i dati delle ricompense
    $carouselRecompense = array();

    // Carico la configurazione delle soglie target corrispondente al mese selezionato
    $validConfig = getValidConfig($carouselTargetConfig,$carouselTargetMonth->targetMonth);

    // Per ogni giostraio calcolo la sua ricompensa
    for($i = 0; $i<count($carouselData);$i++){
        calculateCarouselRecompense($carouselTargetConfig[$validConfig],$carouselData[$i]);
    }

    // Scrivo le ricompense nel file di output
    if(putJsonContent(CAROUSEL_RECOMPENSE_FILE,$carouselRecompense) == true){
        echo "Process complete: check output in ".CAROUSEL_RECOMPENSE_FILE;
    }

    // ### Funzione che calcola la ricompensa
    function calculateCarouselRecompense($targetConfig, $carouselData){
        global $carouselRecompense;
        $fascia = -1;
        // Per ogni soglia target controllo se i dati del giostraio corrispondono ad essa
        for($i = count($targetConfig->targets) - 1; $i >= 0; $i--){
            // Gestisco il caso in cui ci siano oggetti vuoti o con proprietà mancanti
            if(!isset($carouselData->tokensSold) || !isset($carouselData->carouselRides) || !isset($carouselData->avgTokenPrice)) die("Empty Json property found in ".CAROUSEL_DATA_FILE);
            if(!isset($targetConfig->targets[$i]->tokensSold) || !isset($targetConfig->targets[$i]->carouselRides) || !isset($targetConfig->targets[$i]->avgTokenPrice)) die("Empty Json property found in ".CAROUSEL_TARGET_CONFIG_FILE);
            if($carouselData->tokensSold > $targetConfig->targets[$i]->tokensSold && $carouselData->carouselRides > $targetConfig->targets[$i]->carouselRides && $carouselData->avgTokenPrice > $targetConfig->targets[$i]->avgTokenPrice){
                $fascia = $i;
                break;
            }
        }

        // Creo un oggetto della classe CarouselRecompense per ogni giostraio
        if($fascia == -1){
            // Se non ho trovato nessuna soglia target corrispondente ai dati del giostraio forzo la ricompensa a 0
            $recompense = new CarouselRecompense($carouselData->carouselName,$fascia,$carouselData->tokensSold,0);
        } else {
            // Altrimenti scrivo il dato ricavato dalla configurazione delle soglie target
            $recompense = new CarouselRecompense($carouselData->carouselName,$fascia,$carouselData->tokensSold,$targetConfig->targets[$fascia]->recompenseForTokenSold);
        }
        // Aggiungo l'oggetto all'array contenente le ricompense
        array_push($carouselRecompense,$recompense);
    }

    // ### Funzione che restituisce la configurazione della soglia target corrispondente alla data configurata
    function getValidConfig($carouselTargetConfig,$carouselTargetMonth){
        // Per ogni configurazione, ritorno il suo indice se il valore startDate è uguale alla data inserita nel file
        for($validConfig = 0; $validConfig<count($carouselTargetConfig);$validConfig++){
            if(!isset($carouselTargetConfig[$validConfig]->startDate)){
                die("No startDate property found in file ".CAROUSEL_TARGET_CONFIG_FILE);
            }
            if($carouselTargetConfig[$validConfig]->startDate == $carouselTargetMonth){
                return $validConfig;
            }
        }
        // Gestisco il caso in cui non ci siano configurazioni con la data desiderata
        die("No configuration found for given month in " . CAROUSEL_TARGET_CONFIG_FILE);
    }

    // ### Funzione che legge il contenuto di un file Json
    function getJsonContent($jsonFilePath){
        // Se esiste il file, memorizzo il suo contenuto
        if(file_exists($jsonFilePath)){
            $fileContent = file_get_contents($jsonFilePath);
        } else{
            // Gestisco il caso in cui non ci sia il file desiderato
            die("File $jsonFilePath not found.");
        }
        // Decodifico la stringa Json
        $jsonData = json_decode($fileContent);
        // Se non ci sono errori nella decodifica restituisco i dati in formato Json
        if(json_last_error() === JSON_ERROR_NONE){
            return $jsonData;
        } else {
            // Gestisco il caso in cui ci sia un errore nella decodifica nel file Json
            die("Error decoding Json data. File: ".$jsonFilePath);
        }
    }

    // ### Funzione che scrive il contenuto di un file in formato Json
    function putJsonContent($jsonFileName,$jsonData){
        // Se il processo di scrittura del file va a buon fine restituisco true
        if(file_put_contents($jsonFileName,json_encode($jsonData))){
            return true;
        } else {
            // Gestisco il caso in cui ci sia un errore nella scrittura del file
            die("Error writing file.");
        }
    }

    // ### Classe che descrive la ricompensa di ogni giostraio
    class CarouselRecompense {

        // Dichiaro le proprietà della classe
        public $reachedTarget, $tokensSold, $recompenseForTokenSold, $recompense;

        // Costruttore della classe
        function __construct($carouselName,$reachedTarget,$tokensSold,$recompenseForTokenSold){

            // Assegno i valori all'oggetto creato
            $this->carouselName = $carouselName;
            $this->reachedTarget = ++$reachedTarget;
            $this->tokensSold = $tokensSold;
            $this->recompenseForTokenSold = $recompenseForTokenSold;
            // Calcolo la ricompensa arrotondando a 2 cifre decimali
            $this->recompense = round($this->recompenseForTokenSold*$this->tokensSold,2);

        }

    }

?>