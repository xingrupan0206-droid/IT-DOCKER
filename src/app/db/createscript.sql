/*********************************************************************************
 * Project     : Theater Aurora
 * Database    : Aurora
 * Beschrijving: Genormaliseerd database-schema met 9 entiteiten, foreign keys,
 *               systeemvelden en 5+ realistische testgegevens per tabel.
 * Engine      : InnoDB (vereist voor foreign key relaties)
 *
 * Versiegeschiedenis
 * Versie  Datum          Auteur          Omschrijving
 * ******  **********     **********      **************
 * 01      04-06-2026     Systeem         Initiële creatie met 9 genormaliseerde tabellen
 *********************************************************************************/

-- Maak de database aan als deze nog niet bestaat
CREATE DATABASE IF NOT EXISTS Aurora CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE Aurora;

-- Verwijder bestaande tabellen in de juiste volgorde (foreign key afhankelijkheden)
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS Melding;
DROP TABLE IF EXISTS Ticket;
DROP TABLE IF EXISTS Voorstelling;
DROP TABLE IF EXISTS Prijs;
DROP TABLE IF EXISTS Bezoeker;
DROP TABLE IF EXISTS Medewerker;
DROP TABLE IF EXISTS Contact;
DROP TABLE IF EXISTS Rol;
DROP TABLE IF EXISTS Gebruiker;
SET FOREIGN_KEY_CHECKS = 1;

/*********************************************************************************
 * 1. GEBRUIKER
 * Bevat alle gebruikersaccounts van het systeem (bezoekers, medewerkers, admins).
 *********************************************************************************/
CREATE TABLE Gebruiker (
    Id              INT AUTO_INCREMENT  NOT NULL,
    Voornaam        VARCHAR(50)         NOT NULL,
    Tussenvoegsel   VARCHAR(10)             NULL,
    Achternaam      VARCHAR(50)         NOT NULL,
    Gebruikersnaam  VARCHAR(100)        NOT NULL,
    Wachtwoord      VARCHAR(255)        NOT NULL    COMMENT 'Versleutelde opslag',
    IsIngelogd      BIT                 NOT NULL    DEFAULT 0,
    Ingelogd        DATETIME                NULL,
    Uitgelogd       DATETIME                NULL,
    Isactief        BIT                 NOT NULL    DEFAULT 1,
    Opmerking       VARCHAR(250)            NULL,
    DatumAangemaakt DATETIME(6)         NOT NULL    DEFAULT CURRENT_TIMESTAMP(6),
    DatumGewijzigd  DATETIME(6)         NOT NULL    DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    PRIMARY KEY (Id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*********************************************************************************
 * 2. ROL
 * Koppelt een rol (Bezoeker, Medewerker, Administrator) aan een gebruiker.
 *********************************************************************************/
CREATE TABLE Rol (
    Id              INT AUTO_INCREMENT  NOT NULL,
    GebruikerId     INT                 NOT NULL,
    Naam            VARCHAR(100)        NOT NULL    COMMENT 'Bezoeker, Medewerker, Administrator',
    Isactief        BIT                 NOT NULL    DEFAULT 1,
    Opmerking       VARCHAR(250)            NULL,
    DatumAangemaakt DATETIME(6)         NOT NULL    DEFAULT CURRENT_TIMESTAMP(6),
    DatumGewijzigd  DATETIME(6)         NOT NULL    DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    PRIMARY KEY (Id),
    CONSTRAINT FK_Rol_Gebruiker FOREIGN KEY (GebruikerId) REFERENCES Gebruiker(Id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*********************************************************************************
 * 3. CONTACT
 * Bevat contactgegevens (email, mobiel) van een gebruiker.
 *********************************************************************************/
CREATE TABLE Contact (
    Id              INT AUTO_INCREMENT  NOT NULL,
    GebruikerId     INT                 NOT NULL,
    Email           VARCHAR(100)        NOT NULL,
    Mobiel          VARCHAR(20)         NOT NULL,
    Isactief        BIT                 NOT NULL    DEFAULT 1,
    Opmerking       VARCHAR(250)            NULL,
    DatumAangemaakt DATETIME(6)         NOT NULL    DEFAULT CURRENT_TIMESTAMP(6),
    DatumGewijzigd  DATETIME(6)         NOT NULL    DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    PRIMARY KEY (Id),
    CONSTRAINT FK_Contact_Gebruiker FOREIGN KEY (GebruikerId) REFERENCES Gebruiker(Id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*********************************************************************************
 * 4. MEDEWERKER
 * Bevat medewerkerspecifieke gegevens (nummer, soort) gekoppeld aan een gebruiker.
 *********************************************************************************/
CREATE TABLE Medewerker (
    Id              INT AUTO_INCREMENT  NOT NULL,
    GebruikerId     INT                 NOT NULL,
    Nummer          MEDIUMINT           NOT NULL    COMMENT 'Uniek medewerkersnummer',
    Medewerkersoort VARCHAR(20)         NOT NULL    COMMENT 'Beheerder, Ticketcontroleur',
    Isactief        BIT                 NOT NULL    DEFAULT 1,
    Opmerking       VARCHAR(250)            NULL,
    DatumAangemaakt DATETIME(6)         NOT NULL    DEFAULT CURRENT_TIMESTAMP(6),
    DatumGewijzigd  DATETIME(6)         NOT NULL    DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    PRIMARY KEY (Id),
    CONSTRAINT FK_Medewerker_Gebruiker FOREIGN KEY (GebruikerId) REFERENCES Gebruiker(Id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*********************************************************************************
 * 5. BEZOEKER
 * Bevat bezoekerspecifieke gegevens (relatienummer) gekoppeld aan een gebruiker.
 *********************************************************************************/
CREATE TABLE Bezoeker (
    Id              INT AUTO_INCREMENT  NOT NULL,
    GebruikerId     INT                 NOT NULL,
    Relatienummer   MEDIUMINT           NOT NULL    COMMENT 'Uniek bezoeker nummer',
    Isactief        BIT                 NOT NULL    DEFAULT 1,
    Opmerking       VARCHAR(250)            NULL,
    DatumAangemaakt DATETIME(6)         NOT NULL    DEFAULT CURRENT_TIMESTAMP(6),
    DatumGewijzigd  DATETIME(6)         NOT NULL    DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    PRIMARY KEY (Id),
    CONSTRAINT FK_Bezoeker_Gebruiker FOREIGN KEY (GebruikerId) REFERENCES Gebruiker(Id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*********************************************************************************
 * 6. PRIJS
 * Bevat de prijstarieven die aan tickets gekoppeld kunnen worden.
 *********************************************************************************/
CREATE TABLE Prijs (
    Id              INT AUTO_INCREMENT  NOT NULL,
    Tarief          DECIMAL(5,2)        NOT NULL    COMMENT 'Ticketprijs',
    Isactief        BIT                 NOT NULL    DEFAULT 1,
    Opmerking       VARCHAR(250)            NULL,
    DatumAangemaakt DATETIME(6)         NOT NULL    DEFAULT CURRENT_TIMESTAMP(6),
    DatumGewijzigd  DATETIME(6)         NOT NULL    DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    PRIMARY KEY (Id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*********************************************************************************
 * 7. VOORSTELLING
 * Bevat alle voorstellingen van Theater Aurora.
 * Extra kolommen (Genre, Zaal, Afbeelding, Prijs) zijn toegevoegd voor de
 * front-end weergave, naast de verplichte specificatie-kolommen.
 *********************************************************************************/
CREATE TABLE Voorstelling (
    Id                INT AUTO_INCREMENT  NOT NULL,
    MedewerkerId      INT                 NOT NULL,
    Naam              VARCHAR(100)        NOT NULL    COMMENT 'Naam van de voorstelling',
    Beschrijving      TEXT                    NULL    COMMENT 'Extra informatie over de voorstelling',
    Genre             VARCHAR(50)             NULL    COMMENT 'Extra: genre voor front-end weergave',
    Datum             DATE                NOT NULL    COMMENT 'Speeldatum',
    Tijd              TIME                NOT NULL    COMMENT 'Tijdstip van de voorstelling',
    Zaal              VARCHAR(100)            NULL    COMMENT 'Extra: zaal voor front-end weergave',
    MaxAantalTickets  INT                 NOT NULL    COMMENT 'Maximale capaciteit',
    Prijs             DECIMAL(8,2)            NULL    COMMENT 'Extra: weergaveprijs voor front-end',
    Afbeelding        VARCHAR(255)            NULL    COMMENT 'Extra: afbeelding bestandsnaam',
    Beschikbaarheid   VARCHAR(50)         NOT NULL    DEFAULT 'Ingepland' COMMENT 'Ingepland, Uitverkocht, Geannuleerd',
    Isactief          BIT                 NOT NULL    DEFAULT 1,
    Opmerking         VARCHAR(250)            NULL,
    DatumAangemaakt   DATETIME(6)         NOT NULL    DEFAULT CURRENT_TIMESTAMP(6),
    DatumGewijzigd    DATETIME(6)         NOT NULL    DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    PRIMARY KEY (Id),
    CONSTRAINT FK_Voorstelling_Medewerker FOREIGN KEY (MedewerkerId) REFERENCES Medewerker(Id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*********************************************************************************
 * 8. TICKET
 * Bevat alle gereserveerde tickets, met foreign keys naar Bezoeker, Voorstelling
 * en Prijs. Barcode is een unieke code voor scanning bij de ingang.
 *********************************************************************************/
CREATE TABLE Ticket (
    Id              INT AUTO_INCREMENT  NOT NULL,
    BezoekerId      INT                 NOT NULL,
    VoorstellingId  INT                 NOT NULL,
    PrijsId         INT                 NOT NULL,
    Nummer          MEDIUMINT           NOT NULL    COMMENT 'Uniek reserveringsnummer',
    Barcode         VARCHAR(20)         NOT NULL    COMMENT 'Unieke code',
    Datum           DATE                NOT NULL    COMMENT 'Datum van reservering',
    Tijd            TIME                NOT NULL    COMMENT 'Tijdstip van reservering',
    Status          VARCHAR(20)         NOT NULL    DEFAULT 'Gereserveerd' COMMENT 'Vrij, Bezet, Gereserveerd, Geannuleerd',
    Isactief        BIT                 NOT NULL    DEFAULT 1,
    Opmerking       VARCHAR(250)            NULL,
    DatumAangemaakt DATETIME(6)         NOT NULL    DEFAULT CURRENT_TIMESTAMP(6),
    DatumGewijzigd  DATETIME(6)         NOT NULL    DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    PRIMARY KEY (Id),
    CONSTRAINT FK_Ticket_Bezoeker     FOREIGN KEY (BezoekerId)     REFERENCES Bezoeker(Id),
    CONSTRAINT FK_Ticket_Voorstelling FOREIGN KEY (VoorstellingId) REFERENCES Voorstelling(Id),
    CONSTRAINT FK_Ticket_Prijs        FOREIGN KEY (PrijsId)        REFERENCES Prijs(Id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

/*********************************************************************************
 * 9. MELDING
 * Bevat meldingen, notificaties, klachten en reviews.
 *********************************************************************************/
CREATE TABLE Melding (
    Id              INT AUTO_INCREMENT  NOT NULL,
    BezoekerId      INT                     NULL,
    MedewerkerId    INT                     NULL,
    Nummer          MEDIUMINT           NOT NULL    COMMENT 'Uniek meldingsnummer',
    Type            VARCHAR(20)         NOT NULL    COMMENT 'Notificatie, Klacht of Review',
    Bericht         VARCHAR(250)        NOT NULL,
    Isactief        BIT                 NOT NULL    DEFAULT 1,
    Opmerking       VARCHAR(250)            NULL,
    DatumAangemaakt DATETIME(6)         NOT NULL    DEFAULT CURRENT_TIMESTAMP(6),
    DatumGewijzigd  DATETIME(6)         NOT NULL    DEFAULT CURRENT_TIMESTAMP(6) ON UPDATE CURRENT_TIMESTAMP(6),
    PRIMARY KEY (Id),
    CONSTRAINT FK_Melding_Bezoeker   FOREIGN KEY (BezoekerId)   REFERENCES Bezoeker(Id),
    CONSTRAINT FK_Melding_Medewerker FOREIGN KEY (MedewerkerId) REFERENCES Medewerker(Id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;


/*********************************************************************************
 * SEED DATA — Minimaal 5 realistische rijen per tabel
 *********************************************************************************/

-- 1. GEBRUIKER (7 accounts: 2 medewerkers + 5 bezoekers)
INSERT INTO Gebruiker (Voornaam, Tussenvoegsel, Achternaam, Gebruikersnaam, Wachtwoord, Isactief) VALUES
('Admin',   NULL,   'Theater',     'admin',              'admin123',       1),
('Silvan',  NULL,   'Kooijman',    'silvan.kooijman',    'wachtwoord123',  1),
('Joris',   NULL,   'Tenback',     'joris.tenback',      'wachtwoord123',  1),
('Lisa',    'van',  'Dijk',        'lisa.vandijk',        'wachtwoord123',  1),
('Thomas',  NULL,   'Akkerman',    'thomas.akkerman',     'wachtwoord123',  1),
('Sophie',  'de',   'Groot',       'sophie.degroot',      'wachtwoord123',  1),
('Marieke', 'van den', 'Berg',     'marieke.vandenberg',  'wachtwoord123',  1);

-- 2. ROL (7 rollen gekoppeld aan gebruikers)
INSERT INTO Rol (GebruikerId, Naam, Isactief) VALUES
(1, 'Administrator', 1),
(2, 'Medewerker',    1),
(3, 'Bezoeker',      1),
(4, 'Bezoeker',      1),
(5, 'Bezoeker',      1),
(6, 'Bezoeker',      1),
(7, 'Bezoeker',      1);

-- 3. CONTACT (7 contactgegevens)
INSERT INTO Contact (GebruikerId, Email, Mobiel, Isactief) VALUES
(1, 'admin@aurora.nl',          '0612345678', 1),
(2, 'silvan@aurora.nl',         '0623456789', 1),
(3, 'bezoeker@aurora.nl',       '0634567890', 1),
(4, 'lisa.vandijk@email.nl',    '0645678901', 1),
(5, 'thomas.akkerman@email.nl', '0656789012', 1),
(6, 'sophie.degroot@email.nl',  '0667890123', 1),
(7, 'marieke.vdberg@email.nl',  '0678901234', 1);

-- 4. MEDEWERKER (2 medewerkers: admin + silvan)
INSERT INTO Medewerker (GebruikerId, Nummer, Medewerkersoort, Isactief) VALUES
(1, 1001, 'Beheerder',         1),
(2, 1002, 'Ticketcontroleur',  1);

-- Extra medewerkers voor 5 rijen minimum
INSERT INTO Gebruiker (Voornaam, Tussenvoegsel, Achternaam, Gebruikersnaam, Wachtwoord, Isactief) VALUES
('Karel',  NULL,  'Jansen',   'karel.jansen',    'wachtwoord123', 1),
('Petra',  NULL,  'Smit',     'petra.smit',       'wachtwoord123', 1),
('Henk',   'de',  'Vries',    'henk.devries',     'wachtwoord123', 1);

INSERT INTO Rol (GebruikerId, Naam, Isactief) VALUES
(8,  'Medewerker', 1),
(9,  'Medewerker', 1),
(10, 'Medewerker', 1);

INSERT INTO Contact (GebruikerId, Email, Mobiel, Isactief) VALUES
(8,  'karel.jansen@aurora.nl', '0689012345', 1),
(9,  'petra.smit@aurora.nl',   '0690123456', 1),
(10, 'henk.devries@aurora.nl', '0601234567', 1);

INSERT INTO Medewerker (GebruikerId, Nummer, Medewerkersoort, Isactief) VALUES
(8,  1003, 'Ticketcontroleur', 1),
(9,  1004, 'Beheerder',        1),
(10, 1005, 'Ticketcontroleur', 1);

-- 5. BEZOEKER (5 bezoekers)
INSERT INTO Bezoeker (GebruikerId, Relatienummer, Isactief) VALUES
(3, 2001, 1),
(4, 2002, 1),
(5, 2003, 1),
(6, 2004, 1),
(7, 2005, 1);

-- 6. PRIJS (5 prijstarieven)
INSERT INTO Prijs (Tarief, Isactief, Opmerking) VALUES
(15.00, 1, 'Kindertarief'),
(18.00, 1, 'Studententarief'),
(24.50, 1, 'Standaard tarief'),
(27.50, 1, 'Premium tarief'),
(39.50, 1, 'Gala tarief');

-- 7. VOORSTELLING (6 voorstellingen)
INSERT INTO Voorstelling (MedewerkerId, Naam, Beschrijving, Genre, Datum, Tijd, Zaal, MaxAantalTickets, Prijs, Afbeelding, Beschikbaarheid, Isactief) VALUES
(1, 'De Drie Musketiers',  'Een meeslepende adaptatie van het klassieke avonturenverhaal van Alexandre Dumas.',                      'Avontuur', '2025-06-14', '20:00:00', 'Grote Zaal',  200, 24.50, 'show-musketiers.png',   'Ingepland', 1),
(1, 'Nacht van de Zielen', 'Een aangrijpend drama over verlies, herinnering en hoop. Niet te missen.',                                'Drama',    '2025-06-28', '19:30:00', 'Grote Zaal',  150, 19.50, 'show-nacht-zielen.png', 'Ingepland', 1),
(2, 'Aurora Gala Avond',   'Een exclusieve gala-avond met opera, dans en live muziek. Dresscode: formeel.',                            'Gala',     '2025-07-05', '18:00:00', 'Grote Zaal',  300, 39.50, 'show-gala.png',         'Ingepland', 1),
(1, 'Romeo en Julia',      'De meest romantische tragedie van Shakespeare, vertolkt door een jong en getalenteerd ensemble.',          'Drama',    '2025-07-12', '20:00:00', 'Grote Zaal',  200, 27.50, 'show-romeo-julia.png',  'Ingepland', 1),
(2, 'Jazz Nacht',          'Een intieme avond vol swing, blues en improvisatie. Live jazz door het Aurora Jazz Quintet.',               'Musical',  '2025-07-19', '21:00:00', 'Kleine Zaal',  80, 18.00, 'show-jazz.png',         'Ingepland', 1),
(1, 'De Droomwereld',      'Een magische familievoorstelling vol fantasie, kleur en verwondering. Geschikt voor kinderen vanaf 6 jaar.','Avontuur', '2025-08-02', '14:30:00', 'Grote Zaal',  250, 15.00, 'show-droomwereld.png',  'Ingepland', 1);

-- 8. TICKET (5 tickets)
INSERT INTO Ticket (BezoekerId, VoorstellingId, PrijsId, Nummer, Barcode, Datum, Tijd, Status, Isactief) VALUES
(1, 1, 3, 3001, 'TKT-A1B2C3D4E5F6', '2025-06-01', '14:30:00', 'Gereserveerd', 1),
(2, 2, 3, 3002, 'TKT-G7H8I9J0K1L2', '2025-06-02', '10:15:00', 'Gereserveerd', 1),
(3, 3, 5, 3003, 'TKT-M3N4O5P6Q7R8', '2025-06-03', '16:45:00', 'Gereserveerd', 1),
(4, 4, 4, 3004, 'TKT-S9T0U1V2W3X4', '2025-06-04', '11:00:00', 'Gereserveerd', 1),
(5, 5, 2, 3005, 'TKT-Y5Z6A7B8C9D0', '2025-06-05', '09:30:00', 'Gereserveerd', 1);

-- 9. MELDING (5 meldingen)
INSERT INTO Melding (BezoekerId, MedewerkerId, Nummer, Type, Bericht, Isactief) VALUES
(1, NULL, 4001, 'Review',       'Geweldige voorstelling! De sfeer was fantastisch.',                    1),
(2, NULL, 4002, 'Review',       'Mooie zaal en goede service. Kom zeker terug.',                        1),
(3, NULL, 4003, 'Klacht',       'De stoelen waren niet comfortabel genoeg voor een lange voorstelling.',1),
(NULL, 1, 4004, 'Notificatie',  'Voorstelling Aurora Gala Avond is bijna uitverkocht.',                 1),
(4, 2,   4005, 'Notificatie',  'Uw ticket is succesvol gescand bij de ingang.',                        1);

-- Verificatie: toon alle tabellen met hun aantal rijen
SELECT 'Gebruiker' AS Tabel, COUNT(*) AS Aantal FROM Gebruiker
UNION ALL SELECT 'Rol',          COUNT(*) FROM Rol
UNION ALL SELECT 'Contact',      COUNT(*) FROM Contact
UNION ALL SELECT 'Medewerker',   COUNT(*) FROM Medewerker
UNION ALL SELECT 'Bezoeker',     COUNT(*) FROM Bezoeker
UNION ALL SELECT 'Prijs',        COUNT(*) FROM Prijs
UNION ALL SELECT 'Voorstelling', COUNT(*) FROM Voorstelling
UNION ALL SELECT 'Ticket',       COUNT(*) FROM Ticket
UNION ALL SELECT 'Melding',      COUNT(*) FROM Melding;