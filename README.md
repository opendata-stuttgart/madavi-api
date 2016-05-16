# madavi-api
api for rrdtool graphics per sensors + graphics from archive.luftdaten.info
  
  
Update der Daten von archiv.luftdaten.info  
  
für das tägliche Update der Daten in der crontab einen Aufruf der get_new_data.sh erstellen. Dieser sollte gegen 9:00 Uhr erfolgen, da die
Daten des Vortages erst gegen 8 Uhr hochgeladen werden.  
Da der Provider, welcher die DNS-Server für luftdaten.info betreibt, eine "merkwürdige" Konfiguration betreibt, kann es vorkommen, das der
Hostname archive.luftdaten.info nicht immer stabil aufgelöst wird. Meist hilft dann ein weiterer, manueller Aufruf der get_new_data.sh
einige Zeit später.  
