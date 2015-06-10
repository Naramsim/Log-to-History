# Log to Histroy
---

Indice

[TOC]



## Introduzione

Log to history è uno strumento che permette di analizzare le visite di un determinato sito.
Esso è composto da tre grafici che vengono costruiti andando an analizzare un file, noto come access.log, che registra tutte le visite da parte di utenti su un determinato sito web. Di questi grafici i primi due tendono a dare una vista microscopica, ovvero sono più efficaci per analizzare brevi periodi di tempo, mentre l'ultimo grafico è adattabile ad un qualisasi periodo, sia di poche ore sia di più giorni.
I tre grafici hanno i seguenti nomi: tree, flow e stack. 

### Tree
Tree mostra un albero che si sviluppa lateralmente, esso fa combaciare ad ogni visitatore un set di pagine che sono state visualizzate _direttamente_ dall'utente, direttamente significa che l'utente proviene da un altro sito, come Google, Bing e altri, o che ha caricato il sito dalla barra degli indirizzi del Browser. Ogni visita diretta è dunque rappresentata da un nodo, che ha come figli le pagine visitate dall'utente che ha cliccato su un link del sito che si sta analizzando. Questo grafico rappresenta la cronologia di un utente calcolata secondo i movimenti via link.

### Flow
Flow è un diagramma di flusso che sviluppa verticalmente, il suo scopo è quello di mostrare i cambiamenti di pagina di un utente. In Flow ci sono tante colonne quante cartelle ci sono su un sito, all'interno di queste colonne  sono rappresentati i visitatori come linee verticali, quando una linea cambia colonna significa che il visitatore ha cambiato pagina durante la sua navigazione. E' bene precisare che l'analisi in questo grafico non comprende tutte le pagine di un sito ma solo le cartelle dove sono residenti le pagine web. Questo significa che se un visitatore è sulla pagina [http://sito/cartella/index.html](http://sito/cartella/index.html "URL della pagina"), il grafico mostrerà l'utente come se stesse visitando [cartella/](http://sito/cartella/ "Cartella dove risiede la pagina web"). 

### Stack
Stack è un grafico che si concentra sulle visite non tenendo conto di chi ha fatto la visita. Esso mostra un grafico ad aree sovrapposte, ogni area di colore diverso rappresenta il quantitativo di visite su una determinata cartella nel tempo. Anche questo grafico usa le cartelle al posto delle singole pagine web, per non essere troppo particolareggiato ma più generale possibile. La sovrapposizione delle varie aree permette inoltre di visualizzare anche il numero complessivo di utenti su tutto il sito in un dato istante.

## Funzionamento
I grafici proposti all'utente sono creati _online_, ovvero quando l'utente li chiede, così da circonscrivere solo il periodo che l'utente vuole analizzare.
Il lavoro è spartito fra server(il sito) e client(l'utente), il server analizza il file di accesso al sito(access.log), seleziona il periodo richisto dall'utente e prepara per il client un file di piccole dimensioni, così da rendere il download veloce, che sarà ri-analizzato e renderizzato dal browser. 
Il lavoro del server è fatto da due programmi, Python e PHP. Python analizza il file di log e crea il file per il client, mentre PHP fa da ponte tra server e client.

### Lato Server
Il server è il computer o la macchina virtuale dove risiede il sito web. Per funzionare è neccessario che su di esso siano installati sia Python 2.7 sia PHP >5.3 sia un server web come Apache2, Nginx, Lighttpd. Se si usa node.js come server web vedere [Troubleshots](#troubleshots).
Il primo programma ad essere interpellato è PHP tramite tramite una richiesta AJAX da parte del browser che porta la data di inizio e di fine scansione del log richesta dall'utente. PHP tramite il comando `ob_start()` e `system()` chiama uno script Python(main.py) con tre parametri, le due date e il tipo di grafico richesto dall'utente. Una volta eseguito lo script PHP invia al browser una stringa vuota se main.py ha avuto successo, una stringa "fail", se c'è stato un errore e non si possono visualizzare i dati. Durante la chiamata `system()` viene eseguito main.py, script fondamentale.

#### main.py



### Lato Client

## Troubleshots

### Generare un access.log su server web alternativi
* [Node.js log](https://github.com/petershaw/NodeJS-Apache-Like-AccessLog)
* [Node.js log](https://www.npmjs.com/package/apache-log)
* [Django log]()
