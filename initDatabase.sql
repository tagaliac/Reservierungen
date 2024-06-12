CREATE DATABASE sitzordnung;

use sitzordnung;

create table kunde(
    KundenID int PRIMARY KEY AUTO_INCREMENT,
    Kundenname varchar(127) NOT NULL,
    Gezahlt boolean DEFAULT 0
);

create table sitzplatz(
    SitzplatzID int PRIMARY KEY,
    Belegt boolean DEFAULT 0
);

CREATE TABLE reservierung(
    ReservierungsID int PRIMARY KEY AUTO_INCREMENT,
    KundenID int NOT NULL,
    SitzplatzID int NOT NULL,
    FOREIGN KEY (KundenID) REFERENCES kunde(KundenID),
    FOREIGN KEY (SitzplatzID) REFERENCES sitzplatz(SitzplatzID)
);