# Log to History
Uno strumento di analisi rivolto al web.

<!-- START doctoc generated TOC please keep comment here to allow auto update -->
<!-- DON'T EDIT THIS SECTION, INSTEAD RE-RUN doctoc TO UPDATE -->

Indice:
- [Introduzione](#introduzione)
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
- [Problemi](#problemi)
  - [Generare un access.log su server web alternativi](#generare-un-accesslog-su-server-web-alternativi)
  - [Nascondere config.json al pubblico](#nascondere-configjson-al-pubblico)
- [Altri analizzatori open-source](#altri-analizzatori-open-source)

<!-- END doctoc generated TOC please keep comment here to allow auto update -->

## Introduzione

Log to history è uno strumento che permette di analizzare le visite di un determinato sito.
Esso è composto da tre grafici che vengono costruiti andando ad analizzare un file, noto come access.log, che registra tutte le visite da parte di utenti su un determinato sito web. Di questi grafici i primi due tendono a dare una vista microscopica, ovvero sono più efficaci per analizzare brevi periodi di tempo, mentre l'ultimo grafico è adattabile ad un qualisasi periodo, sia di poche ore sia di più giorni. L'idea per la creazione di Log to History è venuta perchè sul web mancano degli strumenti di analisi microscopica e di analisi del flusso di utenti. Log to History infatti con i primi due grafici mostra la cronologia delle pagine visitate da singoli utenti, ricreando la storia di un utente su un sito web. E' quindi uno strumento molto potente in quanto lascia intendere quando l'utente cambia pagina e che pagina gli viene servita.
I tre grafici di Log to History hanno i seguenti nomi: tree, flow e stack. 

### Tree
Tree mostra un albero che si sviluppa lateralmente, esso fa combaciare ad ogni visitatore un set di pagine che sono state visualizzate _direttamente_ dall'utente, direttamente significa che l'utente proviene da un altro sito, come Google, Bing e altri, o che ha caricato il sito dalla barra degli indirizzi del browser. Ogni visita diretta è dunque rappresentata da un nodo, che ha come figli le pagine visitate dall'utente che ha cliccato su un link del sito che si sta analizzando. Questo grafico rappresenta la cronologia di un utente calcolata secondo i movimenti via link. Passando sopra ad ogni nodo che identifica un visitatore si può vedere l'user agent, se è identificato come un bot o un crawler il nodo verrà colorato di giallo. Mentre se si passa sopra una pagina visitata da un utente comparirà la data della visita.

[<img src="http://i.imgur.com/mW1XgZb.png"/>](#tree-graph "Tree Graph")

Questo grafico é la rappresentazione di Tree, si vedono quindi molti IP, e se un IP viene cliccato si aprono le pagine che ha richiesto direttamente. In questo caso 7.50.142.24(il penultimo IP analizzato) ha comiciato a visitare il sito dalla pagina _/_, che é l'home-page del sito, si é quindi spostato a _/atleta_ cliccando qualche link nella home-page, poi si é spostato su _atleta/Levi-Roche-Mandji/150970_. Nel caso di 94.32.224.201(l'ultimo IP), nell'ultima parte della sua visita partendo da _/societa/1810_ egli ha richesto 5 pagine cliccando su 5 link diversi probabilmente aprendo le nuove pagine come nuove tab del browser.

### Flow
Flow è un diagramma di flusso che sviluppa verticalmente, il suo scopo è quello di mostrare i cambiamenti di pagina di un utente. In Flow ci sono tante colonne quante cartelle ci sono su un sito, all'interno di queste colonne sono rappresentati i visitatori come linee verticali, quando una linea cambia colonna significa che il visitatore ha cambiato pagina durante la sua navigazione. E' bene precisare che l'analisi in questo grafico non comprende tutte le pagine di un sito ma solo le cartelle dove sono residenti le pagine web. Questo significa che se un visitatore è sulla pagina _sito/cartella/index.html_, il grafico mostrerà l'utente come se stesse visitando _cartella/_. Si ha quindi una generalizzazione di cosa i visitatori stanno navigando. E' presente pure una casella di ricerca in cui si può cercare ed evidenziare un certo visitatore. 

![Flow Graph](https://raw.githubusercontent.com/Naramsim/Log-to-History/master/img/Screenshot_flow.png "Flow Graph")

In questo esempio si possono notare i visitatori di un sito del giorno 8 Giugno dalle 19.07 alle 19.10, é evidenziato un visitatore che é stato cercato tramite la search-box in alto a sinistra. Il visitatore é identificato dal suo indirizzo IP e la sua cronologia di un colore arancio. Si puó capire che il visitatore ha abbandonato una pagina un _/confronto_ verso le 19.50 per andare su una pagina in _/atleta_, poi é ritornato su _/confronto_ e in fine é ritornato in _/atleta_.

#### Nota su tree e flow
Questi due grafici sono utili per avere una vista microscopica di un determinato periodo di tempo, che può andare dal minuto al massimo di un'ora. La logica di un intervallo così breve sta nel capire che se si analizzasse un periodo più lungo i grafici sarebbero troppo lunghi e non si capire più molto il rendering. Vi è da dire anche che ad ogni riconnessione ad internet un utente cambia indirizzo IP, il che significa che è quasi impossibile rintracciare la storia di un utente in periodi lunghi, perchè esso ha cambiato IP. In stack invece l'utente non è più considerato e dunque l'analisi si può prolungare a qualsiasi periodo.

### Stack
Stack è un grafico che si concentra sulle visite non tenendo conto di chi ha fatto la visita. E' l'unico grafico che si discosta dagli altri. Esso mostra un grafico ad aree sovrapposte, ogni area di colore diverso rappresenta il quantitativo di visite su una determinata cartella nel tempo. Anche questo grafico usa le cartelle al posto delle singole pagine web, per non essere troppo particolareggiato e più generale possibile. La sovrapposizione delle varie aree permette inoltre di visualizzare anche il numero complessivo di utenti su tutto il sito in un dato istante. Sono presenti dei controlli nella parte superiore del grafico, a destra viene permesso di passare fra il quantitativo di visite alla percentuale delle visite cliccando il pulsante "expanded", mentre se viene premuto "stream" i dati verranno organizzati attorno all'asse x e non solo al di sopra, creando un grafico organico e di flusso.

![Stack Chart](https://raw.githubusercontent.com/Naramsim/Log-to-History/master/img/Screenshot_stack.png "Stack Chart")

Sopra viene mostrato un esempio prolungato di uno stack chart, dalle tre di mattina fino alle venti di sera, si puó notare come in generale gli utenti tendano a crescere durante la mattinata per poi stabilizzarsi. Vi sono anche alcuni picchi che probabilmente sono dovuti ad attivitá di indicizzazione di spider e crawler. Si puó notare anche che le pagine piú richieste sono quelle che risiedono nella cartella _/atleta_.

![Stack Chart](https://raw.githubusercontent.com/Naramsim/Log-to-History/master/img/Screenshot_stack3.png "Stack Chart")

In questo esempio viene analizzato invece solo un breve periodo di un'ora, e viene usato il metodo _Expanded_ che mostra la percentuale di utenti per ogni cartella del sito. Si puó vedere che nella prima mezz'ora gli utenti sono prevalentemente su _/atleta_, mentre nella seconda mezz'ora tendono a crescere le visite su _/confronto_ e sulla root del sito(_/_), ovvero la home-page.

## Sviluppo

### Fase iniziale
Come fase iniziale prima ancora di capire il nostro obiettivo abbiamo studiato e installato molti [analizzatori](#altri-analizzatori-open-source) di access.log open-source che si trovano in rete, li abbiamo confrontati e siamo giunti alla concolusione che l'unica cosa che mancava era uno strumento di analisi microscopica che tenesse conto dell'utente, che mostrasse come si muove all'interno di un sito. Da qui è nata l'idea per lo sviluppo di Log to History. Dopo aver scoperto il nostro obiettivo abbiamo cominciato a studiare il metodo migliore per realizzarlo, quali linguaggi usare, come renderizzare i dati, come interfacciarsi con l'utente. Siamo quindi arrivati ad utilizzare Python lato Server, per la sua semplicità e velocità. PHP come intermediario con il browser dell'utente, si sarebbe potuto riutilizzare Python come web-server attreverso dei framework(esempo: Django, Flask, Tornado) ma secondo [alcune statistiche](http://news.netcraft.com/archives/2015/05/19/may-2015-web-server-survey.html) la maggior parte degli utenti usa ancora la soluzione Apache/PHP come server web. [D3](https://github.com/mbostock/d3) per il rendering dei dati su browser. 

### Fase di sviluppo
Durante la fase di sviluppo abbiamo usato come editor di codice [Sublime 3](https://www.sublimetext.com/3), come strumento di controllo versione Git assieme a Github, sul quale si trova tutto il [codice sorgente](https://github.com/Naramsim/Log-to-History/). Per testare il prodotto si è usato un [server](https://mtgfiddle.me/tirocinio/pezze/) su DigitalOcean e come access.log il file di log di un sito molto visitato: [Atletica.me](http://atletica.me/).

### Fase di testing
Ogni settimana dopo ogni aggiunta al codice é avvenuta una fase di testing, fase che si é prolungata quando il programma é stato ultimato, dalla fase di testing si é poi passati ad [ottimizzare](#ottimizzazioni) il codice.

## Funzionamento
I grafici proposti all'utente sono creati _online_, ovvero quando l'utente li richiede, così da circonscrivere solo il periodo che l'utente vuole analizzare.
Il lavoro è spartito fra server(il sito) e client(l'utente), il server analizza il file di accesso al sito(access.log), seleziona il periodo richisto dall'utente e prepara per il client un file di piccole dimensioni in formato JSON, così da rendere il download veloce, che sarà ri-analizzato e renderizzato dal browser. 
Il lavoro del server è fatto da due programmi, Python e PHP. Python analizza il file di log e crea il file per il client, mentre PHP fa da ponte tra server e client. Il client invece necessita solo di un browser.

### Lato Server
Il server è il computer o la macchina virtuale dove risiede il sito web. Per far funzionare Log to History è neccessario che su di esso siano installati sia Python 2.7 sia PHP >5.3, sia un server web come Apache2, Nginx, Lighttpd. Se si usa node.js come server web vedere la pagina [Troubleshots](#troubleshots).
Il formato di log che questi server web usano è il formato _combined_, una possibile riproduzione di una visita potrebbe essere questa:

`145.50.30.131 - [10/Mar/2015:13:55:36 -0100] "GET /second.html HTTP/1.1" 200 2326 "http://www.site.com/first.html" "Mozilla/4.08 [en] (Win98; I ;Nav)"`

Come informazione esso fornisce l'indirizzo IP, la data della visita con il relativo fuso orario, il metodo usato e il file richiesto, il protocollo, la risposta del server, i byte scaricati, la pagina visitata in precedenza(referrer) e infine lo UserAgent del browser del visitatore. Da tutte queste informazioni, specialmente dal file richiesto e dal referrer si riesce a creare una catena di richeste e a ricreare una _user-story_. 

#### Il ruolo di PHP
Il primo programma ad essere interpellato sul server è PHP tramite tramite una richiesta AJAX da parte del browser che porta la data di inizio e di fine scansione del log richesta dall'utente. PHP tramite il comando `ob_start()` e `system()` chiama uno script Python(main.py) con tre parametri, le due date e il tipo di grafico richesto dall'utente. Una volta eseguito lo script, PHP invia al browser una stringa vuota se main.py ha avuto successo, una stringa "fail", se c'è stato un errore e non si possono visualizzare i dati. Durante la chiamata `system()` viene eseguito main.py, script fondamentale. Dopo l'avvio dello script si riceve l'output di main.py, o vuoto o "fail", con la chiamata `ob_get_clean()`. Lo stesso output verrà riportato al browser dell'utente che deciderà se scaricare il file JSON e renderizzarlo o lanciare un errore.

#### main.py
main.py è lo script che sta alla base di tutti e tre i grafici, è capace di costruire i dati per tutti e tre. Come parametri prende due date e un numero che identifica il tipo di grafico che l'utente ha rihiesto: 0-> tree, 1-> flow, 2->stack.
Come prima cosa apre il file config.json, in cui ci sono dei parametri impostati dal proprietario del sito:

* access_log_location: il percorso dove risiede l'access.log(solitamente in /var/log/_apache_)
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

Questo script crea un albero orizzontale, il primo livello di nodi idetnifica l'indirizzo IP dei visitatori, gli altri livelli sono le pagine visitate. In questo grafico non c'è una fase di pre-processing a differenza di flow e stack, quindi lo script procede subito a renderizzare i dati creando un oggetto SVG dove si svilupperà il grafico, e in seguito analizzando il file JSON. Sostanzialmente il file JSON è costruito gerarchialmente, ovvero un IP ha dei figli e questi figli a loro volta hanno altri figli; quello che fa tree_graph.js è tradurre questa gerarchia in un albero. Ogni nodo viene creato con la funzione `nodeEnter()` e unito agli altri nodi con `link()`. Ad ogni nodo sono associato due eventi, "click" che mostra e nasconde i figli del nodo e "hover" che mostra un riquadro con le informazioni del nodo. 

#### flow_chart.js

Anche `flow_chart.js` scarica subito il JSON contentete i dati riguardo alle visite. Avviene una fase di analisi e poi di rendering. Il rendering viene affidato a una funzione, `draw()`, che è da considerarsi una sorta black box, in quanto non è stata scritta da me ma presa da un sito: [Football Conferences](http://www.nytimes.com/newsgraphics/2013/11/30/football-conferences/). Questo script quindi prepara i dati per la funzione e poi la chiama. Quello che viene essenzialmente fatto dalla fase di pre-processing è aggiungere delle entry al file JSON, perchè il codice preso è basato per analizzare anni, mentre Log to History deve analizzare misure molto più brevi, come minuti e secondi. Ciò che è invece fatto da `draw()` è disegnare delle linee verticali, rappresentanti i visitatori che stanno navigando su un pagina del sito, e delle linee di shift che stanno a significare che il visitatore ha cambiato pagina.

#### stack_chart.js

Come flow, `stack_chart.js` scarica il file JSON, da questo file egli ne costruisce un array che verrà renderizzato in seguito. Questo array è costituito da ogni cartella del sito associata ad una lista di intervalli discreti, analizzando le visite nel file JSON viene incrementato un contatore in corrispondenza dell'intervallo corretto e della pagina visitata. Questo array viene poi messo a grafico usando una libreria chiamata [NVD3](https://github.com/novus/nvd3), basata su D3. Il grafico è ad aree sovrapposte, il che significa che nello stesso istante si può osservare sia quanti visitatori erano presenti nelle singole pagine, sia quanti visitatori aveva il sito in generale. Questo grafico è l'unico che può analizzare tempi lunghi come giorni, grazie al fatto di essere dinamico, infatti la grandezza degli intervalli discreti è scelta da`stack_chart.js` in base alla durata del periodo di analisi. Se si analizzano giorni gli intervalli potrebbero essere di 30 minuti, se si analizza un'ora potrebbero essere di 10 secondi. 

#### Interfaccia grafica

L'interfaccia grafica di Log to History è composta quasi solamente da un header, il quale permette di scegliere un periodo di analisi degli accessi a un sito. Questo header è costitito da due data input costruiti con la libreria [Datetime Picker](https://github.com/tarruda/bootstrap-datetimepicker) e un pulsante che una volta premuto manda una richesta AJAX al server. Per maneggiare facilmente l'interattività con l'utente è stata usata la libreria [jQuery](https://jquery.com/). Per non sovraccaricare PHP, usandolo solo in qualità di ponte fra client e Python, come libreria per gestire l'header come template è stato usato [Handlebars](https://github.com/wycats/handlebars.js/). 

## Ottimizzazioni

Dopo la fase di creazione di Log to History è avvenuta anche una fase di testing e di ottimizazione delle performance. Riguardo a main.py è stato usato questo strumento: [LineProfiler](https://github.com/rkern/line_profiler) che permette di vedere quanto tempo viene speso per ogni riga di una funzione che si vuole analizzare. In particolare usando questo tool si è visto che main.py trascorreva molto tempo a convertire delle stringhe in date con il metodo `time.strptime(compiled_line[1][:-6], '%d/%b/%Y:%H:%M:%S')`. É stato quindi sostituito da uno molto più efficente:

```python
month_map = {'Jan': 1, 'Feb': 2, 'Mar':3, 'Apr':4, 'May':5, 'Jun':6, 'Jul':7, 
    'Aug':8,  'Sep': 9, 'Oct':10, 'Nov': 11, 'Dec': 12}

def apachetime(s):
    '''
    method that parses 4 times faster dates using slicing instead of regexs
    '''
    return datetime.datetime(int(s[7:11]), month_map[s[3:6]], int(s[0:2]), \
         int(s[12:14]), int(s[15:17]), int(s[18:20]))
```

Sempre per ottimizzare main.py nella fase di scansione del log(la piú pesante e lenta), se la data di inizio log é distante da quella da dove parte l'analisi, la prima parte del log non viene presa in considerazione risparmiando molto tempo.

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

Basta poi chiamare `access_log_file.seek(first_seek_jump, os.SEEK_SET)` e si sará evitato di analizzare la prima parte non necessaria del file di log.

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

Questo metodo è altamente inefficente perchè per cercare l'elemento si procede a passi piccoli(+1, -1) controllando se appunto esiste un elemento, il metodo è stato sostituito da un array in cui ad ogni elemento dell'array è associato un indice: 

```javascript
var array_sorted_keys = Object.keys(associative_array).sort( function(a,b) { //sorting object elements for fast access to the next element
    return +b - +a; //desc ordering
});
```

Dunque per accedere all'elemento successivo basta chiedere l'indice dell'elemento corrente con `current_key_index = entry_sorted_keys.indexOf(key)` e eseguire `array_sorted_keys[current_key_index +1]`

## Problemi

### Generare un access.log su server web alternativi

Se come server web si usa Node.js, che non genera alcun file di log, di seguito si possono trovare delle implementazioni per creare un access.log.

* [Node.js log](https://github.com/petershaw/NodeJS-Apache-Like-AccessLog)
* [Node.js log](https://www.npmjs.com/package/apache-log)

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

## Altri analizzatori open-source

Prima di sviluppare Log to History é stata eseguita una analisi degli strumenti giá esistenti per l'analisi delle visite di siti web, di seguito si trovano alcuni progetti open-source con una brevissima analisi delle loro funzionalitá:

* [Piwik](https://github.com/piwik/piwik): Software avanzato che usa l’injection di codice js nelle pagine di un sito per il tracking degli utenti, alternativa a Google Analytics.
* [Request-log-analyzer](https://github.com/wvanbergen/request-log-analyzer): Software scritto in Ruby che analizza tanti formati di file di log non solo access log. Fornisce molte statistiche nel terminale. Non dispone di interfaccia web.
* [Goaccess](https://github.com/allinurl/goaccess/): Software molto avanzato scritto in C che analizza file di log via terminale anche in real-time, può generare file html statici.
* [Apache-scalp](https://github.com/neuroo/apache-scalp): script python che mostra in terminale i possibili attacchi che ha subito un server web leggendo le richeste dell'access log, mostra solo gli attacchi, non altre statistiche. Genera html statico. 
* [Http_log_analyzer](https://github.com/tmarly/http_log_analyzer): analizza l’access log e ne fa il display via web, manca di un analisi microscopica
* [Http-logs-analyzer](https://github.com/flrnull/http-logs-analyzer): Script in c++ che analizza velocemente un file di log nel terminale e mostra a video un JSON con le statistiche. Non dispone di interfaccia web.
* [Nginx-mongo-logger](https://github.com/mikedamage/nginx-mongo-logger): Script che lavora in background e inserisce ogni nuova linea dell’access log in un database Mongo. Fondamentalmente non fornisce statistiche ma solo un database per costrirle.
* [ServerLogStats](https://github.com/danielstjules/ServerLogStats): analizza i log che gli utenti caricano, quindi non quello del server e mostra varie statistiche: è scritto in Javascript.
* [Live-log-analyzer](https://github.com/saltycrane/live-log-analyzer): Software python che mostra via web in real-time statistiche sull’access log. 
