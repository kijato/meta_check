## DAT(R) META-adat ellenőrző

A webapplikáció a DATR-ben tárolt META-adatok ellenőrzésének és javításának támogatására készült.

Jelen állapotában képes megmutatni:
- azokat a hrsz-eket, melyek a META "adatbázisból" hiányoznak,
- a megadott hrsz tartomány alapján a BC-BD táblákból kiolvasott min(y,x) - max(y,x) értékeket, azaz a befoglaló téglalapot.

#### Rendszerkövetelmények:
- PHP-képes webszerver
- Oracle Instant Client
- az előző két tétel helyes beállításából eredő, működőképes PHP-OCI8 kiterjesztés
- a 'config-minta.php' fájl mintájára készült 'config.php'

#### Fejlesztési célok:
- az aktuális befoglaló téglalap helyességének ellenőrzése az adott tartományhoz tartozó, BC-BD táblákból kiolvasott min(y,x) - max(y,x) értékekk alapján
- azon META tól-ig határ-hrszok listázása, melyek nem létező hrsz-ekre hivatkoznak
- részletes META adatok listázása *(nem csak az "összefoglaló")*
- a jogszabálynak megfelelő META adatlap kiadás az iktatószám alapján

#### Képernyőkép
![Képernyőkép](https://github.com/kijato/meta_check/blob/main/meta_check_screenshot.png)
