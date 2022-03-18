# Facile.it_Carousel
La repository contiene i sorgenti della soluzione sviluppata per risolvere il problema esplicitato nella descrizione dell'[esercizio](https://bitbucket.org/faciledotit/interview-test/src/master/README.md)

## Specifiche tecniche
- Il software è stato sviluppato utilizzando il linguaggio PHP 7.4

## Descrizione della struttura: input e output
- Il software utilizza sia come metodo di input che come metodo di output file di tipo Json.

### File di input
- _**CarouselTargetMonth.json**_: il file contiene, nelle modalità scritte di seguito, il mese per il quale si richiede al software il calcolo dei compensi.
```javascript
{
    "targetMonth": "2018-11-01"
}
```
L'oggetto che contiene il mese di riferimento è formato da un unico field:
| Field     | Type      | Description                                                                                                                                                                                     |
| --------- | -------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| targetMonth | Date           | Indica il mese per cui si richiede il calcolo del compenso. E' sempre nel formato Y-m-d e **il giorno è sempre 1**, poichè le configurazioni hanno sempre validità mensile. |

- _**CarouselTargetConfig.json**_: il file contiene, nelle modalità già riportate nel README linkato sopra, la configurazione delle soglie target. Il file è composto da un array contenente tanti oggetti quanti sono i mesi per cui si vuole descrivere la configurazione delle soglie target. Si assume che il raggiungimento di un target avvenga al superamento della soglia impostata per i 3 valori di riferimento.
**Il software non pone limiti al numero di configurazioni target per ogni mese, purchè vengano inserite nel file in ordine crescente di: gettoni venduti, prezzo medio per gettone e giri totali della giostra.**

- _**CarouselData.json**_: il file contiene, nelle modalità scritte di seguito, i dati mensili di ogni giostraio.
```javascript
{  
    "tokensSold": 200,
    "carouselRides": 211,
    "avgTokenPrice": 1.10,
    "carouselName": "Manuela"
}
```
L'oggetto che contiene le statistiche mensili di ogni giostraio è formato dai seguenti fields:
| Field     | Type      | Description                                                                                                                                                                                     |
| --------- | -------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| tokensSold | Integer           | Numero di gettoni venduti nel mese di riferimento |
| carouselRides   | Integer  | Numero di giri effettuati dalla giostra nel mese di riferimento |
| avgTokenPrice | Float | Prezzo medio del singolo gettone nel mese di riferimento |
| carouselName | String | Nome del giostraio |

Il file è composto da un array contenente tanti oggetti quanti sono i giostrai per cui si vuole calcolare il compenso.

**Si assume che venga generato un file diverso per ogni mese di riferimento. Non è prevista la coesistenza di più statistiche dello stesso giostraio all'interno del file per mesi diversi.**

### File di output
- _**CarouselRecompense.json**_: il file, che viene generato e sovrascritto ad ogni esecuzione del programma, contiene, nelle modalità scritte di seguito, i dati relativi al compenso per ogni giostraio presente nel file di configurazione dato in input.
```javascript
{
    "reachedTarget": 1,
    "tokensSold": 200,
    "recompenseForTokenSold": 0.1,
    "recompense": 20,
    "carouselName": "Manuela"
}
```
L'oggetto che contiene i dati relativi al compenso per ogni giostraio è formato dai seguenti fields:
| Field     | Type      | Description                                                                                                                                                                                     |
| --------- | -------------- | ----------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| reachedTarget | Integer           | Soglia target raggiunta dal giostraio, dipendente dall'ordine in cui sono state inserite nel file di configurazione. In caso di mancato raggiungimento della soglia minima, viene scritto valore 0. |
| tokensSold   | Integer  | Numero di gettoni venduti dal giostraio nel mese di riferimento. Dato inserito per completezza di informazioni. |
| recompenseForTokenSold | Float | Compenso che spetta al giostraio per ogni gettone venduto nel mese di riferimento. Dato inserito per completezza di informazioni. |
| recompense | Float | Compenso calcolato dal software che spetta al giostraio, utilizzando i valori riportati precedentemente. |
| carouselName | String | Nome del giostraio |

Il file è composto da un array, contenente tanti oggetti quanti sono i giostrai per cui viene calcolato il compenso mensile. 

### Modalità di esecuzione ed utilizzo
Per utilizzare il software è necessario che:
- I file di input si trovino nella stessa directory del file main.php
- Si abbiano i permessi di scrittura nella directory in cui viene eseguito il programma, per permettere la generazione del file di output.

Una volta rispettati i requisiti riportati sopra, è sufficiente eseguire il file main.php senza alcun parametro o opzione. L'esecuzione del programma permette, come richiesto dal requisito, di calcolare il compenso mensile per ogni giostraio in base a delle configurazioni basate su target.
