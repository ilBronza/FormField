# FormField

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![Build Status][ico-travis]][link-travis]
[![StyleCI][ico-styleci]][link-styleci]

This is where your description should go. Take a look at [contributing.md](contributing.md) to see a to do list.

## Installation

Via Composer

``` bash
$ composer require ilbronza/formfield
```

## Usage

### Select or multiple fields

#### Set the possible values for a relation

Given a relation (ex. Categories) you can set the possible values for a select or multiple field with the following code
to populate the select or multiple field options

``` bash

public function getPossibleClientsValuesArray() : array
{
    return Category::select('name', 'id')->take(10)->pluck('name', 'id')->toArray();
}

```

### Ricaricare le opzioni di un select

Impostare `possibleValuesRoute` con l'URL GET che restituisce le opzioni, usando
la stessa convenzione di dataTables:

```php
'vehicle_id' => [
    'type' => 'select',
    'possibleValuesRoute' => route('orders.vehicle-options', ['order' => $order->getKey()]),
],
```

L'endpoint restituisce una mappa `valore => etichetta`, direttamente oppure sotto
la chiave `list`:

```json
{ "list": { "4": "Panda", "7": "Golf" } }
```

Sono supportati anche i gruppi nel formato dataTables:

```json
{ "groups": { "Disponibili": { "4": "Panda" }, "Altri": { "7": "Golf" } } }
```

- La GET parte a ogni apertura, senza usare la cache del browser. Click ripetuti
  durante il caricamento condividono la stessa richiesta.
- Con Select2, la tendina si apre dopo aver ricevuto le opzioni aggiornate. Con
  `select2 => false`, la GET parte all'interazione con mouse o tasti di apertura;
  la lista nativa viene aggiornata in modo asincrono.
- Funziona anche con select multipli, `selectInput` e campi aggiunti al DOM dopo
  l'inizializzazione. I campi readonly o disabled non vengono ricaricati.
- La selezione corrente viene conservata soltanto se è ancora presente nelle
  opzioni restituite. Un valore non più compatibile viene svuotato. Nei select
  multipli restano selezionati i soli valori ancora ammessi, compreso `0`.
  Questa regola si applica anche ai valori manuali di `selectInput`.
- Il ricaricamento non genera `change` applicativi, salvataggi o fetcher. In caso
  di errore HTTP o risposta non valida, opzioni e selezione restano intatte e
  viene mostrata una notifica di errore.

Il caricamento iniziale PHP resta invariato (`list`, relazione, enum o getter
del modello): `possibleValuesRoute` serve ad aggiornarlo alle aperture successive,
inclusa la prima interazione. L'URL è già risolto lato PHP.

### Aggiornare select al change di altri campi

Le configurazioni sono indipendenti:

- Sul **select destinatario**, `possibleValuesRoute` indica la URL getter e
  `possibleValuesRouteFields` elenca i nomi dei campi da inviare alla GET.
- Su **qualsiasi campo sorgente** (testo, data, checkbox, select, textarea...),
  `refreshSelectOptions` elenca i select da ricaricare al suo `change`.

Esempio di dipendenza reciproca fra operatori e mansioni:

```php
'operator_id' => [
    'type' => 'select',
    'possibleValuesRoute' => route('operators.options'),
    'possibleValuesRouteFields' => ['task_id'],
    'refreshSelectOptions' => ['task_id'],
],
'task_id' => [
    'type' => 'select',
    'possibleValuesRoute' => route('tasks.options'),
    'possibleValuesRouteFields' => ['operator_id'],
    'refreshSelectOptions' => ['operator_id'],
],
```

Cambiando operatore, il getter delle mansioni riceve per esempio
`GET /tasks/options?operator_id=42`; cambiando mansione, il getter degli operatori
riceve `GET /operators/options?task_id=7`. I valori vengono letti dal form al
momento della richiesta, anche se non sono stati salvati. Il getter deve applicare
i filtri ricevuti e restituire la mappa delle opzioni disponibili.

Anche un campo testuale può aggiornare più select:

```php
'search' => [
    'type' => 'text',
    'refreshSelectOptions' => ['operator_id', 'task_id', 'location_id'],
],
```

Ogni destinatario usa il proprio getter e i propri `possibleValuesRouteFields`;
può quindi inviare più sorgenti, per esempio `['search', 'starts_at', 'active']`.
L'elenco dei dati inviati è indipendente dall'elenco dei campi che scatenano il
refresh. Se non vengono dichiarate sorgenti, la GET non aggiunge valori del form.

Le sorgenti e i destinatari si cercano nello stesso form, per nome. Dichiarare i
nomi dei campi senza il suffisso `[]` dei multipli. Per i campi ripetuti si cerca
prima il nome con lo stesso indice; si possono indicare anche nomi completi come
`operator_id[row-key]`. Sono inclusi i controlli associati tramite l'attributo
HTML `form`. Un campo mancante o ambiguo causa un errore senza leggere dati da
altri form. Fuori da un form vengono considerati soltanto altri controlli senza
form associato.

Testo e date inviano il valore corrente; i radio inviano il valore selezionato
oppure una stringa vuota. Un checkbox singolo invia `true`/`false`, mentre gruppi
di checkbox e select multipli inviano array. I valori vuoti vengono inviati:
un array vuoto è serializzato come `nome[]=` per evitare che jQuery lo ometta
(Laravel può convertirne l'elemento vuoto in `null`). Il getter deve gestire
esplicitamente i filtri vuoti.

Il refresh parte subito al `change`, senza attendere il salvataggio editor.
Per un testo, il normale `change` scatta all'uscita dal campo dopo una modifica,
non a ogni tasto. Più destinatari vengono richiesti indipendentemente; i nomi
duplicati sono deduplicati. L'aggiornamento automatico delle opzioni e lo
svuotamento di una selezione incompatibile non emettono un altro `change`:
le dipendenze reciproche non producono richieste circolari e le dipendenze
indirette devono essere elencate esplicitamente nei destinatari del trigger.

Richieste simultanee con gli stessi dati vengono condivise; se cambia il
contesto parte una nuova richiesta. Le risposte superate da modifiche successive
vengono scartate, anche se arrivano dopo quelle più recenti. Errori HTTP o risposte
non valide conservano la lista e la selezione esistenti.

Per richiedere lo stesso aggiornamento da codice:

```javascript
// Restituisce una Promise, oppure null se il campo non è configurato/editabile.
window.ibFormFieldReloadSelectOptions(document.querySelector('#vehicle_id'));
```

La funzione è inclusa da `ilbronza.formfields.js`: dopo l'aggiornamento del
pacchetto occorre ricompilare gli asset dell'applicazione che lo importa.

## Batch field refresh (chiamata cumulativa)

I campi del form che vanno riletti dal server dopo un salvataggio (editor inline, save di riga in tabella, `fetchFieldValue`) non vengono più richiesti con una chiamata HTTP per campo: vengono accodati e richiesti con **una sola POST cumulativa**.

Il sistema è composto da tre pezzi:

| Pacchetto | Ruolo |
|---|---|
| **FormField** (`ilbronza.formfields.batch.js`) | coordinatore JS: coda, dedup, debounce, chiamata batch, applicazione valori al DOM |
| **Crud** (`CRUDUpdateEditorBatchReadTrait`) | endpoint batch: legge N campi dal modello in una richiesta |
| **datatables** | hook su `draw.dt` e su save riga che innescano il refresh |

### Come funziona

1. Qualcuno chiede il refresh di uno o più campi (per nome).
2. Il coordinatore accoda i nomi in un `Set` per URL editor (`data-updateeditorurl`), deduplicandoli.
3. Dopo un debounce di 50ms parte **una** POST sulla route di update del modello con:

```
ib-editor-read-batch: true
fields[]: total_cost
fields[]: total_revenue
_method: PUT
```

4. La risposta contiene tutti i valori:

```json
{
    "success": true,
    "fetch-fields-batch": true,
    "values": { "total_cost": 123.45, "total_revenue": 456.78 },
    "model-id": "..."
}
```

5. Il coordinatore applica ogni valore al campo corrispondente (gestendo date, cleave/money, checkbox/radio, `originalvalue`, trigger `ibchanged`) senza scatenare salvataggi automatici.

### API JavaScript

``` javascript
// accoda campi per il refresh (debounce + dedup), una POST per URL
window.requestFieldsRefreshBatch(['total_cost', 'total_revenue']);
window.requestFieldsRefreshBatch(['total_cost'], { url: '...', debounceMs: 100 });

// singolo campo
window.requestFieldRefreshBatch('total_cost');

// esegue subito la coda di una URL senza aspettare il debounce
window.flushFieldsRefreshBatch(url);

// chiamata diretta senza coda (ritorna jqXHR)
window.getFieldsValuesFromEditorBatch(['total_cost'], url);

// equivalente batch di refreshFetchingFieldsValues (legge data-fetchfields dal target)
window.refreshFetchingFieldsValuesBatch(target);

// equivalente batch di dtRefreshFieldsList (risolve i selettori di
// window.dtEditorRefreshingFieldList in nomi campo)
window.dtRefreshFieldsListBatch();
```

### Entry point pubblici (retrocompatibili)

Le funzioni storiche restano e ora passano dal batch:

- `getFieldValueFromEditor(target)` → `requestFieldRefreshBatch`
- `refreshFetchingFieldsValues(target)` → `refreshFetchingFieldsValuesBatch`
- `dtRefreshFieldsList()` (datatables) → `dtRefreshFieldsListBatch`

Le implementazioni a chiamata singola sono sospese ma disponibili come fallback:
`__ibLegacyGetFieldValueFromEditor`, `__ibLegacyRefreshFetchingFieldsValues`, `__ibLegacyDtRefreshFieldsList`.

### Dichiarare le dipendenze (lato PHP, invariato)

Nei fieldset si continua a usare `fetchFieldValue` per dire "quando questo campo viene salvato, rileggi questi altri":

``` php
'starts_at' => [
    'type' => 'date',
    'fetchFieldValue' => [
        'calculated_production_days',
        'calculated_event_days',
    ],
],
```

Il campo viene renderizzato con `data-fetchfields`; al salvataggio i campi elencati vengono riletti **in un'unica chiamata**.

Questo flusso parte dopo la risposta al salvataggio editor, non su ogni `change`
di un normale form. L'applicazione batch attuale gestisce gli input, ma non
aggiorna i valori di select o textarea. Separatamente, il `fetcher` di tipo
`ajaxfetcher` reagisce al `change` con una POST che sostituisce o aggiunge HTML
nel target configurato; non è il caricamento delle opzioni descritto sopra.

### Requisiti

- Il controller del modello deve usare `CRUDUpdateTrait` (Crud), che include `CRUDUpdateEditorBatchReadTrait`.
- I campi target devono avere `data-updateeditorurl` (renderizzato automaticamente da `__attributes.blade.php`).

[ico-version]: https://img.shields.io/packagist/v/ilbronza/formfield.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/ilbronza/formfield.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/ilbronza/formfield/master.svg?style=flat-square
[ico-styleci]: https://styleci.io/repos/12345678/shield

[link-packagist]: https://packagist.org/packages/ilbronza/formfield
[link-downloads]: https://packagist.org/packages/ilbronza/formfield
[link-travis]: https://travis-ci.org/ilbronza/formfield
[link-styleci]: https://styleci.io/repos/12345678
[link-author]: https://github.com/ilbronza
[link-contributors]: ../../contributors
