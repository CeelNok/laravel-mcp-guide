# Laravel MCP Guide 🔧
 
> Gyakorlati útmutató MCP szerver építéséhez meglévő Laravel projektekben
 
---
 
## Mi az az MCP?
 
Az **MCP (Model Context Protocol)** egy nyílt protokoll, amelyet az Anthropic fejlesztett ki 2024-ben. A célja egyszerű: szabványos módon összekapcsolni az AI modelleket (pl. Claude) a külső eszközökkel, adatokkal és rendszerekkel.
 
Gondolj rá úgy, mint egy **USB szabványra az AI világában**.
 
Ahogy az USB lehetővé teszi, hogy bármilyen eszközt csatlakoztass bármilyen számítógéphez – az MCP lehetővé teszi, hogy az AI bármilyen rendszerhez hozzáférjen, amelyre felkészíted.
 
---
 
## Mire jó?
 
Képzeld el, hogy van egy webshopod Laravel-ben. Az AI önmagában nem tud belenyúlni az adatbázisodba, nem látja a rendeléseidet, nem tudja kezelni az ügyfeleidet.
 
**MCP szerverrel viszont képes lesz:**
 
- 📦 Lekérdezni az utolsó rendeléseket
- 👤 Megkeresni egy ügyfelet név vagy email alapján
- 📊 Összeállítani egy napi forgalmi riportot
- 📝 Létrehozni vagy módosítani rekordokat az adatbázisban
- 🔔 Triggerelni bármilyen üzleti logikát
Mindezt úgy, hogy te csak annyit írsz a Claude-nak: *„Listázd ki a mai sikertelen fizetéseket"* – és az AI elvégzi a munkát a saját rendszeredben.
 
---
 
## Hogyan működik?
 
Az MCP egy kliens-szerver architektúrán alapul:
 
```
Claude (kliens)  ←→  MCP Szerver (a te Laravel appod)  ←→  Adatbázis / API-k
```
 
A szervered **Tool**-okat, **Resource**-okat és **Prompt**-okat tud kiszolgálni:
 
| Típus | Mire való | Példa |
|-------|-----------|-------|
| **Tool** | Műveletek végrehajtása | `get_orders`, `create_invoice` |
| **Resource** | Adatok olvasása | dokumentumok, fájlok, konfigok |
| **Prompt** | Előre definiált AI utasítások | sablon promptok |
 
Ez a sorozat elsősorban a **Tool**-okra fókuszál, mert ezek a leggyakrabban használt és leginkább hasznos elemek.
 
---
 
## Miért Laravel?
 
A Laravel kiváló választás MCP szerver építéséhez:
 
- **Routing** – egyszerűen definiálhatók az MCP végpontok
- **Eloquent** – az adatbázis elérés tiszta és gyors
- **Middleware** – autentikáció és jogosultságkezelés könnyedén
- **Artisan** – a tooling már adott a fejlesztéshez
Ha már van egy működő Laravel projekted, az MCP integráció néhány órás munka – nem kell mindent újraírni.
 
---
 
## A sorozatról
 
Ez egy **gyakorlatorientált sorozat**. Minden rész egy konkrét problémát old meg, példakóddal együtt.
 
| # | Téma | Státusz |
|---|------|---------|
| 01 | [MCP alapok – integráció meglévő Laravel projektbe](./01-alapok-meglevo-projektbe/) | 🚧 Hamarosan |
| 02 | Autentikáció és biztonság | 📅 Tervezve |
| 03 | Haladó tool-ok és kompozíció | 📅 Tervezve |
 
---
 
## Kinek szól?
 
- **Laravel fejlesztőknek**, akik szeretnék AI-jal kiegészíteni a meglévő projektjeiket
- **PHP senioroknak**, akik már ismerik az MCP elméletet, de nem tudják hol kezdjék
- **Kíváncsi junioroknak**, akik szeretnék megérteni hogyan kapcsolódik össze az AI és a backend
---