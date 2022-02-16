# Zadání projektu z předmětu IPP 2021/

```
Zbyněk Křivka
E-mail:krivka@fit.vut.cz
```
# 1 Základní charakteristika projektu

Navrhněte, implementujte, dokumentujte a testujte sadu skriptů pro interpretaci nestrukturovaného
imperativního jazyka IPPcode22. K implementaci vytvořte odpovídající stručnou programovou do-
kumentaci. Projekt se skládá ze dvou úloh a je individuální.
První úloha se skládá ze skriptuparse.php v jazyce PHP 8.1 (viz sekce 3 ) a dokumentace
k tomuto skriptu (viz sekce2.1). Druhá úloha se skládá ze skriptuinterpret.pyv jazyce Python
3.8 (viz sekce 4 ), testovacího skriptutest.phpv jazyce PHP 8.1 (viz sekce 5 ) a dokumentace těchto
dvou skriptů (viz sekce2.1).

# 2 Požadavky a organizační informace

Kromě implementace skriptů a vytvoření dokumentace je třeba dodržet také řadu následujících for-
málních požadavků. Pokud některý nebude dodržen, může být projekt hodnocen nula body! Pro
kontrolu alespoň některých formálních požadavků lze využít skriptis_it_ok.shdostupný v _Soubo-
rech_ předmětu IPP.

**Termíny:**

```
připomínky^1 k zadání projektu do 13. února 2022;
```
```
fixace zadání projektu od 14. února 2022;
```
```
odevzdání první úlohy ve středu 16. března 2022 do 23:59:59;
```
```
odevzdání druhé úlohy v úterý 19. dubna 2022 do 23:59:59.
```
**Dodatečné informace a konzultace k projektu z IPP:**

- _Wiki_ a _E-Learning_ (Moodle)předmětu IPP včetně Často kladených otázek ( _FAQ_ ).
- _Fórum_ v _E-Learning_ (Moodle)IPP pro ak. rok 2021/2022, témataProjekt.*.
- Zbyněk Křivka (garant projektu): dle konzultačních slotů (vizwebová stránka) nebo po dohodě
    e-mailem(uvádějte předmět začínající"IPP:"), vizhttp://www.fit.vut.cz/person/krivka.
    Další cvičící najdete na kartě předmětu.
- Dušan Kolář (garant předmětu; jen v závažných případech): po dohoděe-mailem(uvádějte
    předmět začínající"IPP:"), vizhttp://www.fit.vut.cz/person/kolar.

(^1) Naleznete-li v zadání nějakou chybu či nejasnost, dejte prosím vědět na _Fóru_ předmětu nebo e-mailem na
krivka@fit.vut.cz. Validní připomínky a upozornění na chyby budou oceněny i bonusovými body.


Pokud máte jakékoliv dotazy, problémy či nejasnosti ohledně tohoto projektu, tak po přečtení
_FAQ_ a _Fóra_ předmětu (využívejte i možnosti hledání na _Fóru_ ), neváhejte napsat na _Fórum_ (u o-
becného problému, jenž se potenciálně týká i vašich kolegů) či kontaktovat garanta projektu, cvičí-
cího (v případě individuálního problému), nebo nouzově garanta předmětu, kdy do předmětu vždy
uveďte na začátek řetězec"IPP:". Na problémy zjištěné v řádu hodin až jednotek dní před termí-
nem odevzdání některé části projektu nebude brán zřetel. Začněte proto projekt řešit s dostatečným
předstihem.

**Forma a způsob odevzdání:** Každá úloha se odevzdává individuálně prostřednictvím IS FIT
v předmětu IPP (odevzdání e-mailem je bodově postihováno) a odevzdáním stvrzujete výhradní
autorství skriptů i dokumentace.
Do termínu
”
Projekt - 1. úloha v PHP 8.1“odevzdáte archív pro první úlohu v jazyce PHP 8.
(skriptparse.php; včetně dokumentace tohoto skriptu). Po termínu odevzdání 1. úlohy bude otevřen
termín
”
Projekt - 2. úloha v Pythonu 3.8 a testovací skript v PHP 8.1“pro odevzdání archívu pro
druhou úlohu (skriptyinterpret.pyatest.php; včetně dokumentace pro obsažené skripty). Sou-
částí archívu první úlohy mohou být i nedokončené skripty a dokumentace odevzdávané k hodnocení
až v druhé úloze a naopak v archívu druhé úlohy se smí vyskytovat skript z první úlohy a adresář
s vašimi testy.
Každá úloha bude odevzdána ve zvláštním archívu, kde budou soubory k dané úloze zkom-
primovány programem ZIP, TAR+GZIP či TAR+BZIP do jediného archívu pojmenovaného
xlogin99.zip,xlogin99.tgz, neboxlogin99.tbz, kde xlogin99je váš login. Velikost každého
archívu bude omezena informačním systémem (pravděpodobně na 1 MB). Archív nesmí obsaho-
vat speciální či binární spustitelné soubory. Názvy všech souborů mohou obsahovat pouze písmena
anglické abecedy, číslice, tečku, pomlčku a podtržítko. **Skripty budou umístěny v kořenovém
adresáři odevzdaného archívu**. Po rozbalení archívu na serveru _Merlin_ bude možné skript(y)
spustit. Archív smí obsahovat v rozumné míře pomocné adresáře (typicky pro vaše vlastní testy, vaše
knihovny a pomocné skripty nebo povolené knihovny, které nejsou nainstalovány na serveru _Merlin_ ).

**Hodnocení:** Výše základního bodového hodnocení projektu v předmětu IPP je **maximálně 20
bodů**. Navíc lze získat maximálně 5 bonusových bodů za kvalitní a podařené řešení některého z roz-
šíření nebo kvalitativně nadprůměrnou účast na _Fóru_ projektu apod.
Hodnocení jednotlivých skriptů:parse.phpaž 6 bodů;interpret.pyaž 8 bodů;test.phpaž
3 body. Tj. v součtu až **17 bodů**. Dokumentace a úprava zdrojových textů skriptů bude dohro-
mady hodnocena až **3 body** (až 1 bod za popis skriptuparse.php a až 2 body za popis skriptů
interpret.pyatest.php), avšak maximálně 30 % ze sumy hodnocení skriptů dané úlohy (tedy
v případě neodevzdání žádného funkčního skriptu v dané úloze bude samotná dokumentace hodno-
cena 0 body). Body za každou úlohu včetně její dokumentace se před vložením do IS FIT zaokrouhlují
na desetiny bodu.
Skripty budou spouštěny na serveru _Merlin_ příkazem: _interpret skript parametry_ , kde _interpret_
budephp8.1nebopython3.8, _skript_ a _parametry_ závisí na dané úloze a skriptu. Hodnocení většiny
funkčnosti bude zajišťovat automatizovaný nástroj. Kvalitu dokumentace, komentářů a strukturu
zdrojového kódu budou hodnotit cvičící.
Vaše skripty budou hodnoceny nezávisle na sobě, takže je možné odevzdat například jen skript
interpret.pynebo jentest.phpdo 2. úlohy bez odevzdání 1. úlohy.
Podmínky pro opakující studenty ohledně případného uznání hodnocení loňského projektu najdete
v _FAQ_ na _Wiki_ předmětu.


**Registrovaná rozšíření:** V případě implementace některých registrovaných rozšíření za bonusové
body bude odevzdaný archív obsahovat souborrozsireni, ve kterém uvedete na každém řádku
identifikátor jednoho implementovaného rozšíření^2 (řádky jsou ukončeny unixovým koncem řádku,
tj. znak s dekadickou ASCII hodnotou 10 ). V průběhu řešení mohou být zaregistrována nová rozšíření
úlohy za bonusové body (viz _Fórum_ předmětu IPP). Nejpozději do termínu pokusného odevzdání
dané úlohy můžete na _Fórum_ zasílat návrhy na nová netriviální rozšíření, která byste chtěli navíc
implementovat. Cvičící rozhodne o přijetí/nepřijetí rozšíření a hodnocení rozšíření dle jeho nároč-
nosti včetně přiřazení unikátního identifikátoru. Implementovaná rozšíření neidentifikovaná v sou-
borurozsireninebudou hodnocena.

**Pokusné odevzdání:** Pro zvýšení motivace studentů pro včasné vypracování úloh nabízíme kon-
cept nepovinného pokusného odevzdání. Výměnou za pokusné odevzdání do uvedeného termínu (cca
týden před finálním termínem) dostanete zpětnou vazbu v podobě zařazení do některého z pěti roz-
mezí hodnocení (0–10 %, 11–30 %, 31–50 %, 51–80 %, 81–100 %). Bude-li vaše pokusné odevzdání
v prvním rozmezí hodnocení, máte možnost osobně konzultovat důvod, pokud jej neodhalíte sami.
U ostatních rozmezí nebudou detailnější informace poskytovány.
Pokusné odevzdání bude relativně rychle vyhodnoceno automatickými testy a studentům zaslána
orientační informace o správnosti pokusně odevzdané úlohy z hlediska části automatických testů (tj.
nebude se jednat o finální hodnocení; proto nebudou sdělovány ani body či přesnější procentuální
hodnocení). Využití pokusného termínu není povinné, ale jeho nevyužití může být negativně vzato
v úvahu v případě reklamace hodnocení projektu.
Formální požadavky na pokusné odevzdání jsou totožné s požadavky na finální termín a odevzdání
se bude provádět do speciálních termínů
”
Projekt - Pokusné odevzdání 1. úlohy“do **9. března 2022**
a
”
Projekt - Pokusné odevzdání 2. úlohy“do **12. dubna 2022**. Není nutné zahrnout dokumentaci,
která spolu s rozšířeními pokusně vyhodnocena nebude. U skriptutest.phpbude pokusné hodnocení
omezeno jen na velmi základní automatizovatelné testy (bez manuální kontroly skutečného obsahu).

## 2.1 Dokumentace

Implementační dokumentace (dále jen dokumentace) musí být stručným a uceleným průvodcem **va-
šeho způsobu řešení** skriptů 1., resp. 2. úlohy. Bude vytvořena ve formátu **PDF** nebo **Markdown**
(viz [ 4 ]). Jakékoliv jiné formáty dokumentace než PDF či Markdown^3 (příponamd) budou ignoro-
vány, což povede ke ztrátě bodů za dokumentaci. Dokumentaci je možné psát buď česky, slovensky
(s diakritikou, formálně čistě), nebo anglicky (formálně čistě).
Dokumentace bude popisovat celkovou filozofii návrhu, interní reprezentaci, způsob a váš spe-
cifický postup řešení (např. řešení sporných případů nedostatečně upřesněných zadáním, konkrétní
řešení rozšíření, případné využití návrhových vzorů, implementované/nedokončené vlastnosti). Do-
kumentace může být doplněna např. o UML diagram tříd, navržený konečný automat, pravidla vámi
vytvořené gramatiky nebo popis jiných formalismů a algoritmů. Nicméně **nesmí obsahovat ani
částečnou kopii zadání**.
**Rozsah** textu dokumentace pro každý skript bude přibližně 1 normostrana. Vysázená dokumen-
tace 1. úlohy popisující skriptparse.phpby neměla přesáhnout 1 stranu A4 a u 2. úlohy (popisující
skriptyinterpret.pyatest.php) pak 2 strany A4. Doporučení pro sazbu: 10bodové písmo Times
New Roman pro text a Courier pro identifikátory a skutečně krátké úryvky zajímavého kódu (či
zpětné apostrofy v Markdown); nevkládejte žádnou zvláštní úvodní stranu, obsah ani závěr. V ro-

(^2) Identifikátory rozšíření jsou uvedeny u konkrétního rozšíření tučně.
(^3) Bude-li přítomna dokumentace v Markdown i PDF, bude hodnocena verze v PDF, kde je jistější sazba.


zumné míře je vhodné používat nadpisy první a případně druhé úrovně (12bodové a 11bodové písmo
Times New Roman či ## a ### v Markdown) pro vytvoření logické struktury dokumentace.
Nadpis a hlavička dokumentace^4 bude na prvních třech řádcích obsahovat:

Implementační dokumentace k %cislo%. úloze do IPP 2021/
Jméno a příjmení: %jmeno_prijmeni%
Login: %xlogin99%

kde%jmeno_prijmeni%je vaše jméno a příjmení,%xlogin99%váš login a%cislo%je číslo doku-
mentované úlohy.
Dokumentace bude v kořenovém adresáři odevzdaného archívu a pojmenovánareadme1.pdfnebo
readme1.mdpro první úlohu areadme2.pdfneboreadme2.mdpro druhou úlohu.
V rámci dokumentace bude hodnocena i funkční/objektová dekompozice a komentování zdrojo-
vých kódů (minimálně každá funkce a modul (třída) by měly mít komentář o svém účelu a parame-
trech; u složitějších funkcí okomentujte i omezení na vstupy či výstupy).

## 2.2 Programová část

Zadání projektu vyžaduje implementaci tří skriptů^5 , které mají parametry příkazové řádky a je defi-
nováno, jakým způsobem manipulují se vstupy a výstupy. Skript (vyjmatest.php) nesmí spouštět
žádné další procesy či příkazy operačního systému. Veškerá chybová hlášení, varování a ladicí výpisy
směřujte pouze na standardní chybový výstup, jinak pravděpodobně nedodržíte zadání kvůli modi-
fikaci definovaných výstupů (ať již do externích souborů, nebo do standardního výstupu). Jestliže
proběhne činnost skriptu bez chyb, vrací se návratová hodnota 0 (nula). Jestliže dojde k nějaké
chybě, vrací se chybová návratová hodnota větší jak nula. Chyby mají závazné chybové návratové
hodnoty:

- 10 - chybějící parametr skriptu (je-li třeba) nebo použití zakázané kombinace parametrů;
- 11 - chyba při otevírání vstupních souborů (např. neexistence, nedostatečné oprávnění);
- 12 - chyba při otevření výstupních souborů pro zápis (např. nedostatečné oprávnění, chyba
    při zápisu);
- 20 – 69 - návratové kódy chyb specifických pro jednotlivé skripty;
- 99 - interní chyba (neovlivněná vstupními soubory či parametry příkazové řádky; např. chyba
    alokace paměti).

Pokud zadání níže nestanoví jinak, veškeré vstupy a výstupy jsou v kódování UTF-8. Pro
účely projektu z IPP musí být na serveru _Merlin_ ponecháno implicitní nastavení locale^6 , tj.
LC_ALL=cs_CZ.UTF-8.
Jména hlavních skriptů jsou dána zadáním. Pomocné skripty nebo knihovny budou mít příponu
dle zvyklostí v daném programovacím jazyce (.phppro PHP 8 a.pypro Python 3). Vyhodnocení

(^4) Anglické znění nadpisu a hlavičky dokumentace najdete v _FAQ_ na _Wiki_ předmětu.
(^5) Tyto skripty jsou aplikace příkazové řádky neboli konzolové aplikace.
(^6) Správné nastavení prostředí je nezbytné, aby bylo možné používat a správně zpracovávat parametry příkazové
řádky v UTF-8. Pro správnou funkčnost je třeba mít na UTF-8 nastaveno i kódování znakové sady konzole (např. u
programu PuTTY v kategorii _Window.Translation_ nastavíte _Remote character set_ na UTF-8). Pro změnu ovlivňující
aktuální sezení lze využít unixový příkazexport LC_ALL=cs_CZ.UTF-8.


skriptů bude prováděno na serveru _Merlin_ s aktuálními verzemi interpretů (dne 24. 1. 2022 bylo na
tomto serveru nainstalovánophp8.1^7 verze8.1.2apython3.8^8 verze3.8.12).
K řešení lze využít standardně předinstalované knihovny obou jazykových prostředí na serveru
_Merlin_. Případné využití jiné knihovny kromě knihovny podporující načítání/ukládání formátu XML,
zpracování parametrů příkazové řádky a zpracování regulárních výrazů je třeba konzultovat s garan-
tem projektu (především z důvodu, aby se řešení projektu použitím vhodné knihovny nestalo zcela
triviálním). Seznam povolených a zakázaných knihoven bude udržován aktuální na _Wiki_ předmětu.
Ve skriptech v jazyce PHP jsou některé funkce z bezpečnostních důvodů zakázány (např.header,
mail,popen,curl_exec,socket_*; úplný seznam je u povolených/zakázaných knihoven na _Wiki_ ).
Každý skript bude pracovat s jedním společným parametrem:

- --help vypíše na standardní výstup nápovědu skriptu (nenačítá žádný vstup), kterou lze
    převzít ze zadání (lze odstranit diakritiku, případně přeložit do angličtiny dle zvoleného jazyka
    dokumentace), a vrací návratovou hodnotu 0. Tento parametr nelze kombinovat s žádným
    dalším parametrem, jinak skript ukončete s chybou 10.

Kombinovatelné parametry skriptů jsou odděleny alespoň jedním bílým znakem a mohou být
uváděny v libovolném pořadí, pokud nebude řečeno jinak. U skriptů je možné implementovat i vaše
vlastní nekolizní parametry (doporučujeme konzultaci na _Fóru_ nebo ugaranta projektu).
Není-li řečeno jinak, tak dle konvencí unixových systémů lze uvažovat zástupné zkrácené (s jednou
pomlčkou) i dlouhé parametry (se dvěma pomlčkami), které lze se zachováním sémantiky zaměňovat
(tzv. alias parametry), ale testovány budou vždy dlouhé verze.
Je-li součástí parametru i soubor (např.--source= _file_ nebo--source=" _file_ ") či cesta, může
být tento soubor/cesta zadán/a relativní cestou^9 nebo absolutní cestou; výskyt znaku uvozovek a
rovnítka ve _file_ neuvažujte. Cesty/jména souborů mohou obsahovat i Unicode znaky v UTF-8.

# 3 Analyzátor kódu v IPPcode22 (parse.php)

Skript typu filtr (parse.phpv jazycePHP 8.1) načte ze standardního vstupu zdrojový kód v IPP-
code22 (viz sekce 6 ), zkontroluje lexikální a syntaktickou správnost kódu a vypíše na standardní
výstup XML reprezentaci programu dle specifikace v sekci3.1.

**Tento skript bude pracovat s těmito parametry:**

- --help viz společný parametr všech skriptů v sekci2.2.

**Chybové návratové kódy specifické pro analyzátor:**

- 21 - chybná nebo chybějící hlavička ve zdrojovém kódu zapsaném v IPPcode22;
- 22 - neznámý nebo chybný operační kód ve zdrojovém kódu zapsaném v IPPcode22;
- 23 - jiná lexikální nebo syntaktická chyba zdrojového kódu zapsaného v IPPcode22.

(^7) Upozornění: Na serveru _Merlin_ je třeba dodržet testování příkazemphp8.1, protože pouhýmphpse spouští verze,
která má omezen přístup k souborovému systému!
(^8) Upozornění: Na serveru _Merlin_ je třeba dodržet testování příkazempython3.8, protože pouhýmpythonse spouští
stará nekompatibilní verze! Python 3.x není zpětně kompatibilní s verzí 2.x!
(^9) Relativní cesta nebude obsahovat zástupný symbol~(vlnka).


## 3.1 Popis výstupního XML formátu

Za povinnou XML hlavičkou^10 následuje kořenový elementprogram(s povinným textovým atribu-
temlanguages hodnotou IPPcode22), který obsahuje pro instrukce elementyinstruction. Každý
elementinstructionobsahuje povinný atributorders pořadím instrukce. Při generování elementů
je pořadí číslováno od 1 v souvislé posloupnosti. Dále element obsahuje povinný atributopcode
(hodnota operačního kódu je ve výstupním XML vždy velkými písmeny) a elementy pro odpoví-
dající počet operandů/argumentů:arg1pro případný první argument instrukce,arg2pro případný
druhý argument aarg3pro případný třetí argument instrukce. Element pro argument má povinný
atributtypes možnými hodnotamiint,bool,string,nil,label,type,varpodle toho, zda se
jedná o literál, návěští, typ nebo proměnnou, a obsahuje textový element.
Tento textový element potom nese buď hodnotu literálu (již bez určení typu a bez znaku @),
jméno návěští, typ, nebo identifikátor proměnné (včetně určení rámce a @). U proměnných vypisujte
označení rámce vždy velkými písmeny, jak by mělo být již na vstupu. Velikosti písmen samotného
jména proměnné ponechejte beze změny. Formát celých čísel je dekadický, oktalový nebo hexade-
cimální dle zvyklostí PHP (viz funkceintval), nicméně na výstup tato čísla vypisujte přesně ve
formátu, v jakém byla načtena ze zdrojového kódu (např. zůstanou kladná znaménka čísel nebo po-
čáteční přebytečné nuly). U literálů typustringpři zápisu do XML nepřevádějte původní escape
sekvence, ale pouze pro problematické znaky v XML (např.<,>, &) využijte odpovídající XML
entity (např.&lt;,&gt;,&amp;). Podobně převádějte problematické znaky vyskytující se v identifi-
kátorech proměnných. Literály typuboolvždy zapisujte malými písmeny jakofalsenebotrue.

**Doporučení:** Všimněte si, že analýza IPPcode22 je tzv. kontextově závislá (viz přednášky), kdy
například můžete mít klíčové slovo použito jako návěští a z kontextu je třeba rozpoznat, zda jde o
návěští, nebo ne. Při tvorbě analyzátoru doporučujeme kombinovat konečně-stavové řízení a regulární
výrazy a pro generování výstupního XML využít vhodnou knihovnu.
Výstupní XML bude porovnáváno s referenčními výsledky pomocí nástroje A7Soft JExamXML^11 ,
viz [ 2 ]. Pozor, v PHP příkazreturnneslouží pro návrat chybového kódu, použijte funkciexit. Pro
výpis varování na standardní chybový výstup uveďte na začátku skriptu příkaz

```
ini_set('display_errors', 'stderr');
```
## 3.2 Bonusová rozšíření

**STATP** Sbírání statistik zpracovaného zdrojového kódu v IPPcode22. Skript bude podporovat pa-
rametr--stats= _file_ pro zadání souboru _file_ , kam se budou vypisovat statistiky v parametrech
umístěných za tímto--stats. Statistiky se do souboru vypisují po řádcích dle pořadí v pa-
rametrech s možností jejich opakování; na každý řádek nevypisujte nic kromě požadovaného
číselného výstupu a odřádkování; případně existující soubor je přepsán. Pro sběr skupin sta-
tistik do různých souborů se použije další výskyt parametru--statss jiným jménem souboru
a následovaný dalšími parametry nové skupiny statistik. Parametr--locvypíše do statistik
počet řádků s instrukcemi (nepočítají se prázdné řádky ani řádky obsahující pouze komen-
tář ani úvodní řádek). Parametr--commentsvypíše do statistik počet řádků, na kterých se
vyskytoval komentář. Parametr--labelsvypíše do statistik počet definovaných návěští (tj.
unikátních možných cílů skoku). Parametr--jumpsvypíše do statistik počet všech instrukcí
návratů z volání a instrukcí pro _skoky_ (souhrnně podmíněné/nepodmíněné skoky a volání),

(^10) Tradiční XML hlavička včetně verze a kódování je<?xml version="1.0" encoding="UTF-8"?>
(^11) Nastavení A7Soft JExamXML pro porovnávání XML (souboroptions) je v _Souborech_ k projektu v IS FIT.


```
--fwjumpspočet dopředných skoků,--backjumpszpětných skoků a--badjumpspočet skoků
na neexistující návěští. Je-li uveden pouze parametr--statsbez upřesnění statistik k výpisu,
bude výstupem prázdný soubor. Chybí-li při zadání parametrů statistik--loc,--commentsa
dalších před nimi parametr--stats, jedná se o chybu 10. Pokus o zápis více skupin statistik
do stejného souboru během jednoho spuštění skriptu vede na chybu 12. [1,5 b]
```
**NVP** Při návrhu a implementaci skriptuparse.phpa pomocných skriptů bude aplikováno objektově
orientované programování a využit alespoň jeden vhodný standardní návrhový vzor (viz [ 5 ]),
což bude řádné zdokumentováno (proč, kde, jak, popis omezení). [1 b]

# 4 Interpret XML reprezentace kódu (interpret.py)

Skript (interpret.py v jazycePython 3.8) načte XML reprezentaci programu a tento program
s využitím vstupu dle parametrů příkazové řádky interpretuje a generuje výstup. Vstupní XML
reprezentace je definována trochu volněji než XML generované skriptemparse.php, ale lze předpo-
kládat, že vstupní XML reprezentace již nebude obsahovat chyby, jež měl ze zadání za úkol detekovat
parse.php. Interpret navíc oproti sekci3.1podporuje existenci volitelných dokumentačních texto-
vých atributůnameadescriptionv kořenovém elementuprograma různě naformátované značky
(např. volitelné využití zkráceného zápisu značek, pokud neobsahuje značka podelement). Sémantika
jednotlivých instrukcí IPPcode22 je popsána v sekci 6. Interpretace instrukcí probíhá dle atributu
ordervzestupně (nicméně, sekvence nemusí být souvislá/seřazená na rozdíl od sekce3.1).

**Tento skript bude pracovat s těmito parametry:**

- --help viz společný parametr všech skriptů v sekci2.2;
- --source= _file_ vstupní soubor s XML reprezentací zdrojového kódu dle definice ze sekce3.1;
- --input= _file_ soubor se vstupy^12 pro samotnou interpretaci zadaného zdrojového kódu.

Alespoň jeden z parametrů (--sourcenebo --input) musí být vždy zadán. Pokud jeden z nich
chybí, jsou chybějící data načítána ze standardního vstupu.

**Chybové návratové kódy specifické pro interpret:**

- 31 - chybný XML formát ve vstupním souboru (soubor není tzv. dobře formátovaný, angl.
    _well-formed_ , viz [ 1 ]);
- 32 - neočekávaná struktura XML (např. element pro argument mimo element pro instrukci,
    instrukce s duplicitním pořadím nebo záporným pořadím);

Chybové návratové kódy interpretu v případě chyby během interpretace jsou uvedeny v popisu jazyka
IPPcode22 (viz sekce6.1).

**Doporučení:** Doporučujeme použít knihovnu pro načítání XML. V případě nekompletní imple-
mentace se zaměřte na funkčnost globálního rámce, práce s proměnnými typuint, instrukceWRITE
a instrukce pro řízení toku programu.

(^12) Vstup/vstupní soubor může být prázdný; např. neinterpretuje-li se žádná instrukce READ.


## 4.1 Bonusová rozšíření

**FLOAT** Podpora typufloatv IPPcode22 (dekadický i **hexadecimální zápis** v analyzátoru i v in-
terpretu včetně načítání ze standardního vstupu; např.float@0x1.2000000000000p+0repre-
zentuje 1,125; viz funkcefloat.fromhex()afloat.hex()v Python 3). Podporujte instrukce
pro práci s tímto typem: INT2FLOAT, FLOAT2INT, aritmetické instrukce (včetně DIV), atd.
(viz [ 3 ]). Podpora vparse.phpje možná, ale nebude testována. [1 b]

**STACK** Podpora zásobníkových variant instrukcí (přípona S; viz [ 3 ]): CLEARS, ADDS/SUB-
S/MULS/IDIVS, LTS/GTS/EQS, ANDS/ORS/NOTS, INT2CHARS/STRI2INTS a JUMPI-
FEQS/JUMPIFNEQS. Zásobníkové verze instrukcí z datového zásobníku vybírají operandy se
vstupními hodnotami dle popisu tříadresné instrukce od konce (tj. typicky nejprve⟨ _symb_ 2 ⟩a
poté⟨ _symb_ 1 ⟩). Podpora vparse.phpje možná, ale nebude testována. [1 b]

**STATI** Sbírání statistik interpretace kódu. Skript bude podporovat parametr--stats= _file_ pro za-
dání souboru _file_ , kam se agregované statistiky budou vypisovat (po řádcích dle pořadí v dalších
(případně opakovaných) parametrech; na každý řádek nevypisujte nic kromě požadovaného
číselného výstupu a odřádkování). Podpora parametru--instspro výpis počtu tzv. vykona-
ných instrukcí (tj. kromě ladicích instrukcí a speciální instrukceLABEL) během interpretace do
statistik. Parametr--hotvypíše do statistik hodnotu atributuorderu vykonané instrukce,
která byla provedena nejvícekrát a má hodnotu atributuordernejmenší. Podpora parametru
--varspro výpis maximálního počtu inicializovaných proměnných přítomných v jeden oka-
mžik ve všech platných rámcích během interpretace zadaného programu do statistik. Chybí-li
při zadání--insts,--hotči--varsparametr--stats, jedná se o chybu 10. [1 b]

**NVI** Při návrhu a implementaci skriptuinterpret.pya pomocných skriptů bude aplikováno ob-
jektově orientované programování a využit alespoň jeden vhodný standardní návrhový vzor
(viz [ 5 ]), což bude řádné zdokumentováno (proč, kde, jak, popis omezení). [1 b]

# 5 Testovací rámec (test.php)

Skript (test.php v jazycePHP 8.1) bude sloužit pro automatické testování (postupné) aplikace
parse.phpainterpret.py^13. Skript projde zadaný adresář s testy a využije je pro automatické
otestování správné funkčnosti jednoho či obou předchozích skriptů včetně vygenerování přehledného
souhrnu v HTML 5 na standardní výstup. Pro hodnocenítest.phpbudou dodány referenční im-
plementaceparse.phpiinterpret.py. Testovací skript nemusí uparse.phpani uinterpret.py
testovat jejich dodatečnou funkčnost aktivovanou parametry příkazové řádky (s výjimkou potřeby
parametru--sourcea/nebo--inputuinterpret.py).

**Tento skript bude pracovat s těmito parametry:**

- --help viz společný parametr všech skriptů v sekci2.2;
- --directory= _path_ testy bude hledat v zadaném adresáři (chybí-li tento parametr, skript
    prochází aktuální adresář);
- --recursive testy bude hledat nejen v zadaném adresáři, ale i rekurzivně ve všech jeho
    podadresářích;

(^13) Za tímto účelem lze vytvářet dočasné soubory, které však nesmí přepsat žádný jiný existující soubor a potom musí
být uklizeny, není-li aktivován přepínač--noclean.


- --parse-script= _file_ soubor se skriptem v PHP 8.1 pro analýzu zdrojového kódu v IPP-
    code22 (chybí-li tento parametr, implicitní hodnotou jeparse.phpuložený v aktuálním adre-
    sáři);
- --int-script= _file_ soubor se skriptem v Python 3.8 pro interpret XML reprezentace kódu
    v IPPcode22 (chybí-li tento parametr, implicitní hodnotou jeinterpret.pyuložený v aktuál-
    ním adresáři);
- --parse-only bude testován pouze skript pro analýzu zdrojového kódu v IPPcode22 (tento
    parametr se nesmí kombinovat s parametry--int-onlya--int-script), výstup s referenčním
    výstupem (soubor s příponouout) porovnávejte nástrojem A7Soft JExamXML (viz [ 2 ]);
- --int-only bude testován pouze skript pro interpret XML reprezentace kódu v IPP-
    code22 (tento parametr se nesmí kombinovat s parametry--parse-only, --parse-script
    a--jexampath). Vstupní program reprezentován pomocí XML bude v souboru s příponou
    src.
- --jexampath= _path_ cesta k adresáři obsahující souborjexamxml.jars JAR balíčkem s ná-
    strojem A7Soft JExamXML a soubor s konfigurací jménemoptions. Je-li parametr vynechán,
    uvažuje se implicitní umístění/pub/courses/ipp/jexamxml/ na serveru _Merlin_ , kde bude
    test.phphodnocen. Koncové lomítko v _path_ je případně nutno doplnit.
- --noclean během činnostitest.phpnebudou mazány pomocné soubory s mezivýsledky, tj.
    skript ponechá soubory, které vznikají při práci testovaných skriptů (např. soubor s výsledným
    XML po spuštěníparse.phpatd.).

**Chybový návratový kód specifický pro testovací rámec:**

- 41 - zadaný adresář ( _path_ v parametru--directory) nebo zadaný soubor ( _file_ v parametrech
    --parse-script,--int-scripta--jexampath) neexistuje či není přístupný.

Každý test je tvořen až 4 soubory stejného jména s příponamisrc,in,outarc(ve stejném
adresáři). Hlavní soubor testu s příponousrcobsahuje zdrojový kód v jazyce IPPcode22 (příp. jeho
XML reprezentaci). Soubory s příponamiin,outarcobsahují vstup a očekávaný/referenční výstup
a očekávaný první chybový návratový kód analýzy resp. interpretace nebo bezchybový návratový kód

0. Pokud soubor s příponouinnebooutchybí, automaticky se fyzicky dogeneruje prázdný soubor.
V případě chybějícího souboru s příponourcse vygeneruje soubor obsahující návratovou hodnotu 0.
Při nenulovém návratovém kódu je provedení testu považováno za úspěšné, pokud je získán správný
návratový kód v odpovídajícím skriptu^14 (výstupy se neporovnávají). V případě nulového kódu je
třeba provést i porovnání výstupu skriptu s referenčním výstupem. Při parametru--parse-only
jsou výstupy porovnávány pomocí nástroje A7Soft JExamXML^15 , jinak je použit unixový nástroj
diff.
    Testy budou umístěny v adresáři včetně případných podadresářů pro lepší kategorizaci testů.
Adresářová struktura může být libovolná (včetně zanoření). Není třeba uvažovat symbolické odkazy
a skryté adresáře.

(^14) Při postupné aplikaci obou filtrů závisí na návratové hodnotěparse.php, zda se následně spustí iinterpret.py.
(^15) Použití nástroje A7Soft JExamXML je popsáno na Wiki předmětu v článkuProjectNotes.


**Požadavky na výstupní HTML verze 5:** Přehledová stránka o úspěšnosti/neúspěšnosti jed-
notlivých testů a celých adresářů bude prohlédnuta ručně opravujícím, takže bude hodnocena její
přehlednost a intuitivnost. Mělo by být na první pohled zřejmé, které testy uspěly a které nikoli (a
kolik), a zda případně uspěly všechny testy (případně i po jednotlivých adresářích), takže je třeba
zahrnout do výpisu všechny prováděné testy, ideálně v každém adresáři řazené abecedně. Výsledná
stránka nesmí načítat externí zdroje^16 a musí být možné ji zobrazit v běžném prohlížeči.

## 5.1 Bonusová rozšíření

**FILES** Podporujte parametr--testlist= _file_ sloužící pro explicitní zadání seznamu adresářů (za-
daných relativními či absolutními cestami) a případně i souborů s testy (zadává se soubor s pří-
ponou.src) formou externího souboru _file_ místo načtení testů z aktuálního adresáře (nelze
kombinovat s parametrem--directory). Dále podporujte parametr--match= _regexp_ pro vý-
běr testů, jejichž jméno bez přípony (ne cesta) odpovídá zadanému regulárnímu výrazu _regexp_
dle PCRE syntaxe (tj. včetně oddělovačů; syntaktická chyba výrazu vede na chybu 11). [1 b]

# 6 Popis jazyka IPPcode

Nestrukturovaný imperativní jazyk IPPcode22 vznikl úpravou jazyka IFJcode21 (jazyk pro mezikód
překladače jazyka IFJ21, viz [ 3 ]), který zahrnuje instrukce tříadresné (typicky se třemi argumenty)
a případně zásobníkové (typicky méně parametrů a pracující s hodnotami na datovém zásobníku).
Každá instrukce se skládá z operačního kódu (klíčové slovo s názvem instrukce), u kterého nezáleží
na velikosti písmen (tj. case insensitive). Zbytek instrukcí tvoří operandy, u kterých na velikosti
písmen záleží (tzv. case sensitive). Operandy oddělujeme libovolným nenulovým počtem mezer či
tabulátorů. Také před operačním kódem a za posledním operandem se může vyskytnout libovolný
počet mezer či tabulátorů. Odřádkování slouží pro oddělení jednotlivých instrukcí, takže na každém
řádku je maximálně jedna instrukce a není povoleno jednu instrukci zapisovat na více řádků. Každý
operand je tvořen proměnnou, konstantou, typem nebo návěštím. V IPPcode22 jsou podporovány
jednořádkové komentáře začínající mřížkou (#). Vyjma prázdných^17 či zakomentovaných řádků obsa-
huje kód v jazyce IPPcode22 na začátku místo instrukce jen identifikátor jazyka (tečka následovaná
jménem jazyka, kdy nezáleží na velikosti písmen):

```
.IPPcode
```
## 6.1 Návratové hodnoty interpretu

Proběhne-li interpretace bez chyb, vrací se návratová hodnota 0 (nula). Chybovým případům odpo-
vídají následující návratové hodnoty:

- 52 - chyba při sémantických kontrolách vstupního kódu v IPPcode22 (např. použití nedefino-
    vaného návěští, redefinice proměnné);
- 53 - běhová chyba interpretace – špatné typy operandů;
- 54 - běhová chyba interpretace – přístup k neexistující proměnné (rámec existuje);
- 55 - běhová chyba interpretace – rámec neexistuje (např. čtení z prázdného zásobníku rámců);

(^16) Přímo do generované HTML stránky je možné vložit vlastní JavaScript nebo CSS kód.
(^17) Řádek obsahující pouze bílé znaky je považován za prázdný.


- 56 - běhová chyba interpretace – chybějící hodnota (v proměnné, na datovém zásobníku nebo
    v zásobníku volání);
- 57 - běhová chyba interpretace – špatná hodnota operandu (např. dělení nulou, špatná návra-
    tová hodnota instrukce EXIT);
- 58 - běhová chyba interpretace – chybná práce s řetězcem.

## 6.2 Paměťový model

Hodnoty během interpretace nejčastěji ukládáme do pojmenovaných proměnných, které jsou sdružo-
vány do tzv. rámců, což jsou v podstatě slovníky proměnných s jejich hodnotami. IPPcode22 nabízí
tři druhy rámců:

- globální, značímeGF(Global Frame), který je na začátku interpretace automaticky inicializován
    jako prázdný; slouží pro ukládání globálních proměnných;
- lokální, značímeLF(Local Frame), který je na začátku nedefinován a odkazuje na vrcholový/ak-
    tuální rámec na zásobníku rámců; slouží pro ukládání lokálních proměnných funkcí (zásobník
    rámců lze s výhodou využít při zanořeném či rekurzivním volání funkcí);
- dočasný, značímeTF(Temporary Frame), který slouží pro chystání nového nebo úklid starého
    rámce (např. při volání nebo dokončování funkce), jenž může být přesunut na zásobník rámců
    a stát se aktuálním lokálním rámcem. Na začátku interpretace je dočasný rámec nedefinovaný.

K překrytým (dříve vloženým) lokálním rámcům v zásobníku rámců nelze přistoupit dříve, než
vyjmeme později přidané rámce.
Další možností pro ukládání nepojmenovaných hodnot je datový zásobník využívaný zásobníko-
vými instrukcemi.

## 6.3 Datové typy

IPPcode22 pracuje s typy operandů dynamicky, takže je typ proměnné (resp. paměťového místa) dán
obsaženou hodnotou. Není-li řečeno jinak, jsou implicitní konverze zakázány. Interpret podporuje
speciální hodnotu/typ nil a tři základní datové typy (int, bool a string), jejichž rozsahy i přesnosti
jsou kompatibilní s jazykem Python 3.
Zápis každé konstanty v IPPcode22 se skládá ze dvou částí oddělených zavináčem (znak@; bez
bílých znaků), označení typu konstanty (int, bool, string, nil) a samotné konstanty (číslo, literál, nil).
Např.bool@true,nil@nilneboint@-5.
Typ int reprezentuje celé číslo (přetečení/podtečení neřešte). Typ bool reprezentuje pravdivostní
hodnotu (falsenebotrue). Literál pro typ string je v případě konstanty zapsán jako sekvence
tisknutelných znaků v kódování UTF-8 (vyjma bílých znaků, mřížky (#) a zpětného lomítka (\))
a escape sekvencí, takže není ohraničen uvozovkami. Escape sekvence, která je nezbytná pro znaky
s dekadickým kódem 000–032, 035 a 092, je tvaru\xyz, kdexyzje dekadické číslo v rozmezí 000–
složené právě ze tří číslic^18 ; např. konstanta

```
string@řetězec\ 032 s\ 032 lomítkem\ 032 \ 092 \ 032 a\ 010 novým\ 035 řádkem
```
(^18) Zápis znaků s kódem Unicode větším jak 126 pomocí těchto escape sekvencí nebudeme testovat.


reprezentuje řetězec

```
řetězec s lomítkem \ a
novým#řádkem
```
Pokus o práci s neexistující proměnnou (čtení nebo zápis) vede na chybu 54. Pokus o čtení hodnoty
neinicializované proměnné vede na chybu 56. Pokus o interpretaci instrukce s operandy nevhodných
typů dle popisu dané instrukce vede na chybu 53.

## 6.4 Instrukční sada

U popisu instrukcí sázíme operační kód tučně a operandy zapisujeme pomocí neterminálních symbolů
(případně číslovaných) v úhlových závorkách. Neterminál⟨ _var_ ⟩značí proměnnou,⟨ _symb_ ⟩konstantu
nebo proměnnou,⟨ _label_ ⟩značí návěští. Identifikátor proměnné se skládá ze dvou částí oddělených
zavináčem (znak@; bez bílých znaků), označení rámceLF,TFneboGFa samotného jména proměnné
(sekvence libovolných alfanumerických a speciálních znaků bez bílých znaků začínající písmenem nebo
speciálním znakem, kde speciální znaky jsou:_,-,$,&,%,*,!,?). Např.GF@_xznačí proměnnou_x
uloženou v globálním rámci.
Na zápis návěští se vztahují stejná pravidla jako na jméno proměnné (tj. část identifikátoru za
zavináčem).

```
Příklad jednoduchého programu v IPPcode22:
```
.IPPcode
**DEFVAR** GF@counter
**MOVE** GF@counter string@ #Inicializace proměnné na prázdný řetězec
#Jednoduchá iterace, dokud nebude splněna zadaná podmínka
**LABEL** while
**JUMPIFEQ** end GF@counter string@aaa
**WRITE** string@Proměnná\032GF@counter\032obsahuje\
**WRITE** GF@counter
**WRITE** string@\
**CONCAT** GF@counter GF@counter string@a
**JUMP** while
**LABEL** end

Instrukční sada nabízí instrukce pro práci s proměnnými v rámcích, různé skoky, operace s da-
tovým zásobníkem, aritmetické, logické a relační operace, dále také konverzní, vstupně/výstupní a
ladicí instrukce.

**6.4.1 Práce s rámci, volání funkcí**

```
MOVE ⟨ var ⟩ ⟨ symb ⟩ Přiřazení hodnoty do proměnné
Zkopíruje hodnotu⟨ symb ⟩do⟨ var ⟩. Např.MOVE LF@par GF@varprovede zkopírování hodnoty
proměnnévarv globálním rámci do proměnnéparv lokálním rámci.
CREATEFRAME Vytvoř nový dočasný rámec
Vytvoří nový dočasný rámec a zahodí případný obsah původního dočasného rámce.
PUSHFRAME Přesun dočasného rámce na zásobník rámců
Přesuň TF na zásobník rámců. Rámec bude k dispozici přes LF a překryje původní rámce
na zásobníku rámců.TF bude po provedení instrukce nedefinován a je třeba jej před dalším
použitím vytvořit pomocí CREATEFRAME. Pokus o přístup k nedefinovanému rámci vede na
chybu 55.
```

```
POPFRAME Přesun aktuálního rámce do dočasného
Přesuň vrcholový rámecLFze zásobníku rámců doTF. Pokud žádný rámec vLFnení k dispozici,
dojde k chybě 55.
DEFVAR ⟨ var ⟩ Definuj novou proměnnou v rámci
Definuje proměnnou v určeném rámci dle⟨ var ⟩. Tato proměnná je zatím neinicializovaná a bez
určení typu, který bude určen až přiřazením nějaké hodnoty. Opakovaná definice proměnné již
existující v daném rámci vede na chybu 52.
CALL ⟨ label ⟩ Skok na návěští s podporou návratu
Uloží inkrementovanou aktuální pozici z interního čítače instrukcí do zásobníku volání a provede
skok na zadané návěští (případnou přípravu rámce musí zajistit jiné instrukce).
RETURN Návrat na pozici uloženou instrukcí CALL
Vyjme pozici ze zásobníku volání a skočí na tuto pozici nastavením interního čítače instrukcí
(úklid lokálních rámců musí zajistit jiné instrukce). Provedení instrukce při prázdném zásobníku
volání vede na chybu 56.
```
**6.4.2 Práce s datovým zásobníkem**

Operační kód zásobníkových instrukcí je zakončen písmenem
”
S“. Zásobníkové instrukce případně
načítají chybějící operandy z datového zásobníku a výslednou hodnotu operace případně ukládají
zpět na datový zásobník.

```
PUSHS ⟨ symb ⟩ Vlož hodnotu na vrchol datového zásobníku
Uloží hodnotu⟨ symb ⟩na datový zásobník.
POPS ⟨ var ⟩ Vyjmi hodnotu z vrcholu datového zásobníku
Není-li zásobník prázdný, vyjme z něj hodnotu a uloží ji do proměnné⟨ var ⟩, jinak dojde k chybě
56.
```
**6.4.3 Aritmetické, relační, booleovské a konverzní instrukce**

V této sekci jsou popsány tříadresné instrukce pro klasické operace pro výpočet výrazu. Přetečení
nebo podtečení číselného výsledku neřešte.

```
ADD ⟨ var ⟩ ⟨ symb 1 ⟩ ⟨ symb 2 ⟩ Součet dvou číselných hodnot
Sečte⟨ symb 1 ⟩a⟨ symb 2 ⟩(musí být typu int) a výslednou hodnotu téhož typu uloží do proměnné
⟨ var ⟩.
SUB ⟨ var ⟩ ⟨ symb 1 ⟩ ⟨ symb 2 ⟩ Odečítání dvou číselných hodnot
Odečte⟨ symb 2 ⟩od ⟨ symb 1 ⟩(musí být typu int) a výslednou hodnotu téhož typu uloží do
proměnné⟨ var ⟩.
MUL ⟨ var ⟩ ⟨ symb 1 ⟩ ⟨ symb 2 ⟩ Násobení dvou číselných hodnot
Vynásobí⟨ symb 1 ⟩ a⟨ symb 2 ⟩(musí být typu int) a výslednou hodnotu téhož typu uloží do
proměnné⟨ var ⟩.
IDIV ⟨ var ⟩ ⟨ symb 1 ⟩ ⟨ symb 2 ⟩ Dělení dvou celočíselných hodnot
Celočíselně podělí celočíselnou hodnotu ze⟨ symb 1 ⟩druhou celočíselnou hodnotou ze⟨ symb 2 ⟩
(musí být oba typu int) a výsledek typu int přiřadí do proměnné⟨ var ⟩. Dělení nulou způsobí
chybu 57.
LT/GT/EQ ⟨ var ⟩ ⟨ symb 1 ⟩ ⟨ symb 2 ⟩ Relační operátory menší, větší, rovno
Instrukce vyhodnotí relační operátor mezi⟨ symb 1 ⟩a⟨ symb 2 ⟩(stejného typu; int, bool nebo
string) a do⟨ var ⟩zapíše výsledek typu bool (falsepři neplatnosti nebotruev případě platnosti
odpovídající relace). Řetězce jsou porovnávány lexikograficky afalseje menší nežtrue. Pro
výpočet neostrých nerovností lze použít AND/OR/NOT. S operandem typu nil (další zdrojový
operand je libovolného typu) lze porovnávat pouze instrukcí EQ, jinak chyba 53.
```

```
AND/OR/NOT ⟨ var ⟩ ⟨ symb 1 ⟩ ⟨ symb 2 ⟩ Základní booleovské operátory
Aplikuje konjunkci (logické A)/disjunkci (logické NEBO) na operandy typu bool⟨ symb 1 ⟩a
⟨ symb 2 ⟩nebo negaci na⟨ symb 1 ⟩(NOT má pouze 2 operandy) a výsledek typu bool zapíše do
⟨ var ⟩.
INT2CHAR ⟨ var ⟩ ⟨ symb ⟩ Převod celého čísla na znak
Číselná hodnota⟨ symb ⟩je dle Unicode převedena na znak, který tvoří jednoznakový řetězec
přiřazený do⟨ var ⟩. Není-li⟨ symb ⟩validní ordinální hodnota znaku v Unicode (viz funkcechr
v Python 3), dojde k chybě 58.
STRI2INT ⟨ var ⟩ ⟨ symb 1 ⟩ ⟨ symb 2 ⟩ Ordinální hodnota znaku
Do⟨ var ⟩uloží ordinální hodnotu znaku (dle Unicode) v řetězci ⟨ symb 1 ⟩na pozici⟨ symb 2 ⟩
(indexováno od nuly). Indexace mimo daný řetězec vede na chybu 58. Viz funkceordv Python 3.
```
**6.4.4 Vstupně-výstupní instrukce**

```
READ ⟨ var ⟩ ⟨ type ⟩ Načtení hodnoty ze standardního vstupu
Načte jednu hodnotu dle zadaného typu⟨ type ⟩ ∈ {int, string, bool}a uloží tuto hodnotu do
proměnné⟨ var ⟩. Načtení proveďte vestavěnou funkcí input() (či analogickou) jazyka Python 3,
pak proveďte konverzi na specifikovaný typ⟨ type ⟩. Při převodu vstupu na typ bool nezáleží na
velikosti písmen a řetězec
”
true“se převádí nabool@true, vše ostatní nabool@false. V případě
chybného nebo chybějícího vstupu bude do proměnné⟨ var ⟩uložena hodnotanil@nil.
WRITE ⟨ symb ⟩ Výpis hodnoty na standardní výstup
Vypíše hodnotu⟨ symb ⟩na standardní výstup. Až na typ bool a hodnotunil@nilje formát
výpisu kompatibilní s příkazem print jazyka Python 3 s doplňujícím parametremend=''(za-
mezí dodatečnému odřádkování). Pravdivostní hodnota se vypíše jakotruea nepravda jako
false. Hodnotanil@nilse vypíše jako prázdný řetězec.
```
**6.4.5 Práce s řetězci**

```
CONCAT ⟨ var ⟩ ⟨ symb 1 ⟩ ⟨ symb 2 ⟩ Konkatenace dvou řetězců
Do proměnné⟨ var ⟩uloží řetězec vzniklý konkatenací dvou řetězcových operandů ⟨ symb 1 ⟩a
⟨ symb 2 ⟩(jiné typy nejsou povoleny).
STRLEN ⟨ var ⟩ ⟨ symb ⟩ Zjisti délku řetězce
Zjistí počet znaků (délku) řetězce v⟨ symb ⟩a tato délka je uložena jako celé číslo do⟨ var ⟩.
GETCHAR ⟨ var ⟩ ⟨ symb 1 ⟩ ⟨ symb 2 ⟩ Vrať znak řetězce
Do⟨ var ⟩uloží řetězec z jednoho znaku v řetězci⟨ symb 1 ⟩na pozici⟨ symb 2 ⟩(indexováno celým
číslem od nuly). Indexace mimo daný řetězec vede na chybu 58.
SETCHAR ⟨ var ⟩ ⟨ symb 1 ⟩ ⟨ symb 2 ⟩ Změň znak řetězce
Zmodifikuje znak řetězce uloženého v proměnné⟨ var ⟩na pozici⟨ symb 1 ⟩(indexováno celočíselně
od nuly) na znak v řetězci⟨ symb 2 ⟩(první znak, pokud obsahuje⟨ symb 2 ⟩více znaků). Výsledný
řetězec je opět uložen do⟨ var ⟩. Při indexaci mimo řetězec⟨ var ⟩nebo v případě prázdného
řetězce v⟨ symb 2 ⟩dojde k chybě 58.
```
**6.4.6 Práce s typy**

```
TYPE ⟨ var ⟩ ⟨ symb ⟩ Zjisti typ daného symbolu
Dynamicky zjistí typ symbolu⟨ symb ⟩a do ⟨ var ⟩zapíše řetězec značící tento typ (int, bool,
string nebo nil). Je-li⟨ symb ⟩neinicializovaná proměnná, označí její typ prázdným řetězcem.
```

**6.4.7 Instrukce pro řízení toku programu**

Neterminál⟨ _label_ ⟩označuje návěští, které slouží pro označení pozice v kódu IPPcode22. V případě
skoku na neexistující návěští dojde k chybě 52.

```
LABEL ⟨ label ⟩ Definice návěští
Speciální instrukce označující pomocí návěští⟨ label ⟩důležitou pozici v kódu jako potenciální cíl
libovolné skokové instrukce. Pokus o vytvoření dvou stejně pojmenovaných návěští na různých
místech programu je chybou 52.
JUMP ⟨ label ⟩ Nepodmíněný skok na návěští
Provede nepodmíněný skok na zadané návěští⟨ label ⟩.
JUMPIFEQ ⟨ label ⟩ ⟨ symb 1 ⟩ ⟨ symb 2 ⟩ Podmíněný skok na návěští při rovnosti
Pokud jsou⟨ symb 1 ⟩a⟨ symb 2 ⟩stejného typu nebo je některý operand nil (jinak chyba 53) a
zároveň se jejich hodnoty rovnají, tak provede skok na návěští⟨ label ⟩.
JUMPIFNEQ ⟨ label ⟩ ⟨ symb 1 ⟩ ⟨ symb 2 ⟩ Podmíněný skok na návěští při nerovnosti
Jsou-li ⟨ symb 1 ⟩a ⟨ symb 2 ⟩stejného typu nebo je některý operand nil (jinak chyba 53), tak
v případě různých hodnot provede skok na návěští⟨ label ⟩.
EXIT ⟨ symb ⟩ Ukončení interpretace s návratovým kódem
Ukončí vykonávání programu, případně vypíše statistiky a ukončí interpret s návratovým kódem
⟨ symb ⟩, kde⟨ symb ⟩je celé číslo v intervalu 0 až 49 (včetně). Nevalidní celočíselná hodnota
⟨ symb ⟩vede na chybu 57.
```
**6.4.8 Ladicí instrukce**

Následující ladicí instrukce (DPRINT a BREAK) nesmí ovlivňovat standardní výstup. Jejich sku-
tečnou funkcionalitu nebudeme testovat, ale mohou se v testech objevit.

```
DPRINT ⟨ symb ⟩ Výpis hodnoty nastderr
Předpokládá se, že vypíše zadanou hodnotu⟨ symb ⟩na standardní chybový výstup (stderr).
BREAK Výpis stavu interpretu nastderr
Předpokládá se, že na standardní chybový výstup (stderr) vypíše stav interpretu (např. pozice
v kódu, obsah rámců, počet vykonaných instrukcí) v danou chvíli (tj. během vykonávání této
instrukce).
```
# Reference

```
[1] Extensible Markup Language (XML) 1.0. W3C. World Wide Web Consortium [online]. 5. vydání.
```
26. 11. 2008 [cit. 2020-02-03]. Dostupné z:https://www.w3.org/TR/xml/

```
[2] A7Soft JExamXML is a java based command line XML diff tool for comparing and merging
XML documents. c2018 [cit. 2020-02-03]. Dostupné z:https://www.a7soft.com/jexamxml.html
```
```
[3] Křivka, Z., a kol.: Zadání projektu z předmětu IFJ a IAL. c2021 [cit. 2022-01-24].
Dostupné z:https://www.fit.vutbr.cz/study/courses/IFJ/private/projekt/ifj2021.pdf
```
```
[4] The text/markdown Media Type. Internet Engineering Task Force (IETF). 2016 [cit. 2020-02-
03]. Dostupné z:https://tools.ietf.org/html/rfc
```
```
[5] Gamma, E., a kol.: Design Patterns: Elements of Reusable Object-Oriented Software. Addison-
Wesley, 1994.
```

**Revize zadání:**

```
2022-02-14: Opravy překlepů, pravopisných a stylistických chyb. Odstraněna opomenutá
zmínka voleb--jexamxmla--jexamcfg. Přidán zákaz kombinace--int-onlya--jexampath.
```
