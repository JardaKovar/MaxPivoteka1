# 🏪 MAX PIVOTÉKA - Webové stránky

Webové stránky pro prodejnu piv MAX PIVOTÉKA v Benešově. Obsahuje ceník, akce, galerii, půjčovnu a rezervační systém.

## 📋 Požadavky

- PHP 7.4 nebo vyšší
- MySQL 5.7 nebo vyšší
- Webový server (Apache, Nginx)

## 🚀 Lokální vývoj (XAMPP)

1. Nainstaluj **XAMPP** z https://www.apachefriends.org/
2. Spusť **Apache** a **MySQL** v XAMPP Control Panelu
3. Naklonuj projekt do `C:/xampp/htdocs/htdocs3/` (nebo `D:/xampp/htdocs/htdocs3/`)
4. Otevři v prohlížeči:
   - Web: `http://localhost/htdocs3/`
   - Dashboard: `http://localhost/htdocs3/dashboard.php`
   - phpMyAdmin: `http://localhost/phpmyadmin`

### 🛠 Rychlé spuštění
Dvojklikem na `start.bat` se automaticky spustí Apache, MySQL a otevře se web v prohlížeči.

## 🌐 Nahrání na hosting (InfinityFree.net)

### Krok 1: Nahraj soubory na server
1. Zaregistruj se na https://infinityfree.net/
2. Vytvoř nový hostingový účet
3. Získej FTP přístup (IP adresa, uživatelské jméno, heslo)
4. Nahraj **všechny soubory** z tohoto projektu (kromě složek `dashboard/`, `xampp/`, `webalizer/`, `node_modules/`, `MAX PIVOTÉKA_files/`, `MaxWebDatabase/`)
5. Hlavní složka na serveru je obvykle `htdocs/` nebo `public_html/`

### Krok 2: Vytvoř databázi
1. V cPanelu na InfinityFree klikni na **MySQL Databases**
2. Vytvoř novou databázi (poznamenej si název)
3. Vytvoř uživatele databáze (poznamenej si uživatelské jméno a heslo)
4. Přidej uživatele k databázi s **ALL PRIVILEGES**
5. Otevři **phpMyAdmin**
6. Vyber svou databázi
7. Klikni na záložku **SQL**
8. Otevři soubor `export_databaze.sql`, zkopíruj celý jeho obsah
9. Vlož ho do textového pole v phpMyAdmin
10. Klikni na **Provést**

### Krok 3: Uprav připojení k databázi
Otevři soubor `db_config.php` a nahraď údaje:
```php
$db_host = 'sql123.infinityfree.com';   // Z emailu od InfinityFree
$db_name = 'if0_12345678_nazev';         // Název databáze
$db_user = 'if0_12345678';               // Uživatelské jméno
$db_pass = 'tvoje_heslo';                // Heslo
```

### Krok 4: Hotovo!
- Web: `https://tvoje-domena.infinityfreeapp.com/`
- Dashboard: `https://tvoje-domena.infinityfreeapp.com/dashboard.php`

## 🔑 Přihlašovací údaje do dashboardu

| Uživatel | Heslo (výchozí) | Přístup |
|----------|-----------------|---------|
| MaxZ | FerdaBN25 | Základní |
| Admin | NiggaFaggot1224 | Plný přístup |
| MaxP | BeneP04 | Plný přístup |

## 📁 Struktura projektu

| Složka/Soubor | Popis |
|---------------|-------|
| `index.php` | Hlavní webová stránka |
| `dashboard.php` | Administrační rozhraní |
| `login.php` | Přihlašovací stránka |
| `db_config.php` | **Nastavení databáze** (uprav při nahrání na hosting) |
| `export_databaze.sql` | **Export celé databáze** (importovat do phpMyAdmin) |
| `start.bat` | Rychlé spuštění XAMPP (pouze lokálně) |
| `data/` | JSON soubory s daty (fallback) |
| `images/` | Obrázky, galerie, ceník |
| `js/` | JavaScript soubory |
| `css/` | CSS styly |

## 🛟 Řešení problémů

### "Database connection failed"
1. Zkontroluj údaje v `db_config.php`
2. Ujisti se, že databáze existuje
3. Na InfinityFree může chvíli trvat, než se databáze aktivuje (až 24h)

### Web se zobrazuje, ale data nejsou vidět
- Projekt používá **JSON fallback** - pokud databáze není dostupná, bere data z `data/` složky
- Po nahrání na hosting se data začnou ukládat do databáze

### 404 Error
- Ujisti se, že jsi soubory nahrál do správné složky (obvykle `htdocs/` nebo `public_html/`)
- Zkus otevřít `https://tvoje-domena.infinityfreeapp.com/index.php`
