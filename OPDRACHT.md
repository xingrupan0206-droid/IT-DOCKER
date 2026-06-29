# Opdracht: Contactformulier met Docker

**Niveau:** 1e jaar Software Developer / Applicatieontwikkeling

## Situatieschets

Je hebt van een collega een werkend PHP-contactformulier gekregen. Een bezoeker
kan een formulier invullen. Het bericht moet:

1. opgeslagen worden in een **MySQL-database**, en
2. per **e-mail** verstuurd worden naar `support@mboutrecht.nl`.

Het PHP-gedeelte is al voor je gemaakt. **Jouw taak** is om het project draaiend
te krijgen met **Docker** en **Docker Compose**, zodat iedereen het project met
één commando kan opstarten.

## Wat is al af?

De map `src/` bevat de complete PHP-applicatie:

| Bestand | Functie |
|---|---|
| `index.php` | De pagina met het contactformulier |
| `verzend.php` | Verwerkt het formulier: slaat op in DB + verstuurt mail |
| `style.css` | Opmaak van de pagina |
| `includes/db.php` | Maakt verbinding met de database |
| `includes/mail.php` | Verstuurt de e-mail via SMTP |
| `config/database.php` | Database-instellingen |
| `config/mail.php` | Mail-instellingen |

De map `db/` bevat `init.sql`: dit maakt automatisch de database en de tabel
`berichten` aan.

## Wat moet jij doen?

Je vult twee bestanden in die nu **leeg** zijn:

### 1. `docker/php/Dockerfile`

Maak een image gebaseerd op een PHP + Apache image. Zorg dat de PHP-extensie
voor MySQL (`pdo_mysql`) geïnstalleerd is, want de applicatie gebruikt PDO.

### 2. `docker-compose.yml`

Definieer **vier** services:

| Service | Image / build | Taak |
|---|---|---|
| `apache` | build uit `docker/php/Dockerfile` | Draait de PHP-applicatie (map `src/`) |
| `mysql` | `mysql:8.0` | De database. Laad `db/init.sql` automatisch in |
| `phpmyadmin` | `phpmyadmin` | Beheer de database via de browser |
| `mailtrap` | een mailcatcher-image (zie hint) | Vangt uitgaande e-mail af |

### Aandachtspunten

- De webserver moet de map `src/` als webroot gebruiken.
- De database-gegevens in `config/database.php` (host `mysql`, database
  `contactdb`, gebruiker `appuser`, wachtwoord `apppass`) moeten overeenkomen
  met wat je in `docker-compose.yml` instelt.
- De mail-instellingen in `config/mail.php` verwachten host `mailtrap` op poort
  `1025`. Zorg dat je mailcatcher op die poort luistert.
- Gebruik **volumes** zodat je codewijzigingen direct zichtbaar zijn zonder de
  image opnieuw te bouwen.

### Hint voor de mailcatcher

Mailtrap heeft een gratis online dienst, maar voor deze opdracht draai je een
lokale variant in Docker. Zoek naar een image dat zowel een **SMTP-poort (1025)**
als een **webinterface** aanbiedt om afgevangen mail te bekijken. Zoektermen:
"mailpit" of "mailhog".

## Opstarten en testen

1. Bouw en start alles:
   ```
   docker compose up -d --build
   ```
2. Open de applicatie in je browser (bv. `http://localhost:8080`).
3. Vul het formulier in en verstuur het.
4. Controleer in **phpMyAdmin** of het bericht in de tabel `berichten` staat.
5. Controleer in de **webinterface van je mailcatcher** of de e-mail is afgevangen.

## Inleveren

- Je ingevulde `docker-compose.yml` en `Dockerfile`.
- Een screenshot van het ingevulde formulier met succesmelding.
- Een screenshot van het bericht in phpMyAdmin.
- Een screenshot van de afgevangen mail in de mailcatcher.
- Korte uitleg (max. half A4) over hoe de vier containers samenwerken.

## Beoordeling

| Onderdeel | Punten |
|---|---|
| Dockerfile correct (PHP + Apache + pdo_mysql) | 20% |
| docker-compose.yml: alle 4 services draaien | 30% |
| Formulier werkt en slaat op in database | 20% |
| Mail wordt afgevangen door mailcatcher | 20% |
| Uitleg samenwerking containers | 10% |
