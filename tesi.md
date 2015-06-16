# Log to History



## Introduzione

Log to history è uno strumento che permette di analizzare le visite di un determinato sito.
Esso è composto da tre grafici che vengono costruiti andando ad analizzare un file, noto come access.log, che registra tutte le visite da parte di utenti su un determinato sito web. Di questi grafici i primi due tendono a dare una vista microscopica, ovvero sono più efficaci per analizzare brevi periodi di tempo, mentre l'ultimo grafico è adattabile ad un qualisasi periodo, sia di poche ore sia di più giorni. L'idea per la creazione di Log to History è venuta perchè sul web mancano degli strumenti di analisi microscopica e di analisi del flusso di utenti. Log to History infatti con i primi due grafici mostra la cronologia delle pagine visitate dai singoli utenti, ricreando la storia di un utente su un sito web. E' quindi uno strumento molto potente in quanto fa capire quando l'utente cambia pagina e che pagina gli viene servita.
I tre grafici hanno i seguenti nomi: tree, flow e stack. 

### Tree
Tree mostra un albero che si sviluppa lateralmente, esso fa combaciare ad ogni visitatore un set di pagine che sono state visualizzate _direttamente_ dall'utente, direttamente significa che l'utente proviene da un altro sito, come Google, Bing e altri, o che ha caricato il sito dalla barra degli indirizzi del Browser. Ogni visita diretta è dunque rappresentata da un nodo, che ha come figli le pagine visitate dall'utente che ha cliccato su un link del sito che si sta analizzando. Questo grafico rappresenta la cronologia di un utente calcolata secondo i movimenti via link. Passando sopra ad ogni nodo che identifica un visitatore si può vedere l'user agent, se è identificato come un bot o un crawler il nodo verrà colorato di giallo. Mentre se si passa sopra una pagina visitata da un utente comparirà la data della visita.

### Flow
Flow è un diagramma di flusso che sviluppa verticalmente, il suo scopo è quello di mostrare i cambiamenti di pagina di un utente. In Flow ci sono tante colonne quante cartelle ci sono su un sito, all'interno di queste colonne sono rappresentati i visitatori come linee verticali, quando una linea cambia colonna significa che il visitatore ha cambiato pagina durante la sua navigazione. E' bene precisare che l'analisi in questo grafico non comprende tutte le pagine di un sito ma solo le cartelle dove sono residenti le pagine web. Questo significa che se un visitatore è sulla pagina [http://sito/cartella/index.html](http://sito/cartella/index.html "URL della pagina"), il grafico mostrerà l'utente come se stesse visitando [cartella/](http://sito/cartella/ "Cartella dove risiede la pagina web"). Si ha quindi una generalizzazione di cosa i visitatori stanno navigando. E' presente pure una casella di ricerca in cui si può cercare ed evidenziare un certo visitatore. 

### Stack
Stack è un grafico che si concentra sulle visite non tenendo conto di chi ha fatto la visita. E' l'unico grafico che si discosta dagli altri. Esso mostra un grafico ad aree sovrapposte, ogni area di colore diverso rappresenta il quantitativo di visite su una determinata cartella nel tempo. Anche questo grafico usa le cartelle al posto delle singole pagine web, per non essere troppo particolareggiato ma più generale possibile. La sovrapposizione delle varie aree permette inoltre di visualizzare anche il numero complessivo di utenti su tutto il sito in un dato istante. Sono presenti dei controlli nella parte superiore del grafico, a destra viene permesso di passare fra il quantitativo di visite alla percentuale delle visite cliccando il pulsante "expanded", mentre se viene premuto "stream" i dati verranno organizzati attorno all'asse x e non solo al di sopra, creando un grafico organico e di flusso.

## Funzionamento
I grafici proposti all'utente sono creati _online_, ovvero quando l'utente li chiede, così da circonscrivere solo il periodo che l'utente vuole analizzare.
Il lavoro è spartito fra server(il sito) e client(l'utente), il server analizza il file di accesso al sito(access.log), seleziona il periodo richisto dall'utente e prepara per il client un file di piccole dimensioni, così da rendere il download veloce, che sarà ri-analizzato e renderizzato dal browser. 
Il lavoro del server è fatto da due programmi, Python e PHP. Python analizza il file di log e crea il file per il client, mentre PHP fa da ponte tra server e client.

### Lato Server
Il server è il computer o la macchina virtuale dove risiede il sito web. Per funzionare è neccessario che su di esso siano installati sia Python 2.7 sia PHP >5.3 sia un server web come Apache2, Nginx, Lighttpd. Se si usa node.js come server web vedere [Troubleshots](#troubleshots).
Il formato di log che questi server web usano è il formato _combined_ , che è strutturato come segue:

`145.50.30.131 - [10/Mar/2015:13:55:36 -0100] "GET /second.html HTTP/1.1" 200 2326 "http://www.site.com/first.html" "Mozilla/4.08 [en] (Win98; I ;Nav)"`

Come informazione esso fornisce l'indirizzo IP, la data della visita, il file richiesto e il metodo usato, il protocollo, la risposta del server, i byte scaricati, la pagina visitata in precedenza(referrer) e infine lo UserAgent del browser del visitatore.

#### Il ruolo di PHP
Il primo programma ad essere interpellato è PHP tramite tramite una richiesta AJAX da parte del browser che porta la data di inizio e di fine scansione del log richesta dall'utente. PHP tramite il comando `ob_start()` e `system()` chiama uno script Python(main.py) con tre parametri, le due date e il tipo di grafico richesto dall'utente. Una volta eseguito lo script PHP invia al browser una stringa vuota se main.py ha avuto successo, una stringa "fail", se c'è stato un errore e non si possono visualizzare i dati. Durante la chiamata `system()` viene eseguito main.py, script fondamentale.

#### main.py
main.py è lo script che sta alla base di tutti e tre i grafici, è capace di costruirli tutti e tre. Come parametri prende due date e un numero che identifica il tipo di grafico che l'utente ha rihiesto: 0-> tree, 1-> flow, 2->stack.
Come prima cosa apre il file config.json, in cui ci sono dei parametri settati dal proprietario del sito:

* access_log_location: il percorso dove risiede l'access.log(solitamente in /var/log/apache)
* website_name: il nome del sito(www.sito.com)
* folder_level: la profodità dalle cartelle da analizzare, ad esempio se impostato ad 1 la seguente richiesta www.sito/cartella/cartella2/file.html verrà cosiderata solo fino a /cartella, se impostato a due, viene considerata fino a /cartella/cartella2
* blacklist_folders: questa è una blacklist delle cartelle che non si vuole mostrare al pubblico, come portali di amministrazione o di statistiche
* whitelist_extensions: qui vengono definiti i file che si vogliono analizzare, tipicamente si scelgono i file .html, .php, tralasciando le immagini, i fogli di stile e gli script

Come seconda cosa lo script esegue `get_requests()` e apre il file di log, lo legge riga per riga per non occupare RAM preziosa e decide se la riga deve essere tenuta o scartata, perchè fuori range, controllando se la data è nell'intervallo richiesto, perchè appartiene alla blacklist o non appartiene alla whitelist.
Viene poi eseguito anche un controllo per evitare di passare al browser un file troppo lungo: se viene richiesto flow o tree e il numero di accessi è troppo alto per essere renderizzato da un browser viene lanciato un errore e il programma si ferma. 
Dopo la fase preparativa avviene la vera e propria costruzione del file da passare al browser, un file JSON. Per stack e flow questo file è molto simile, mentre per tree è totalmente diverso.
Il JSON che viene sviluppato per tree è di questa forma:
```
{
 "name": "root",
 "children": [
  {
   "name": "IP1",
   "children": [
    {
     "name": "first_page_requested",
     "children": [
      {"name": "first_page_requested_coming_by parent"},
      {"name": "second_page_requested_coming_by parent"},
      {"name": "third_page_requested_coming_by parent"},
      {"name": "forth_page_requested_coming_by parent"},
      {"name": "fifth_page_requested_coming_by parent"}
     ]
    },
```

viene creato un primo livello con tutti gli indirizzi IP(i visitatori del sito). Mentre si costruisce questo primo livello vengono appese le varie visite effettuate ad un certo IP, le visite dirette sono figlie primogenite dell'indirizzo IP, mentre quelle che vengono dal sito stesso(ad esempio cliccando su un link) vengono appese alla visita precedente tramite una funzione ricorsiva: `attach_node()`.
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
Per la creazione di questo JSON il procedimento è molto semplice, per ogni riga dell'access.log se l'IP è nuovo viene creato un nuovo dizionario, se l'IP era gia stato preso in considerazione viene aggiornato il dizionario relativo aggiungendo la nuova visita. 
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
            0: "/second", //la prima visista
            10: "/first", //il referrer della seconda visita
            20: "/fourth", //la seconda visita
            name: "145.50.30.131",
        }
```

Quando non ci sono più righe da analizzare indifferentemente dal tipo di grafico il file JSON viene effettivamente scritto in memoria.

### Lato Client

Dal punto di vista del client è sufficente un qualisasi browser per visualizzare Log to History, con Javascript abilitato. Come prima cosa quando l'utente ha deciso l'intervallo di analisi, viene effettuata una chiamata AJAX per informare il server che deve preparare un file da analizzare. Dopo la risposta del server alla chiamata, viene eseguito dal browser il download del file JSON, preparato da Python, sempre tramite una chiamata AJAX. Nel caso di flow e stack, viene eseguita una fase di pre-processing dei dati scaricati dal client per prepararli al rendering. Per il rendering viene sfruttata la libreria [D3](https://github.com/mbostock/d3) che permette di costruire grafici SVG interattivi in maniera sia veloce sia leggera. Per ogni grafico la fase di pre-processing e di rendering avviene in modo diverso, per questo sono stati creati tre script: `tree_graph.js`, `flow_chart.js` e `stack_chart.js`.

#### tree_graph.js

Questo script crea un albero orizzontale, il primo livello di nodi idetnifica l'indirizzo IP dei visitatori, gli altri livelli sono le pagine visitate.In questo grafico non c'è una fase di pre-processing a differenza di flow e stack, quindi lo script procede subito a renderizzare i dati creando un oggetto SVG dove si svilupperà il grafico, e in seguito analizzando il file JSON. Sostanzialmente il file JSON è costruito gerarchialmente, ovvero un IP ha dei figli e questi figli a loro volta hanno altri figli; quello che tree_graph.js è tradurre questa gerarchia in un albero. Ogni nodo viene creato con la funzione `nodeEnter()` e unito agli altri nodi con `link()`. Ad ogni nodo sono associato due eventi, "click" che mostra e nasconde i figli del nodo e "hover" che mostra un riquadro con le informazioni del nodo. 

#### flow_chart.js

Anche flow_chart.js scarica subito il JSON contentete i dati riguardo alle visite. Avviene una fase di analisi e poi di rendering. Il rendering viene affidato a una funzione, `draw()`, che è da considerarsi una sorta black box, in quanto non è stata scritta da me ma presa da un sito: [Football Conferences](http://www.nytimes.com/newsgraphics/2013/11/30/football-conferences/). Questo script quindi prepara i dati per la funzione e poi la chiama. Quello che viene essenzialmente fatto dalla fase di pre-processing è aggiungere delle entry al file JSON, perchè il codice preso è basato per analizzare anni, mentre Log to History deve analizzare misure molto più brevi, come minuti e secondi. Ciò che è invece fatto da `draw()` è disegnare delle linee verticali, ogni linea rappresenta un visitatore che sta navigando su un pagina del sito, e delle linee di shift che stanno a significare che il visitatore ha cambiato pagina. 

#### stack_chart.js

Come flow, stack_chart.js scarica il file JSON, da questo file egli ne costruisce un array che verrà renderizzato in seguito. Questo array è costituito da ogni cartella del sito associata ad una lista di intervalli discreti, analizzando le visite nel file JSON viene incrementato un contatore in corrispondenza dell'intervallo corretto e della pagina visitata. Questo array viene poi messo a grafico usando una libreria chiamata [NVD3](https://github.com/novus/nvd3), basata su D3. Il grafico è ad aree sovrapposte, il che significa che nello stesso istante si può osservare sia quanti visitatori erano presenti nelle singole pagine, sia quanti visitatori aveva il sito in generale.

#### Interfaccia grafica

L'interfaccia grafica di Log to History è composta quesi solamente da un header, il quale permette di scegliere un periodo di analisi degli accessi a un sito. Questo header è costitito da due data input costruiti con la libreria [Datetime Picker](https://github.com/tarruda/bootstrap-datetimepicker) e un pulsante che una volta premuto manda una richesta AJAX al server. Per maneggiare facilmente l'interattività con l'utente è stata usata la libreria [jQuery](https://jquery.com/). Per non sovraccaricare PHP e usarlo solo in qualità di ponte fra client e Python come libreria per gestire l'header come template è stato usato [Handlebars](https://github.com/wycats/handlebars.js/). 

## Ottimizzazioni

## Problemi

### Generare un access.log su server web alternativi

Se come server web si usa Node.js, che non genera alcun file di log, di seguito si possono trovare delle implementazioni per creare un access.log.
* [Node.js log](https://github.com/petershaw/NodeJS-Apache-Like-AccessLog)
* [Node.js log](https://www.npmjs.com/package/apache-log)
