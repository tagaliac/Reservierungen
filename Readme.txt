

Benötigte Projekte zum Installieren:
- PHPMailer link:https://github.com/PHPMailer/PHPMailer
- xampp

Zum Löschen aller Reservierungen:
SQL-Code: DELETE FROM reservierung WHERE ReservierungsID>0;
	  ALTER TABLE reservierung AUTO_INCREMENT = 1;
	  UPDATE sitzplatz SET Belegt=0 WHERE Belegt=1;

Zum Löschen aller Kunden (vorher noch alle Reservierungen löschen):
	  DELETE FROM kunde WHERE KundenID>0;
	  ALTER TABLE reservierung AUTO_INCREMENT = 1;