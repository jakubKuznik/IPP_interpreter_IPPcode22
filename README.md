```
Implementační dokumentace k %1%. úloze do IPP 2021/2022
Jméno a příjmení: %Jakub_Kuzník%
Login: %xkuzni04%
```

# Zamyslení se nad problémem
Skript parser.php je implementován funkcionálně bez objektů. Základní řídicí jednotkou celého programu je funkce ```parse()```, která volá ostatní funkce. Pro ukládání statistik z rozšíření STATP jsou deklarováný globální proměnné, které udržují stav statistik, toto řešení se mi po nabytí zkušenosti s oop nezdá jako ideální a kdybych problém řešil znovu, tak statistiky a funkce nad statistikami zabalím do třídy, což by zvýšilo přehlednost a navíc by nebyla potřeba globálních proměnných. 

## Načítání parametrů programu 
Pro načítání parametrů, nejsou použité žádné knihovny, načtení parametrů a kontrola jejich správnosti se provede čistě ve funkci ```parse_args()```

## Zpracování vstupu 
Vstup se zpracovává ve funkci ```parse()``` vždy po jednom řádku a to tak, že pomocí php funkci ```preg_split()``` a ```trim()``` uložím každý jeden token jako jednu položku pole. Tokeny jsou odděleny whitespacy, nebo znakem nového řádku. Následně pomocí funkce remove_comm() odstraním veškeré komentáře a mám připravené data pro syntaktickou kontrolu. 

## Syntaktická kontrola 
Po zpracování vstupů se provede syntaktická kontrola, vždy na jeden řádek. Veškeré syntaktické kontroly se provádí uvnitř funkce ```syntax_validation()```, která může volat jiné menší dílčí funkce pro syntaktickou kontrolu. ```syntax_validation()``` je v podstatě jeden velký Switch statement, ve kterém jsou jednotlivé příkazy rozdělený do kotegorií podle počtu a typu parametrů. Narazíme-li například na příkaz MOVE tak se zavolají funkce ```expe_size($řadek, 3)```, protože MOVE se skládá ze 3 tokenů a následně se zavolá ```expe_var($řádek[1])``` a ```expe_sym(řádek[2])```, protože u tohoto příkazu očekáváme proměnnou a potom symbol. ```syntax_validation()``` tedy využívá tyto expect funkce, které provedou veškeré kontroly spárvnosti argumentů.

## Ukládání do xml formátu
Pokud se provede dobře syntaktická kontrola posledním krokem řídícího cyklu programu je zavolání funkce store_to_xml(); Ta je opět implementovaná jako switch, který podle typu nikoli konkrétní instrukce ví jak má daný příkaz uložit.

## Závěr
Za předpokladu že vstupní program neobsahoval chybu se výstupní soubor v xml formátu vypíše na standardní výstup. Následně pokud uživatel použil vhodné parametry se vypíší i statistiky.  

## Rozšíření STATP
Jak bylo uvedeno výše pro ukládání statistik se využívají globální proměnné. Statistiky se zaznamenávají i za situace, kdy uživatel nepožaduje výpis statistik. Rozhodl jsem tak, jelikož ukládání statistik je založené na inkrementaci a téměř neovlivňuje rychlost programu. Důležité však je že výpis a dopočítání komplexnějších věcí jako jsou různé typy skoků se provede jenom pokud to uživatel chce.

