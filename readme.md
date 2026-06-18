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
