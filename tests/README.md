# Test FormField

La suite PHP verifica il rendering Blade dei select configurati, inclusi
selezione iniziale, URL escapato, campi disabilitati e selectInput multipli,
sorgenti del getter e trigger su campi testo, data e checkbox:

```sh
python3 ../.ibtest/bin/ibtest.py FormField
```

I test browser usano Node.js, Playwright, jQuery e Select2 reali. Con queste
dipendenze disponibili nella risoluzione dei moduli Node:

```sh
node --test tests/selectOptions.browser.test.js
node --test tests/conditionalOpener.browser.test.js
```

Si può usare `NODE_PATH` per le dipendenze già presenti nell'applicazione e
`IB_TEST_BROWSER_CHANNEL=chrome` per eseguire i test con Chrome installato;
altrimenti Playwright usa il proprio Chromium.

La suite intercetta tutte le richieste verso un host di test locale al browser;
non usa endpoint dell'applicazione. Verifica aperture ripetute, richieste
concorrenti, selezioni multiple e manuali, gruppi ed etichette HTML, errori e
retry, select nativi e dinamici, e assenza di salvataggi durante il refresh.
Copre inoltre dipendenze reciproche senza cicli, un testo che aggiorna cinque
select, serializzazione del contesto, isolamento fra form e campi ripetuti,
selezioni incompatibili e risposte ricevute fuori ordine.
