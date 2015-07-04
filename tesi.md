# Log to History
Uno strumento di analisi rivolto al web. 
<!-- START doctoc generated TOC please keep comment here to allow auto update -->
<!-- DON'T EDIT THIS SECTION, INSTEAD RE-RUN doctoc TO UPDATE -->
Indice:
- [Introduzione](#introduzione)
  - [Altri analizzatori open source](#altri-analizzatori-open-source)
  - [Tree](#tree)
  - [Flow](#flow)
    - [Nota su tree e flow](#nota-su-tree-e-flow)
  - [Stack](#stack)
- [Sviluppo](#sviluppo)
  - [Fase iniziale](#fase-iniziale)
  - [Fase di sviluppo](#fase-di-sviluppo)
  - [Fase di testing](#fase-di-testing)
- [Funzionamento](#funzionamento)
  - [Lato Server](#lato-server)
    - [Il ruolo di PHP](#il-ruolo-di-php)
    - [main.py](#mainpy)
  - [Lato Client](#lato-client)
    - [tree_graph.js](#tree_graphjs)
    - [flow_chart.js](#flow_chartjs)
    - [stack_chart.js](#stack_chartjs)
    - [Interfaccia grafica](#interfaccia-grafica)
- [Ottimizzazioni](#ottimizzazioni)
- [Troubleshooting](#troubleshooting)
  - [Generare un access.log su server web alternativi](#generare-un-accesslog-su-server-web-alternativi)
  - [Installare la libreria python-dateutil](#installare-la-libreria-python-dateutil)
  - [Nascondere config.json al pubblico](#nascondere-configjson-al-pubblico)
- [Statistiche](#statistiche)
- [Conclusioni](#conclusioni)

<!-- END doctoc generated TOC please keep comment here to allow auto update -->



## Introduzione

Log to history è uno strumento che permette di analizzare le visite di un determinato sito. È stato sviluppato con l'aiuto di Norbert Paissan durante un periodo di tesi/tirocinio di 3 mesi.
È un software installabile su tutti i server web ed è scritto con i linguaggi Python, PHP e Javascript, il suo codice è open source e si può estendere o consultare al seguente [link](https://github.com/Naramsim/Log-to-History). 

Esso è composto da tre grafici che vengono costruiti andando ad analizzare un file, noto come access log, che registra tutte le visite da parte di utenti su un determinato sito web. Di questi grafici i primi due tendono a dare una vista microscopica, ovvero sono più efficaci per analizzare brevi periodi di tempo, mentre l'ultimo grafico è adattabile ad un qualsiasi periodo, sia di poche ore sia di più giorni. L'idea per la creazione di Log to History è venuta perché sul web mancano strumenti di analisi microscopica e di analisi del flusso di utenti. Lo scopo di Log to History, infatti, è riuscire ad analizzare come un utente naviga su un sito, ricostruendo la sua cronologia.
Nella prima fase del progetto sono stati analizzati altri software open source che analizzano le visite a siti web, di seguito viene presentata una lista dei più diffusi con i relativi punti di forza e lacune.

### Altri analizzatori open source

* [Piwik](https://github.com/piwik/piwik): Software avanzato che usa l’injection di codice js nelle pagine di un sito per il tracking degli utenti, è un logger alternativo a Google Analytics. Questo progetto è uno dei più diffusi e dispone di una grandissima comunità che lo aggiorna giornalmente.
* [Request-log-analyzer](https://github.com/wvanbergen/request-log-analyzer): Software scritto in Ruby che analizza tanti formati di file di log non solo access log, presentando quindi statistiche basilari. Fornisce le statistiche nel terminale e non dispone di alcuna interfaccia web.
* [Goaccess](https://github.com/allinurl/goaccess/): Software molto avanzato scritto in C che analizza file di log via terminale anche in real-time, può generare file html statici. Il suo punto di forza è la velocità di scansione dei file di log. 
* [Apache-scalp](https://github.com/neuroo/apache-scalp): Script python che mostra in terminale i possibili attacchi che ha subito un server web leggendo le richieste dell'access log, mostra solo gli attacchi, non altre statistiche. Genera un html statico molto prolisso e di difficile lettura. 
* [Http_log_analyzer](https://github.com/tmarly/http_log_analyzer): Analizza l’access log e fa il display dei risultati via web, manca di un analisi microscopica.
* [Http-logs-analyzer](https://github.com/flrnull/http-logs-analyzer): Script in c++ che analizza molto velocemente un file di log nel terminale e mostra a video una rappresentazione JSON con le statistiche. Non dispone di alcuna interfaccia web.
* [Nginx-mongo-logger](https://github.com/mikedamage/nginx-mongo-logger): Script che lavora in background e inserisce ogni nuova linea dell’access log in un database Mongo. Fondamentalmente non fornisce statistiche ma solo un database per costruirle, è quindi molto utile se si vuole costruire un analizzatore di access log che miri ad analizzare lunghissimi periodi come mesi o anni.
* [ServerLogStats](https://github.com/danielstjules/ServerLogStats): Analizza gli access log che gli utenti caricano, quindi non quello del server e mostra varie statistiche: è scritto in Javascript e lavora solo lato client.
* [Live-log-analyzer](https://github.com/saltycrane/live-log-analyzer): Software python che mostra via web in real-time statistiche sull’access log, richiede molte librerie installate sul server web e la sua installazione è molto complicata.


L'unica funzionalità che manca a tutti questi analizzatori è dunque l'analisi microscopica. Log to History infatti con i primi due grafici mostra la cronologia delle pagine visitate da singoli utenti, ricreando la storia di un utente su un sito web. È quindi uno strumento molto potente in quanto lascia intendere quando l'utente cambia pagina e che pagina gli viene servita.
I tre grafici di Log to History hanno i seguenti nomi: tree, flow e stack. 

### Tree
Tree mostra un albero che si sviluppa lateralmente, esso fa corrispondere ad ogni visitatore un set di pagine che sono state visualizzate _direttamente_ dall'utente, direttamente significa che l'utente proviene da un altro sito, come Google, Bing e altri, o che ha caricato il sito dalla barra degli indirizzi del browser. Ogni visita diretta è dunque rappresentata da un nodo, che ha come figli le pagine visitate dall'utente che ha cliccato su un link del sito che si sta analizzando. Questo grafico rappresenta la cronologia di un utente calcolata secondo i movimenti via link. Passando sopra ad ogni nodo che identifica un visitatore si può vedere l'user agent, se è identificato come un bot o un crawler il nodo verrà colorato di giallo. Mentre se si passa sopra una pagina visitata da un utente comparirà la data della visita.

![Tree Graph](http://i.imgur.com/mW1XgZb.png "Tree Graph")

Questo grafico è la rappresentazione di Tree, si vedono quindi molti IP, e se un IP viene cliccato si aprono le pagine che ha richiesto direttamente. In questo caso 7.50.142.24 (il penultimo IP analizzato) ha cominciato a visitare il sito dalla pagina _/_, che è l'home-page del sito, si è quindi spostato a _/atleta_ cliccando qualche link nella home-page, poi si è spostato su _atleta/Levi-Roche-Mandji/150970_. Nel caso di 94.32.224.201 (l'ultimo IP), nell'ultima parte della sua visita partendo da _/societa/1810_ egli ha richiesto 5 pagine cliccando su 5 link diversi probabilmente aprendo le nuove pagine come nuove tab del browser o ritornando a _/societa/1810_ cliccando il tasto indietro del browser. Il tasto indietro del browser infatti non richiede al sito una nuova pagina, ma mostra una copia prelevata dalla cache più recente.

### Flow
Flow è un diagramma di flusso che sviluppa verticalmente, il suo scopo è quello di mostrare i cambiamenti di pagina di un utente. In Flow ci sono tante colonne quante cartelle ci sono su un sito, all'interno di queste colonne sono rappresentati i visitatori come linee verticali, quando una linea cambia colonna significa che il visitatore ha cambiato pagina durante la sua navigazione. E' bene precisare che l'analisi in questo grafico non comprende tutte le pagine di un sito ma solo le cartelle dove sono residenti le pagine web. Questo significa che se un visitatore è sulla pagina _sito/cartella/index.html_, il grafico mostrerà l'utente come se stesse visitando _cartella/_. Vi è comunque la possibilità di visualizzare tutte le pagine del sito web impostando un parametro nel file di configurazione di Log to History. Il parametro è chiamato _folder\_level_, se impostato molto alto verranno messe a display tutte le pagine, utile per siti web piccoli o con poche sotto cartelle. Si ha quindi una generalizzazione di cosa i visitatori stanno navigando. È disponibile inoltre una casella di ricerca in cui si può cercare ed evidenziare un certo visitatore.

![Flow Graph](http://i.imgur.com/US2Slyk.png "Flow Graph")

In questo esempio si possono notare i visitatori di un sito del giorno 8 Giugno dalle 19.07 alle 19.10, è evidenziato un visitatore che è stato cercato tramite la search-box in alto a sinistra. Il visitatore è identificato dal suo indirizzo IP e la sua cronologia di un colore arancio. Si può capire che il visitatore ha abbandonato una pagina un _/confronto_ verso le 19.50 per andare su una pagina in _/atleta_, poi è ritornato su _/confronto_ e in fine è ritornato in _/atleta_.

#### Nota su tree e flow
Questi due grafici sono utili per avere una vista microscopica di un determinato periodo di tempo, che può andare dal minuto al massimo di un'ora. La logica di un intervallo così breve sta nel capire che se si analizzasse un periodo più lungo i grafici sarebbero troppo lunghi e non si capirebbe più molto il rendering. Vi è da dire anche che ad ogni riconnessione ad internet un utente può cambiare indirizzo IP, il che significa che è quasi impossibile rintracciare la storia di un utente in periodi lunghi. In stack invece l'utente non è più considerato e dunque l'analisi si può prolungare a qualsiasi periodo.

### Stack
Stack è un grafico che si concentra sulle visite non tenendo conto di chi ha fatto la visita. E' l'unico grafico che si discosta dagli altri. Esso mostra un grafico ad aree sovrapposte, ogni area di colore diverso rappresenta il quantitativo di visite su una determinata cartella nel tempo. Anche questo grafico usa le cartelle al posto delle singole pagine web, per non essere troppo particolareggiato e più generale possibile. La sovrapposizione delle varie aree permette inoltre di visualizzare anche il numero complessivo di utenti su tutto il sito in un dato istante. Sono presenti dei controlli nella parte superiore del grafico, a destra viene permesso di passare fra il quantitativo di visite alla percentuale delle visite cliccando il pulsante "expanded", mentre se viene selezionato "stream" i dati verranno organizzati attorno all'asse temporale e non solo al di sopra, creando un grafico organico e di flusso, le aree sovrapposte rimarranno tali ma sarà più semplice vedere come le visite ad ogni cartella cambino nel tempo, cosa che nella modalità predefinita "stacked" riesce difficile per le cartelle con poche visualizzazioni.

![Stack Chart](http://i.imgur.com/672XWzU.png "Stack Chart")

Sopra viene mostrato un esempio prolungato di uno stack chart, dalle tre di mattina fino alle venti di sera, si può notare come in generale gli utenti tendano a crescere durante la mattinata per poi stabilizzarsi. Vi sono anche alcuni picchi che sono dovuti ad attivitá di indicizzazione di spider e crawler. Si può notare anche che le pagine più richieste sono quelle che risiedono nella cartella _/atleta_.

![Stack Chart](http://i.imgur.com/IEy1Iti.png "Stack Chart")

In questo esempio viene analizzato invece solo un breve periodo di un'ora, e viene usato il metodo _Expanded_ che mostra la percentuale di utenti per ogni cartella del sito. Si può vedere che nella prima mezz'ora gli utenti sono prevalentemente su _/atleta_, mentre nella seconda mezz'ora tendono a crescere le visite su _/confronto_ e sulla root del sito (_/_), ovvero la home-page.

<br>
## Sviluppo

### Fase iniziale
Come fase iniziale prima ancora di precisare il nostro obiettivo abbiamo studiato e installato molti [analizzatori](#altri-analizzatori-open-source) di access log open source che si trovano in rete, li abbiamo confrontati e siamo giunti alla conclusione che l'unica cosa che mancava era uno strumento di analisi microscopica che tenesse conto dell'utente, che mostrasse come si muove all'interno di un sito. Da qui è nata l'idea per lo sviluppo di Log to History. 

Dopo aver scoperto il nostro obiettivo abbiamo cominciato a studiare il metodo migliore per realizzarlo, quali linguaggi usare, come visualizzare i dati, come interfacciarsi con l'utente. Siamo quindi arrivati ad utilizzare Python lato Server, per la sua semplicità e velocità. PHP  è stato usato come intermediario con il browser dell'utente, si sarebbe potuto riutilizzare Python come web-server attraverso dei framework (esempio: Django, Flask, Tornado) ma secondo [alcune statistiche](http://news.netcraft.com/archives/2015/05/19/may-2015-web-server-survey.html) la maggior parte degli utenti usa ancora la soluzione Apache/PHP come server web. Infine è stata usata la libreria basata su Javascript [D3](https://github.com/mbostock/d3) per il rendering dei dati su browser, grazie al fatto che essa crea grafici non statici direttamente nel browser dell'utente, diversamente da altre librerie che si limitano a fornire solo immagini di grafici.

### Fase di sviluppo
Durante la fase di sviluppo abbiamo usato come editor di codice [Sublime 3](https://www.sublimetext.com/3), come strumento di controllo versione Git assieme a Github, sul quale si trova tutto il [codice sorgente](https://github.com/Naramsim/Log-to-History/). Per testare il prodotto si è usato un [server](https://mtgfiddle.me/tirocinio/pezze/) su DigitalOcean con 512mb di Ram e un solo processore, nonostante queste sue caratteristiche a dir poco non eccezionali Log to History funziona bene e velocemente. Come access log ci sono stati forniti più file di log di un sito molto visitato: [Atletica.me](http://atletica.me/), realizzato da due studenti, Alessio Gorla e Federico Baldessari, frequentanti il mio stesso corso di Informatica. Questo sito permette di navigare fra tutti gli atleti d'Italia, confrontarne i risultati, seguire le gare a cui si sono iscritti e ricevere notifiche dei propri atleti preferiti. Per scrivere questo elaborato è stato usato il linguaggio [Markdown](http://daringfireball.net/projects/markdown/) che permette di scrivere in plain-text e successivamente di essere trasformato in un file HTML o in versione PDF attraverso il sito [GitPrint](https://gitprint.com/).

### Fase di testing
Dopo ogni aggiunta significativa al codice è avvenuta una fase di testing, fase che si è prolungata quando il programma è stato ultimato, dalla fase di testing si è poi passati ad [ottimizzare](#ottimizzazioni) il codice. La fase di testing ha previsto un confronto dei risultati di Log to History con i dati che venivano effettivamente forniti dall'access log. Si è testata inoltre l'installazione del software su altri server oltre che a quello di sviluppo.

## Funzionamento
I grafici proposti all'utente sono creati _online_, ovvero quando l'utente li richiede, così da circoscrivere solo il periodo che l'utente vuole analizzare.
Il lavoro è spartito fra server (il sito) e client (l'utente), il server analizza il file di accesso al sito (access.log), seleziona il periodo richiesto dall'utente e prepara per il client un file di piccole dimensioni in formato JSON, così da rendere il download veloce, che sarà ri-analizzato e renderizzato dal browser. 
Il lavoro del server è fatto da due programmi, Python e PHP. Python analizza il file di log e crea il file per il client, mentre PHP fa da ponte tra server e client. Il client invece necessita solo di un browser.

### Lato Server
Il server è il computer o la macchina virtuale dove risiede il sito web. Per far funzionare Log to History è necessario che su di esso siano installati sia Python 2.7 con la libreria `python-dateutil` sia PHP >5.3, sia un server web come Apache2, Nginx, Lighttpd. Se si usa node.js come server web o servono le istruzioni per installare la libreria di python vedere il capitolo [Troubleshooting](#troubleshooting).
Il formato di log che questi server web usano è il formato _combined_, una possibile riproduzione di una visita potrebbe essere questa:

`145.50.30.131 - [10/Mar/2015:13:55:36 -0100] "GET /second.html HTTP/1.1" 200 2326 "http://www.site.com/first.html" "Mozilla/4.08 [en] (Win98; I ;Nav)"`

Come informazione esso fornisce l'indirizzo IP, la data della visita con il relativo fuso orario, il metodo usato e il file richiesto, il protocollo, la risposta del server, i byte scaricati, la pagina visitata in precedenza (referrer) e infine lo UserAgent del browser del visitatore. Da tutte queste informazioni, specialmente dal file richiesto e dal referrer si riesce a creare una catena di richieste e a ricreare una _user-story_. Per il grafico flow solo in parte viene tenuto conto del referrer, vengono infatti concatenate l'una dopo l'altra tutte le visite di un utente e se per caso il referrer di una visita non è uguale alla pagina visitata in precedenza, il referrer viene aggiunto come pagina effettivamente visualizzata dall'utente.

```
145.50.30.131 - [10/Mar/2015:13:55:36 -0100] "GET /first.html HTTP/1.1" 200 2326 "https://www.google.com" "Mozilla/4.08 [en] (Win98; I ;Nav)"
145.50.30.131 - [10/Mar/2015:13:55:56 -0100] "GET /second.html HTTP/1.1" 200 2136 "http://www.mysite.com/first.html" "Mozilla/4.08 [en] (Win98; I ;Nav)"
```

Nell'esempio sopra il referrer della seconda visita combacia perfettamente con la visita fatta in precedenza, così l'ordine temporale della visualizzazione del sito seguirà questa strada: _/first.html_ -> _/second.html_

```
145.50.30.131 - [10/Mar/2015:13:55:36 -0100] "GET /first.html HTTP/1.1" 200 2326 "https://www.google.com" "Mozilla/4.08 [en] (Win98; I ;Nav)"
145.50.30.131 - [10/Mar/2015:13:55:56 -0100] "GET /second.html HTTP/1.1" 200 2136 "http://www.mysite.com/about.html" "Mozilla/4.08 [en] (Win98; I ;Nav)"
```

In questo esempio invece il referrer (_/about.html_) è diverso dalla vista precedente (_/first.html_), questo vuol dire che l'utente inizialmente era su _/first.html_, poi è passato a visitare _/second.html_ non venendo da _/first.html_ ma da _/about.html_, il che significa che egli in precedenza aveva aperto la pagina _/about.html_ (magari in un'altra tab del browser) e poi da li ha cliccato un link che lo ha portato a _/second.html_.
L'ordine che l'utente ha seguito è dunque: _/first.html_ -> _/about.html_ -> _/second.html_

Viene così ricreata esattamente la sua visita temporale al sito, compresa di salti fra le tab del browser. 
Diversamente viene fatto per il grafico tree che mostra la cronologia degli utenti a livello di spostamenti tramite link. Questo significa per ogni utente vi possono essere più cronologie

#### Il ruolo di PHP
Il primo programma ad essere interpellato sul server è PHP tramite una richiesta AJAX da parte del browser che porta la data di inizio e di fine scansione del log richiesta dall'utente e una stringa indicante il nome del file JSON da creare. PHP tramite il comando `ob_start()` e `system()` chiama uno script Python (main.py) con quattro parametri, le due date e il tipo di grafico richiesto dall'utente e il nome da dare al file JSON. Una volta eseguito lo script, PHP invia al browser una stringa vuota se main.py ha avuto successo, una stringa "fail", se c'è stato un errore e non si possono visualizzare i dati. Durante la chiamata `system()` viene eseguito main.py, script fondamentale. Dopo l'avvio dello script si riceve l'output di main.py, o vuoto o "fail", con la chiamata `ob_get_clean()`. Lo stesso output verrà riportato al browser dell'utente che deciderà se scaricare il file JSON e renderizzarlo o lanciare un errore. Sorge ora una domanda fondamentale, perché usare due chiamate AJAX per avviare lo script e per poi scaricare il JSON invece che usarne solo una e farsi restituire subito il JSON? Perchè con la prima chiamata AJAX si comunica a PHP di eseguire main.py tramite `system()`, da questa chiamata `ob_get_clean()` preleva il buffer che è stato prodotto, nel buffer si potrebbe dunque scrivere il JSON, ma se lo script non andasse a buon fine nel buffer verrebbero scritti gli errori del traceback che poi verrebbero restituiti all'utente che potrebbe leggere tutto ed eseguire operazioni malevoli. Dunque per un fatto di sicurezza non viene mai mostrato l'output di python e si chiede all'utente di scaricare un nuovo file JSON creato appunto da python.

#### main.py
main.py è lo script che sta alla base di tutti e tre i grafici, è capace di costruire i dati per tutti e tre. Come parametri prende due date, un numero che identifica il tipo di grafico che l'utente ha richiesto: 0-> tree, 1-> flow, 2->stack e una stringa casuale che indica il nome del file da creare.
Come prima cosa apre il file config.json, in cui ci sono dei parametri impostati dal proprietario del sito:

* access\_log\_location: il percorso dove risiede l'access.log (solitamente in /var/log/_apache_)
* website\_name: il nome del sito (www.sito.com)
* folder\_level: la profondità dalle cartelle da analizzare, ad esempio se impostato ad 1 la seguente richiesta www.sito/cartella/cartella2/file.html verrà considerata solo fino a /cartella, se impostato a due, viene considerata fino a /cartella/cartella2
* blacklist\_folders: questa è una blacklist delle cartelle che non si vuole mostrare al pubblico, come portali di amministrazione o di statistiche
* whitelist\_extensions: qui vengono definiti i file che si vogliono analizzare, tipicamente si scelgono i file .html, .php, tralasciando le immagini, i fogli di stile e gli script
* omit\_malicious\_bots: se _true_ lo script non esaminerà i tentativi di falsi login a pagine inesistenti del sito

Come seconda cosa lo script esegue `get_requests()` e apre il file di log, lo legge riga per riga per non occupare RAM preziosa e decide se la riga deve essere tenuta o scartata, perché fuori range, controllando se la data è nell'intervallo richiesto, perché appartiene alla blacklist o non appartiene alla whitelist.
Viene poi eseguito anche un controllo per evitare di passare al browser un file troppo lungo: se viene richiesto flow o tree e il numero di accessi è troppo alto per essere renderizzato da un browser viene lanciato un errore e il programma si ferma. 
Dopo la fase di preparazione avviene la vera e propria costruzione del file da passare al browser, un file JSON. Per stack e flow questo file è molto simile, mentre per tree è totalmente diverso.
Il JSON che viene sviluppato per tree è di questa forma:

```
{ name: "root",
 children: [
  {
   name: "IP1",
   children: [
    {
     name: "first_page_requested",
     children: [
      {name: "first_page_requested_coming_by parent"},
      {name: "second_page_requested_coming_by parent"},
      {name: "third_page_requested_coming_by parent"},
     ]
    },
```

viene creato un primo livello con tutti gli indirizzi IP (i visitatori del sito). Mentre si costruisce questo primo livello vengono appese le varie visite effettuate ad un certo IP, le visite dirette sono figlie primogenite dell'indirizzo IP, mentre quelle che vengono dal sito stesso (ad esempio cliccando su un link) vengono appese alla visita precedente tramite una funzione ricorsiva: `attach_node()`.
Differentemente da tree, stack e flow hanno bisogno di un file JSON con struttura diversa:

```
{
    start_time: 1427665171000,
    data: [
        {
            0: "/home",
            3: "/about",
            104: "/contacts",
            203: "/home",
            name: "82.49.143.223",
        },
        {
            31: "/about",
            66: "/home",
```
`start_time` è la data di inizio scansione del log in _timestamp_. `data` contiene un dizionario per ogni IP, in ogni dizionario sono registrate le visite con la loro data, espressa in secondi passati partendo da `start_time`. 
Per la creazione di questo JSON il procedimento è molto semplice, per ogni riga dell'access.log se l'IP è nuovo viene creato un nuovo dizionario, se l'IP era già stato preso in considerazione viene aggiornato il dizionario relativo aggiungendo la nuova visita. 
E' bene ora fare un piccolo esempio, supponiamo di avere due linee di log così configurate:

```
145.50.30.131 - [10/Mar/2015:13:55:36 -0100] "GET /second.html HTTP/1.1" 200 2326 "https://www.google.com" "Mozilla/4.08 [en] (Win98; I ;Nav)"
145.50.30.131 - [10/Mar/2015:13:55:56 -0100] "GET /fourth.html HTTP/1.1" 200 2136 "http://www.site.com/first.html" "Mozilla/4.08 [en] (Win98; I ;Nav)"
```
Il JSON sarà realizzato così:
```
{
    start_time: ...,
    data: [
        {
            0: "/second", //la prima visita
            10: "/first", //il referrer della seconda visita
            20: "/fourth", //la seconda visita
            name: "145.50.30.131",
        }
```

Quando non ci sono più righe da analizzare indifferentemente dal tipo di grafico il file JSON viene effettivamente scritto in memoria con il nome passato in linea di comando, evitando di creare file con lo stesso nome che non permetterebbero la visualizzazione contemporanea da due client di due analisi di periodi diversi.

### Lato Client

Dal punto di vista del client è sufficiente un qualsiasi browser per visualizzare Log to History, con Javascript abilitato. Come prima cosa quando l'utente ha deciso l'intervallo di analisi, viene effettuata una chiamata AJAX per informare il server che deve preparare un file da analizzare. Dopo la risposta del server alla chiamata, viene eseguito dal browser il download del file JSON, preparato da Python, sempre tramite una chiamata AJAX. Nel caso di flow e stack, viene eseguita una fase di pre-processing dei dati scaricati dal client per prepararli al rendering. Per il rendering viene sfruttata la libreria [D3](https://github.com/mbostock/d3) che permette di costruire grafici SVG interattivi in maniera sia veloce sia leggera. Per ogni grafico la fase di pre-processing e di rendering avviene in modo diverso, per questo sono stati creati tre script: `tree_graph.js`, `flow_chart.js` e `stack_chart.js`.

#### tree_graph.js

Questo script crea un albero orizzontale, il primo livello di nodi identifica l'indirizzo IP dei visitatori, gli altri livelli sono le pagine visitate. In questo grafico non c'è una fase di pre-processing a differenza di flow e stack, quindi lo script procede subito a renderizzare i dati creando un oggetto SVG dove si svilupperà il grafico, e in seguito analizzando il file JSON. Sostanzialmente il file JSON è costruito gerarchicamente, ovvero un IP ha dei figli e questi figli a loro volta hanno altri figli; quello che fa tree_graph.js è tradurre questa gerarchia in un albero. Ogni nodo viene creato con la funzione `nodeEnter()` e unito agli altri nodi con `link()`. Ad ogni nodo sono associato due eventi, "click" che mostra e nasconde i figli del nodo e "hover" che mostra un riquadro con le informazioni del nodo. 

#### flow_chart.js

Anche `flow_chart.js` scarica subito il JSON contenente i dati riguardo alle visite. Avviene una fase di analisi e poi di rendering. Il rendering viene affidato a una funzione, `draw()`, che è da considerarsi una sorta black box, in quanto non è stata scritta da me ma presa da un'infografica del New York Times che mostra come le varie squadre di football abbiano cambiato campionato nel corso degli anni: [Football Conferences](http://www.nytimes.com/newsgraphics/2013/11/30/football-conferences/). Questo script quindi prepara i dati per la funzione e poi la chiama. Quello che viene essenzialmente fatto dalla fase di pre-processing è aggiungere delle entry al file JSON, perché il codice preso è basato per analizzare anni, mentre Log to History deve analizzare misure molto più brevi, come minuti e secondi. Ciò che è invece fatto da `draw()` è disegnare delle linee verticali, rappresentanti i visitatori che stanno navigando su un pagina del sito, e delle linee di shift che stanno a significare che il visitatore ha cambiato pagina.

#### stack_chart.js

Come flow, `stack_chart.js` scarica il file JSON, da questo file egli ne costruisce un array che verrà renderizzato in seguito. Questo array è costituito da ogni cartella del sito associata ad una lista di intervalli discreti, analizzando le visite nel file JSON viene incrementato un contatore in corrispondenza dell'intervallo corretto e della pagina visitata. Questo array viene poi messo a grafico usando una libreria chiamata [NVD3](https://github.com/novus/nvd3), basata su D3. Il grafico è ad aree sovrapposte, il che significa che nello stesso istante si può osservare sia quanti visitatori erano presenti nelle singole pagine, sia quanti visitatori aveva il sito in generale. Questo grafico è l'unico che può analizzare tempi lunghi come giorni, grazie al fatto di essere dinamico, infatti la grandezza degli intervalli discreti è scelta da`stack_chart.js` in base alla durata del periodo di analisi. In base al numero di secondi analizzati, questo numero viene diviso per 180 o per 360 e da li si ha l'intervallo. Se si analizza un giorno gli intervalli sono di 86400/180= 8 minuti, se si analizza un'ora sono di 3600/360= 10 secondi, se si analizzano tre ore sono di 10800/360= 30 secondi. La funzione che decide l'ampiezza degli intervalli è la seguente.

```javascript
if (data["interval_processed"] > 86000){ // in data["interval_processed"] è presente il numero di secondi dell'access log che si è analizzato 
    time_interval = +((data["interval_processed"]/180).toFixed(0)) //se il periodo analizzato è superiore al giorno si hanno intervalli più lunghi
}else{
    time_interval = +((data["interval_processed"]/360).toFixed(0)) //se il periodo analizzato è inferiore al giorno si hanno intervalli più brevi
}
```

#### Interfaccia grafica

L'interfaccia grafica di Log to History è composta quasi solamente da un header, il quale permette di scegliere un periodo di analisi degli accessi a un sito. Questo header è costituito da due data input costruiti con la libreria [Datetime Picker](https://github.com/tarruda/bootstrap-datetimepicker) e un pulsante che una volta premuto manda una richiesta AJAX al server. Per maneggiare facilmente l'interattività con l'utente è stata usata la libreria [jQuery](https://jquery.com/). Per non sovraccaricare PHP, usandolo solo in qualità di ponte fra client e Python, come libreria per gestire l'header come template è stato usato [Handlebars](https://github.com/wycats/handlebars.js/). 

## Ottimizzazioni

Dopo la fase di creazione di Log to History è avvenuta anche una fase di testing e di ottimizzazione delle performance. Riguardo a main.py è stato usato questo strumento: [LineProfiler](https://github.com/rkern/line_profiler) che permette di vedere quanto tempo viene speso per ogni riga di una funzione che si vuole analizzare. In particolare usando questo tool si è visto che main.py trascorreva molto tempo a convertire delle stringhe in date con il metodo `time.strptime(compiled_line[1][:-6], '%d/%b/%Y:%H:%M:%S')`. É stato quindi sostituito da uno molto più efficiente che divide le date secondo intervalli conosciuti, grazie al fatto che le date hanno sempre lo stesso formato, e le immette in un oggetto `datetime`:

```python
month_map = {'Jan': 1, 'Feb': 2, 'Mar':3, 'Apr':4, 'May':5, 'Jun':6, 'Jul':7, 
    'Aug':8,  'Sep': 9, 'Oct':10, 'Nov': 11, 'Dec': 12}

def apachetime(s):
    '''
    metodo che riconosce le date 4 volte più velocemente rispetto al metodo datetime che usa le regex, tagliando la stringa a lunghezze fisse
    '''
    return datetime.datetime(int(s[7:11]), month_map[s[3:6]], int(s[0:2]), \
         int(s[12:14]), int(s[15:17]), int(s[18:20]))
```

Sempre per ottimizzare main.py nella fase di scansione del log (la più pesante e lenta), se la data di inizio log è distante da quella da dove parte l'analisi, la prima parte del log non viene presa in considerazione risparmiando molto tempo. Viene presa la data di inizio e fine log e la grandezza del file in bytes, conoscendo anche la data da cui l'utente vuole scansionare il log viene calcolata una percentuale che sta a significare quanta parte del file di log bisogna saltare. La formula è: `(data_richesta - data_inizio_log / data_fine_log - data_inizio_log) * grandezza_del_file`

```python
with open(log_dir, 'rb') as fh: #binary read
    first_line_of_log = next(fh).decode() #first line of log
    first_time_of_log = int(apachetime( first_line_of_log.split("[")[1] ).strftime("%s")) #first time in timestamp
    fh.seek(-2048, os.SEEK_END) #current pointer location is moved 2048 bytes before the end of the file
    last_line_of_log = fh.readlines()[-1].decode() #last line of log
    last_time_of_log = int(apachetime( last_line_of_log.split("[")[1] ).strftime("%s")) #last time in timestamp
    start_percentage = (int(start_point.strftime("%s")) - first_time_of_log) / float(last_time_of_log - first_time_of_log)
    first_seek_jump = int((start_percentage - 0.05) * log_size)
```

Basta poi chiamare `access_log_file.seek(first_seek_jump, os.SEEK_SET)` e si sarà evitato di analizzare la prima parte non necessaria del file di log. Qualora venga saltata una porzione troppo grande del file di log lo script torna indietro a passi fissi cercando una data minore rispetto a quella richiesta dall'utente.

Grazie a queste ottimizzazioni si risparmia molto tempo nell'esecuzione lato server, come viene dimostrato nelle due immagini sotto, nella prima viene mostrato un profiling del programma attuale, nella seconda del programma senza l'uso di `seek()` e `apachetime()`. Il comando dato è lo stesso: `kernprof -l -v main.py 08/06/2015@08:00:30 08/06/2015@11:45:30 2` e dunque anche l'intervallo di scansione è lo stesso. Si può subito notare che il ciclo for esegue 50.000 cicli nel primo caso mentre nel secondo 200.000 dato che nel secondo non viene saltata alcuna parte di file. Si può vedere anche che per calcolare `request_time` nel primo caso occorrono 9.6 millisecondi a volta, mentre nel caso non ottimizzato ne servono 142.

![Optimized](http://i.imgur.com/XXmDcYZ.png "Optimized")

![Not Optimized](http://i.imgur.com/cEVrKd3.png "Not Optimized")

Per analizzare gli script lato browser invece è stata usata la console di Google Chrome nella sezione di profiling, essa mostra il tempo speso per ogni funzione. Nel nostro caso vi era un grosso ammontare di tempo speso nella funzione `findClosest()`. Il suo ruolo era, in un dizionario, oggetto non ordinato in JavaScript, restituire l'elemento dopo uno selezionato.

```javascript
function findClosest(associative_array, id, increasing) {
        /* method that, in a dictionary(associative array), finds the element after the passed one */
        var step = increasing ? 1 : -1; //search next or previous
        var i=+id+step;
        if( associative_array[id]!="" && associative_array[id]!==undefined ){
            for(; i>=0 && i<=o; i+=step ){
                if( associative_array[i] && associative_array[i]!="" ){
                    return i;
                }
            }
        } return false;
    }
```

Questo metodo è altamente inefficiente perché per cercare l'elemento si procede a passi piccoli (+1, -1) controllando se appunto esiste un elemento, il metodo è stato sostituito da un array in cui ad ogni elemento dell'array è associato un indice: 

```javascript
var array_sorted_keys = Object.keys(associative_array).sort( function(a,b) { //sorting object elements for fast access to the next element
    return +b - +a; //desc ordering
});
```

Dunque per accedere all'elemento successivo basta chiedere l'indice dell'elemento corrente con `current_key_index = entry_sorted_keys.indexOf(key)` e eseguire `array_sorted_keys[current_key_index +1]`

## Troubleshooting

### Generare un access.log su server web alternativi

Se come server web si usa Node.js, che non genera alcun file di log, di seguito si possono trovare delle implementazioni per creare un access.log.

* [Node.js log](https://github.com/petershaw/NodeJS-Apache-Like-AccessLog)
* [Node.js log](https://www.npmjs.com/package/apache-log)

### Installare la libreria python-dateutil
Occorre che sia installata una libreria di python per il parsing delle date: `python-dateutil`, installabile dando il comando `pip install -r requirements.txt` se si è nella cartella del progetto.
Per installare pip:

* `sudo apt-get install pip` per sistemi Debian/Ubuntu
* `yum install pip` per sistemi Fedora/CentOS

### Nascondere config.json al pubblico

Se si vuole nascondere questo file per non mostrare la blacklist delle cartelle basta aggiungere una regola alla configurazione di apache o nginx.

```
<FilesMatch "/config.json$">
    Order Allow,Deny
    Deny from all
</FilesMatch>
```

```
location ~ \config.json {
    deny all;
}
```

## Statistiche
Di seguito vengono riportate alcune statistiche sui tempi di analisi e di rendering di Log to History.

Lato server esso è in grado di analizzare interamente un access log da 100mb in circa 30 secondi su una macchina VPS Debian con una CPU Intel(R) Xeon(R) CPU E5-2630L (2.3GHz) e 50mb di RAM disponibile, non arrivando mai a consumare tutti i 50mb di RAM. Per questa analisi è stato usato il comando `watch cat /proc/meminfo` che da molte statistiche sul uso della RAM nei sistemi Linux.

![Starting RAM](http://i.imgur.com/0qongbO.png "Starting RAM") ![RAM before exiting](http://i.imgur.com/NrfBfzX.png "RAM before exiting")

Per analizzare solo un'ora posta nel mezzo del file di log lo script impiega meno di un secondo, per analizzare le ultime 24 ore del file di log impiega dodici secondi.

Lato client per il rendering di un grafico tree di durata un'ora Javascript esegue tutto il suo lavoro impiegando 0.6 secondi. Per ricavare questi numeri è stata usata la console di Google Chrome nella sezione "timeline".

![Tree rendering](http://i.imgur.com/X9Nu3o1.png "Tree rendering")

Per visualizzare un grafico flow di un'ora, Javascript lavora 2.7 secondi.

![Flow rendering](http://i.imgur.com/XnsXLYf.png "Flow rendering")

Mentre per mettere a grafico tre giorni su stack, Javascript impiega 1.9 secondi, notando che il browser resta inattivo(fase idle) per molto tempo aspettando che il server finisca di elaborare l'intero log.

![Stack rendering](http://i.imgur.com/Yuv4gii.png "Stack rendering")


## Conclusioni

Log to History è senza dubbio un buon software con i suoi pregi e le sue lacune. Per prima cosa esso si pone in contrasto con gli altri analizzatori di server web prediligendo la vista microscopica a quella macroscopica. Esso introduce una nuova tecnica di analisi a cartelle che nessun analizzatore aveva fino ad ora, molto utile per generalizzare le visite. Propone in tutto tre grafici con diversi livelli di generalizzazione: il primo è tree che non generalizza nulla, per ogni utente mostra esattamente tutte le pagine, senza ridurle a cartelle, che ha visitato. Il secondo grafico è flow nel quale si può vedere la cronologia di tutti gli utenti contemporaneamente a livello di cartella, quindi già con questo grafico si generalizzano le visite dell'utente a cartelle e non più a pagine. L'ultimo grafico è il più generico di tutti, esso tralascia completamente l'utente e si avvicina come standard agli altri analizzatori che si possono trovare online. Esso mostra l'andamento del numero di utenti che navigano una certa cartella durante un certo lasso di tempo.
Log to History ha ancora molti aspetti in cui migliorare, ma considerando che è stato sviluppato in 3 mesi si può dire che ha già raggiunto un buon livello di complessità e praticità.
