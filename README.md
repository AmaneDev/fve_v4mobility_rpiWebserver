# fve_v4mobility_rpiWebserver

Web PHP server for solar panel with sun tracker, which was created by cooperation of two schools SŠE Lipník nad Bečvou and SOŠ Žarnovica within the Visegrad Fund project

Webový PHP server pro solární panel s trackerem slunce, který vznikl spoluprací dvou škol SŠE Lipník nad Bečvou a SOŠ Žarnovica v rámci projektu Visegrad Fund

CZECH:

# Požadavky
- Minimálně PHP 7.0
- Povolené PDO a SQlite extension v php.ini
- Databáze ve formátu SQLite

# Jak nainstalovat?

1. Přesuňtě všechna data do složky htdocs (XAMPP), případně WWW.
2. Otevřete v kořenové složce soubor conf.inc a upřesněte cestu k SQLite DB souboru, případně nastavte další nutné parametry.
3. Ujistěte se, že používáte alespoň PHP 7.0
4. Otevřete (v závislosti na Vaší instalaci) konfigurační soubor (php.ini) a povolte rozšíření pro SQLite a PDO
5. Hotovo


ENGLISH:

# Requirements
- Minimum PHP 7.0
- Enabled PDO and SQlite extension in php.ini
- Database in SQLite format

# How to install?

1. Move all the data to the htdocs (XAMPP) or WWW folder.
2. Open the conf.inc file in the root folder and specify the path to the SQLite DB file, or set other necessary parameters.
3. Make sure you are using at least PHP 7.0
4. Open (depending on your installation) the configuration file (php.ini) and enable the extensions for SQLite and PDO
5. Done