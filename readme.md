```
Implementační dokumentace k 1. úloze do IPP 2021/2022
Jméno a příjmení: Jakub Kuzník
Login: xkuzni04
```

# Zamyšlení se nad problémem
Skript parser.php je implementován procedurálně bez objektů. Základní řídicí jednotkou celého programu je funkce ```parse()```, která volá ostatní funkce. Pro ukládání statistik z rozšíření STATP jsou deklarovány globální proměnné, které udržují stav statistik. Toto řešení se mi po nabytí zkušenosti s oop nezdá jako ideální a kdybych problém řešil znovu, tak statistiky a funkce nad statistikami zabalím do třídy, což by zvýšilo přehlednost, a navíc by nebyla potřeba globálních proměnných. 

## Načítání parametrů programu 
Pro načítání parametrů nejsou použité žádné knihovny, načtení parametrů a kontrola jejich správnosti se provede čistě ve funkci ```parse_args()```

## Zpracování vstupu 
Vstup se zpracovává ve funkci ```parse()``` vždy po jednom řádku, a to tak, že pomocí php funkcí ```preg_split()``` a ```trim()``` uložím každý jeden token jako jednu položku pole. Tokeny jsou odděleny whitespacy, nebo znakem nového řádku. Následně pomocí funkce remove_comm() odstraním veškeré komentáře a mám připravená data pro syntaktickou kontrolu. 

## Syntaktická kontrola 
Po zpracování vstupů se provede syntaktická kontrola, vždy na jeden řádek. Veškeré syntaktické kontroly se provádí uvnitř funkce ```syntax_validation()```, která může volat jiné menší dílčí funkce pro syntaktickou kontrolu. ```syntax_validation()``` je v podstatě jeden velký switch statement, ve kterém jsou jednotlivé příkazy rozděleny do kategorií podle počtu a typu parametrů. Narazíme-li například na příkaz MOVE, tak se zavolají funkce ```expe_size($řadek, 3)```, protože MOVE se skládá ze 3 tokenů, a následně se zavolá ```expe_var($řádek[1])``` a ```expe_sym(řádek[2])```, protože u tohoto příkazu očekáváme proměnnou a potom symbol. ```syntax_validation()``` tedy využívá tyto expect funkce, které provedou veškeré kontroly spárvnosti argumentů.

## Ukládání do xml formátu
Pokud se provede dobře syntaktická kontrola, posledním krokem řídícího cyklu programu je zavolání funkce ```store_to_xml()``` Ta je opět implementovaná jako switch, který podle typu instrukce, nikoli názvu, uloží instrukci.

## Závěr
Za předpokladu, že vstupní program neobsahoval chybu, se výstupní soubor v xml formátu vypíše na standardní výstup. Následně, pokud uživatel použil vhodné parametry, se vypíší i statistiky.  

## Rozšíření STATP
Jak bylo uvedeno výše, pro ukládání statistik se využívají globální proměnné. Statistiky se zaznamenávají i za situace, kdy uživatel nepožaduje výpis statistik. Rozhodl jsem tak, jelikož ukládání statistik je založené na inkrementaci a téměř neovlivňuje rychlost programu. Důležité však je, že výpis a dopočítání komplexnějších věcí, jako jsou různé typy skoků, se provede jenom tehdy, pokud to uživatel chce.

```
Implementační dokumentace k 2. úloze do IPP 2021/2022
Jméno a příjmení: Jakub Kuzník
Login: xkuzni04
```

# test.php

## Zamýšlení se nad problémem
I přesto že to nebylo povinné skript test.php je implementován objektově. K tomu mě vedlo především množství stavů, které může program nabývat. Veškeré stavy, které určují jak se bude program vykonávat jsou uložený v třídě ```Settings```. Pro načtení parametru a jejich transformaci na toto nastavení jsem využil funkci ```parse_args()```. Kromě nastavení má program velice triviální myšlenku třída ```Test_files```, načte veškeré testovací soubory, poté třída ```Test``` tyto testy postupně otestuje a vždy vygeneruje část výsledné html stránky pomocí třídy ```Html_generation```. 

## Načítání parametrů programu 
Pro načítání parametrů nejsou použité žádné knihovny, načtení parametrů a kontrola jejich správnosti se provede čistě ve funkci ```parse_args()```. Ta tyto informace uloží do třídy ```Settings```. Třída ```Settings``` ještě provede kontrolu zadaných parametrů např: ```Settings->file_exist()``` zjistí zda existují zadané skripty a jexaxml.

## Načtení testovacích scénářů
Třída ```Test_files``` uloží názvy a cestu ke všem testovacím souborům do pole a pokud je to nutné, tak dogeneruje potřebné soubory. Třída taky zohledňuje zda-li má název souborů odpovídat nějakému regulárnímu výrazu, či se využívá argument --test-list

## Vyhodnocení testů a generace HTML 
Jádrem programu je třída ```Test```. Ta uchovává informace o tom kolik testů bylo vykonáno a jak úspěšně. Třída ```Test``` má tři hlavní funkce těmi jsou: ```test_both()```, ```int_only_test()``` a ```parse_only_test```. Jedna z těchto funkci se provádí, podle toho jaké chce uživatel testovat skripty. Každý ze skriptů po provedení testů generuje html pomocí ```Html_generation->store_test()```. Po provedení veškerých testů se toto HTML vygeneruje na STDOUT.

## Rozšíření FILES
Jak bylo výše zmíněno je implementováno rozšíření FILES. argument --match=regexp je vyřešen velmi jednoduše. V případě že nebyl zadán žádný regulární výraz, tak se ve třídě ```Settings``` do proměnné regulárního výrazu uloží hodnota, které odpovídají veškeré názvy tedy ```/*/```. Možnost načítání z test listu je zase vyřešená tak, že se prochází jednotlivá zadaná umístění a klade se důraz na to, aby se nějaký test neukládal vícekrát.

# interpret.py

## Zamýšlení se nad problémem
Interpret je implementován objektově, a jeho činnost je velmi jednoduchá. Nejdříve zpracuje argumenty pomocí ```Arg_parse()```. Poté je načten vstupní soubor pomocí třídy ```Files```. Xml, které interpret očekává na vstupu se zpracuje pomocí knihovny ```xml.etree```. Ta načítá instrukci po instrukci a posílá tyto instrukce do třídy ```Interpret```. ```Interpret``` instrukce rovnou vykoná a nad každou instrukci také provede potřebné syntaktické a sémantické kontroly.

## Speciální datové typy
Program pracuje s datovými typy, které jsou reprezentováný třídámi ```Frame```, ```Variable```, ```Instruction```, ```Label``` a ```Args```. Hlavní řízení programu zajišťuje Třída ```Interpret```, ta ukládá data, která slouží jako kontext pro tuto třídu. To proto aby mohla určit sémantickou správnost vstupního programu. ```Interpret``` ukládá GF, TF jako ```Frame``` a LF jako stack těchto Framu. Dále ukládá pole objektů ```Label```, pro uchovávání návěští a stack objektů ```Instruction```. Třída ```Frame``` pak ukládá objekty typu ```Variable``` a třídá ```Instruction``` si uchovává argumenty instrukcí jako ```Args```.

## Vykonání instrukce  
Jak již bylo řečeno o vykonávání instrukce se stará třída ```Interpret```, ta má metodu ```interpret()```, která zpracuje instrukci a zavolá vhodnou funkci začínající znaky ```__ins_```, například metoda ```__ins_move()``` pro instrukci ```MOVE```. v rámci těchto ```__ins``` metod se prvně provedou veškeré kontroly, kdy se v případě chyb volá funkce ```error()```, pokud se nenalezne chyba instrukce se provede na stdout. Metoda ```interpret()``` vrací číslo instrukce, to slouží k tomu že může vrátit číslo jiné instrukce a program bude pokračovat od ní, tedy se provede skok.

## Rozšíření NVI
Interpret je navržen podle jednoduché návrhové metody jménem ```Singleton```. Program má hlavní třídu ```Interpret```, ta má jenom jednu instanci a provádí veškerou interpretaci. K uchovávání dat však využívá různé menší třídy, na které se dá pohlížet jen jako na datové schránky, které s těmito daty umí manipulovat, ale to je vše. Jsou to třídy ```Frame```, ```Variable```, ```Instruction```, ```Label``` a ```Args```. Tyto třídy pro uchovávání dat si lze představit jako chytřejší datové struktury. Za zmínku stojí ještě jedna ```Singelton``` třída ```Files``` opět má jen jednu instanci a provádí veškeré operace nad soubory. U programu byl kladen důraz na OO. paradigmata. s daty se pracuje pomocí metod ```get()``` a ```set()```. veškeré ostatní funkce by měly být privátní, až na některé vyjímky.

