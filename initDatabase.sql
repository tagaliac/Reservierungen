CREATE DATABASE sitzordnung;

use sitzordnung;

create table kunde(
    KundenID int PRIMARY KEY AUTO_INCREMENT,
    Kundenname varchar(127) NOT NULL,
    Gezahlt boolean DEFAULT 0,
    Bezahlort varchar(31),
    Email varchar(127),
    Telefon varchar(31)
);

create table sitzplatz(
    SitzplatzLabel varchar(31) PRIMARY KEY,
    Belegt boolean DEFAULT 0
);

CREATE TABLE reservierung(
    ReservierungsID int PRIMARY KEY AUTO_INCREMENT,
    KundenID int NOT NULL,
    SitzplatzLabel varchar(31) NOT NULL UNIQUE,
    FOREIGN KEY (KundenID) REFERENCES kunde(KundenID),
    FOREIGN KEY (SitzplatzLabel) REFERENCES sitzplatz(SitzplatzLabel)
);