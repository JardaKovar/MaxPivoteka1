# 📖 NÁVOD KROK ZA KROKEM - Nahrání webu na InfinityFree

## Co budeš potřebovat:
- ✅ Projekt (už ho máš v `D:/Projects/MAX- finale 2/htdocs3`)
- ✅ Email (pro registraci)
- ✅ Čas (asi 15-20 minut)

---

## 🔴 KROK 1 - Registrace na InfinityFree

1. **Otevři prohlížeč** a jdi na: https://www.infinityfree.com/

2. Klikni na tlačítko **"Get Free Hosting"** nebo **"Register"**

3. Vyplň:
   - **Email** - tvůj email (např. `tvoje@email.cz`)
   - **Password** - nějaké heslo (zapiš si ho!)
   - Potvrď, že nejsi robot

4. Klikni **"Create Account"**

5. **Otevři svůj email** - přijde ti potvrzovací zpráva od InfinityFree
   - Klikni na odkaz v emailu pro potvrzení registrace

---

## 🟠 KROK 2 - Vytvoření hostingového účtu

1. **Přihlas se** na https://www.infinityfree.com/ (svým emailem a heslem)

2. Uvidíš **ovládací panel** - klikni na **"Create Account"** (vytvořit účet)

3. Vyplň:
   - **Domain** (doména) - vyber něco jako:
     
```
     maxpivoteka.infinityfreeapp.com
     
```
     (zkontroluj, jestli je doména volná - musí být zeleně)
   
   - **Username** - nech vygenerované nebo změň
   
   - **Password** - nech vygenerované nebo si nastav vlastní
   
   - **Account Type** - nech **Free**

4. Klikni **"Create"**

5. **Počkej pár minut** - účet se vytváří

6. Až bude hotovo, uvidíš svůj účet v seznamu. Klikni na **"Control Panel"** (cPanel)

---

## 🟡 KROK 3 - Najdi FTP a databázové údaje

1. V cPanelu najdi sekci **"FTP Accounts"** nebo **"FTP"**
   - Uvidíš:
     - **FTP Hostname** (např. `ftp.infinityfree.com` nebo IP adresa)
     - **FTP Username**
     - **FTP Password**

2. **Zapiš si tyto údaje!** Budou potřeba pro nahrání souborů.

3. Teď najdi sekci **"MySQL Databases"**
   - Klikni na ni

---

## 🟢 KROK 4 - Vytvoření databáze

1. V sekci MySQL Databases:
   - Do pole **"New Database"** napiš: `maxpivoteka`
   - Klikni **"Create Database"**
   - **Zapiš si celý název databáze** (bude něco jako `if0_12345678_maxpivoteka`)

2. Přejdi dolů do sekce **"New User"**:
   - **Username:** napiš `admin`
   - **Password:** vymysli nějaké heslo (zapiš si ho!)
   - Klikni **"Create User"**
   - **Zapiš si celé uživatelské jméno** (bude něco jako `if0_12345678_admin`)

3. Přejdi do sekce **"Add User to Database"**:
   - Vyber uživatele a databázi
   - Zaškrtni **"ALL PRIVILEGES"** (všechna oprávnění)
   - Klikni **"Add"**

---

## 🔵 KROK 5 - Import databáze

1. V cPanelu najdi **phpMyAdmin** (ikona s databází)
   - Klikni na ni

2. V levém sloupci **klikni na svou databázi** (třeba `if0_12345678_maxpivoteka`)

3. Klikni na záložku **"SQL"** v horním menu

4. **Otevři soubor** `export_databaze.sql` (je v `D:/Projects/MAX- finale 2/htdocs3/export_databaze.sql`)
   - Otevři ho v Poznámkovém bloku (Notepad)
   - **Zvýrazni celý obsah** (Ctrl+A)
   - **Zkopíruj** (Ctrl+C)

5. Vrať se do phpMyAdmin
   - Do velkého textového pole **vlož obsah** (Ctrl+V)

6. Klikni na tlačítko **"Provést"** nebo **"Go"** dole

7. Mělo by se objevit **"Dotaz byl úspěšně proveden"** a vlevo uvidíš tabulky (users, events, taplist, atd.)

---

## 🟣 KROK 6 - Nahrání souborů (potřebuješ FTP program)

1. **Stáhni si FTP program** - doporučuji **FileZilla** (zdarma):
   - Jdi na: https://filezilla-project.org/
   - Klikni na **"Download FileZilla Client"**
   - Nainstaluj

2. **Otevři FileZilla**

3. Vyplň nahoře:
   - **Host:** (to co jsi opsal z FTP Accounts, třeba `ftp.infinityfree.com`)
   - **Username:** (to co jsi opsal)
   - **Password:** (to co jsi opsal)
   - **Port:** nech prázdné

4. Klikni **"Quickconnect"** (Rychlé připojení)

5. **Počkej až se připojí** - v pravém sloupečku uvidíš složky na serveru

6. V pravém sloupečku **dvojklikem otevři složku** `htdocs/` (nebo `public_html/`)

7. V **levém sloupečku** najdi tvůj projekt:
   
```
   D:/Projects/MAX- finale 2/htdocs3/
   
```

8. **Zvýrazni všechny soubory a složky** v levém sloupečku (kromě těchto, které NE Nahraj):
   - ❌ `dashboard/`
   - ❌ `xampp/`
   - ❌ `webalizer/`
   - ❌ `node_modules/`
   - ❌ `MAX PIVOTÉKA_files/`
   - ❌ `MaxWebDatabase/`
   - ❌ `.git/`

9. **Klikni pravým tlačítkem** na zvýrazněné soubory → **"Upload"** (Nahrát)

10. **Počkej až se vše nahraje** - může to pár minut trvat

---

## 🟤 KROK 7 - Úprava db_config.php (NEJDŮLEŽITĚJŠÍ!)

Teď musíš upravit soubor `db_config.php` na serveru.

### Možnost A: Upravit přes FileZillu
1. V FileZille (pravý sloupeček - server) najdi `db_config.php`
2. Klikni na něj pravým tlačítkem → **"View/Edit"**
3. Otevře se ti soubor v Poznámkovém bloku
4. Najdi tyto řádky:

```php
$db_host = 'localhost';
$db_name = 'maxpivoteka_dashboard';
$db_user = 'root';
$db_pass = '';
```

5. **Přepiš je na údaje z InfinityFree:**
```php
$db_host = 'sql123.infinityfree.com';     // Toto dostaneš v emailu od InfinityFree!
$db_name = 'if0_12345678_maxpivoteka';    // Název tvé databáze
$db_user = 'if0_12345678_admin';          // Tvé uživatelské jméno
$db_pass = 'tvoje_nastavene_heslo';       // Tvé heslo
```

6. **Ulož soubor** (Ctrl+S)
7. Zavři Poznámkový blok
8. FileZilla se zeptá "Soubor byl změněn. Nahrát na server?" → Klikni **"Yes"**

### Možnost B: Upravit lokálně a znovu nahrát
1. Otevři `D:/Projects/MAX- finale 2/htdocs3/db_config.php` v Poznámkovém bloku
2. Udělej stejné změny jako v Možnosti A
3. Ulož
4. V FileZille najdi soubor v pravém sloupečku a přetáhni ho z levého sloupečku (přepíše se)

---

## 🟢 KROK 8 - HOTOVO! Otevři web!

1. Otevři prohlížeč a jdi na:
   
```
   https://maxpivoteka.infinityfreeapp.com/
   
```
   (nebo jakou doménu jsi zvolil)

2. **Měla by se zobrazit tvá webová stránka!** 🎉

3. **Dashboard (administrace):**
   
```
   https://maxpivoteka.infinityfreeapp.com/dashboard.php
   
```

4. **Přihlašovací údaje:**
   | Uživatel | Heslo |
   |----------|-------|
   | MaxZ | FerdaBN25 |
   | Admin | NiggaFaggot1224 |
   | MaxP | BeneP04 |

---

## 💡 Důležité tipy

### ⚠️ Databáze se nemusí aktivovat hned!
Na InfinityFree může trvat **až 24 hodin**, než se databáze aktivuje. Do té doby bude web fungovat s **JSON fallbackem** (data ze složky `data/`).

### 🔑 Kam zapsat údaje:
Doporučuji si uložit tyto údaje někam do poznámek:

```
=== INFINITYFREE ÚDAJE ===

Přihlašovací email: _______________
Heslo k účtu:       _______________

Doména:             maxpivoteka.infinityfreeapp.com

FTP Host:           ftp.infinityfree.com
FTP User:           _______________
FTP Password:       _______________

Databáze:           if0_12345678_maxpivoteka
DB User:            if0_12345678_admin
DB Password:        _______________
DB Host:            sql123.infinityfree.com  ★
```
*(★ - hostitel databáze najdeš v emailu od InfinityFree nebo v cPanelu)*

---

## 🆘 Stále nefunguje?

Pokud se web nezobrazí:
1. **Zkus vyčistit cache** prohlížeče (Ctrl+F5)
2. **Zkus jiný prohlížeč**
3. **Zkontroluj, jestli jsou soubory nahoře** (přes FileZillu)
4. **Zkontroluj, jestli máš správné údaje v `db_config.php`**

Pokud je problém s databází, web by měl fungovat alespoň částečně díky JSON souborům.

**Neboj se zeptat, když něco nefunguje!** 😊
