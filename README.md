<Réthey-Prikkel Krisztián>
<RGCFPD>

Webprogramozás - számonkérés

Kijelentem, hogy ez a megoldás a saját munkám. Nem másoltam vagy használtam harmadik féltől származó megoldásokat. Nem továbbítottam megoldást hallgatótársaimnak, és nem is tettem közzé. Nem használtam mesterséges intelligencia által generált kódot, kódrészletet. Az ELTE HKR 377/A. § értelmében, ha nem megengedett segédeszközt veszek igénybe, vagy más hallgatónak nem megengedett segítséget nyújtok, a tantárgyat nem teljesíthetem.

ELTE Hallgatói Követelményrendszer, IK kari különös rész, 377/A. §: "Az a hallgató, aki olyan tanulmányi teljesítménymérés (vizsga, zárthelyi, beadandó feladat) során, amelynek keretében számítógépes program vagy programmodul elkészítése a feladat, az oktató által meghatározottakon kívül más segédeszközt vesz igénybe, illetve más hallgatónak meg nem engedett segítséget nyújt, tanulmányi szabálytalanságot követ el, ezért az adott félévben a tantárgyat nem teljesítheti és a tantárgy kreditjét nem szerezheti meg."

### Minimálisan teljesítendő (enélkül nem fogadjuk el, 6 pont)

[X] `0.0 pont` Readme.md fájl: kitöltve, feltöltve
[X] `0.0 pont` Főoldal: megjelenik
[X] `1.0 pont` Főoldal: összes kártya listázása, pl. képekkel
[X] `1.0 pont` Főoldal: a kártya nevére kattintva a megfelelő kártya részletek oldalra jutunk
[X] `1.0 pont` Kártya részletek: Megjelenik a kártyán szereplő szörny neve, életereje, leírása és eleme
[X] `0.5 pont` Kártya részletek: Megjelenik a kártyához tartozó kép
[X] `0.5 pont` Kártya részletek: A kártyán szereplő szörny eleme szerint változik az oldalon egy vagy több elem színe vagy háttérszíne, pl.: Fire esetén piros, Lightning esetén sárga, stb.
[X] `2.0 pont` Admin: Új kártya létrehozása: hibakezelés, sikeres mentés

### Az alap feladatok (14 pont)

[X] `0.5 pont` Regisztrációs űrlap: Megfelelő elemeket tartalmazza
[X] `0.5 pont` Regisztrációs űrlap: Hibás esetek kezelése, hibaüzenet, állapottartás
[X] `0.5 pont` Regisztrációs űrlap: Sikeres regisztráció
[X] `0.5 pont` Bejelentkezés: Hibás esetek kezelése
[X] `0.5 pont` Bejelentkezés: Sikeres bejelentkezés
[X] `0.5 pont` Kijelentkezés
[X] `0.5 pont` Főoldal: Megjelenik a felhasználó neve és pénze
[X] `0.5 pont` Főoldal: A felhasználónévre kattintva a felhasználó részletei oldalra jutunk
[X] `1.0 pont` Főoldal: Lehessen a kártyákat típus szerint szűrni.
[X] `0.5 pont` Felhasználó részletek: Megjelenik a felhasználó neve, e-mail címe, pénze
[X] `0.5 pont` Felhasználó részletek: Megjelennek a felhasználóhoz tartozó kártyák
[X] `2.0 pont` Felhasználó részletek: A felhasználó kártyái mellett megjelenik egy eladás gomb, amivel a felhasználó el tudja adni az adott kártyát, ekkor törlődik a kártyái közül az eladott lap és megkapja a kártya árának 90%-át. Az eladott lap visszakerül az ADMIN paklijába. (Azt hogy az eladás gombot hova helyezed és hogyan valósítod meg rád van bízva)
[X] `0.5 pont` Admin: Be lehet jelentkezni az admin felhasználó adataival
[X] `0.5 pont` Admin: Új kártya létrehozása csak Admin felhasználóval érhető el
[X] `0.5 pont` Főoldal: Ha be van jelentkezve jelenjen meg egy vásárlás gomb minden kártya alatt
[X] `1.5 pont` Főoldal (Vásárlás): Meg tudja venni a kártyát
[X] `0.5 pont` Főoldal (Vásárlás): Csak annyit tud venni amennyi pénze van
[X] `0.5 pont` Főoldal (Vásárlás): Legfeljebb 5 kártyája lehet
[X] `1.0 pont` Igényes kialakítás

### Plusz feladatok (max plusz 5 pont)

[X] `0.5 pont` Admin: Kártya módosítása: admin felhasználóval elérhető, még el nem adott kártyák esetén
[X] `0.5 pont` Admin: Kártya módosítása: hibakezelés, állapottartás, sikeres mentés
[X] `1.0 pont` Főoldal: A főoldal egy gombra kattintva a nem admin felhasználók tudjanak venni egy véletlenszerű kártyát a pénzükből, egy random kártya ára lehet pl.: 50 coin.
[X] `2.0 pont` Főoldal: A főoldalon egyszerre csak 9 kártya jelenjen meg, alattuk lehessen navigálni az oldalakon (oldalszámokkal, nyilakkal). Mindig az aktuális oldalszámnak megfelelő kártyák jelenjenek meg, minden oldalon a következő 9 kártya. A megoldáshoz használj AJAX-ot/fetch-et.
[X] `1.0 pont` Csere 1. lépés: A főoldalon azoknál a kártyáknál, amely nem az adminnál és nálunk van jelenjen meg egy csere gomb, amire kattintva önhatalmúlag cserélhesse el ezen kártyára egy tetszőleges kártyáját a felhasználó.
[X] `1.0 pont` Csere 2. lépés: A csere ne azonnali és önhatalmú legyen, hanem a másik fél kapjon róla értesítést, és fogadhassa vagy utasíthassa el.
[X] `1.0 pont` Csere 3. lépés: A cseréhez lehessen pénzt is hozzáadni bármely oldalra. Figyelj a negatív számokra!
