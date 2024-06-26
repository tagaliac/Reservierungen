Dies ist eine Webseite zur Reservierung für den Griechisch-Pontischen Verein Romania

Erstellt von Achilleas Tagalidis

Benötigte Projekte zum Installieren:
- PHPMailer link:https://github.com/PHPMailer/PHPMailer
- xampp link:https://www.apachefriends.org/de/download.html

Installierung:
- installiert xampp und richtet es ein.
- Erstellt ein Ordner (der Datenordner) in "C:\xampp\htdocs" (Windows)
- Fügt den PHPMailer in diesem Ordner hinzu

Aufrufen der Seite
- Startet den XAMPP Comtrol Panel
- Startet Apache und MySQL
- Die Localen Links sind
	-für Kunden: http://localhost/[der Datenordner]/KundenIndex.php
	-für Vereinsmitglieder http://localhost/[der Datenordner]/index.php
	-für Admin: http://localhost/phpmyadmin/index.php
- Der Server läuft auf dem Port 80443. Da es der selbe Port ist wie Skype soll entweder Skype deinstalliert werden oder der Port gewechselt werden
- Erstellt im Adminlink den Benutzer "AdminReservierung" mit dem Passwort "Romania1234".
	 Alternativ müssen die Daten in der Datei C:\xampp\htdocs\[der Datenordner]\Funktionen\DatabaseCon.php geändert werden.
- Führt den SQL-Code in der Datei C:\xampp\htdocs\[der Datenordner]\initDatabase.sql aus um die Grundstruktur der Datenbank zu erstellen.
- Gibt den Benutzer "AdminReservierung" alle Rechte der Datenbank

Zum Löschen aller Reservierungen:
SQL-Code: DELETE FROM reservierung WHERE ReservierungsID>0;
	  ALTER TABLE reservierung AUTO_INCREMENT = 1;
	  UPDATE sitzplatz SET Belegt=0 WHERE Belegt=1;

Zum Löschen aller Kunden (vorher noch alle Reservierungen löschen):
	  DELETE FROM kunde WHERE KundenID>0;
	  ALTER TABLE reservierung AUTO_INCREMENT = 1;