# School Matcher API

<!-- TOC -->
* [School Matcher API](#school-matcher-api)
  * [Założenia](#założenia)
  * [Uruchomienie projektu](#uruchomienie-projektu)
    * [Wymagania](#wymagania)
    * [Instalacja](#instalacja)
  * [Podjeście do problemu](#podejście-do-problemu)
<!-- TOC -->

## Założenia

Projekt został zrealizowany w **Symfony 8** z podziałem na warstwy zgodnie z 
**DDD + Clean / Hexagonal Architecture** oraz zgodnie z zasadami **SOLID**.

- Logika domenowa jest całkowicie niezależna od frameworka  
  (brak zależności od Symfony w warstwie **Domain**)
- Komunikacja w warstwie **Application** oparta jest o **CQRS**:
    - **Query + QueryHandler** do odczytu danych
    - brak zapisu stanu (**read-only use case**)
- Do obsługi zapytań używany jest **Symfony Messenger** (`query.bus`)
- Dane wejściowe walidowane są przy użyciu **DTO + Symfony Validator**
- Jakość kodu i bezpieczeństwo typów weryfikowane są przy pomocy  
  **PHPStan (level = 6)**
- Testy zostały napisane w **PHPUnit**
- Kod został doprowadzony do stanu **bez warningów PHPStan i PHPUnit**, co było jednym z kluczowych celów jakościowych projektu.
---

## Uruchomienie projektu 

### Wymagania

- **PHP 8.4+**
- **Docker** 

### Instalacja

W konsoli, będąc w katalogu porojektu (np. www-aplications/operon/), wydajemy polecenie: 
```bash
marcin@marcin-HP-Pavilion-Laptop-15-eg3xxx:~/www-aplications/operon$ docker compose up
```
W konsoli, listujemy kontenrery docker'a:
```bash
marcin@marcin-HP-Pavilion-Laptop-15-eg3xxx:~$ docker ps
```
przechodzimy do kontenera o nazwie "service-operon" podając id, załóżmy, że "c64"

```bash
marcin@marcin-HP-Pavilion-Laptop-15-eg3xxx:~$ docker exec -it c64 /bin/bash
```

Instalujemy zależności, podając w kontenerze:
```bash
root@c641a6322046:/var/www/html# composer install
```

Endpoint:
```bash
/api/school/match/?name=mickiewicz

name - wymagany
city - niewymagany (np. Warszawa)
type - niewymagany (np. liceum)
````

Wywołanie endpoint'a w przeglądarce:
```bash
http://127.0.0.1:81/api/school/match?name=mickieiwcz
````

Wywołanie endpoint'a W POSTMAN (tworzymy request GET):
```bash
http://127.0.0.1:81//api/school/match?name=Mickiewicz
```

Wywołanie endpoint'a z poziomu konsoli:
```bash
marcin@marcin-HP-Pavilion-Laptop-15-eg3xxx:~$ curl "http://127.0.0.1:81/api/school/match?name=mickiewicz"
```

Testy PHPUnit:
```bash
/var/www/html# XDEBUG_MODE=coverage php ./vendor/bin/phpunit --coverage-text
```

Statyczna analiza kodu:
```bash
/var/www/html# php -d memory_limit=1G vendor/bin/phpstan analyse -c phpstan.dist.neon
```

PHP CS-FIXER:
```bash
/var/www/html# php ./vendor/bin/php-cs-fixer fix
```

## Podejście do problemu
Algorytm służy do wyszukiwania szkół na podstawie nazwy, miasta i typu szkoły.

### 1. Filtracja po mieście i typie szkoły
- Jeśli podano `$city`, algorytm pomija szkoły, które nie znajdują się w tym mieście.
- Jeśli podano `$type`, algorytm pomija szkoły, które nie pasują do typu.

### 2. Obliczenie najlepszego dopasowania nazwy
- Każda szkoła może mieć wiele alternatywnych nazw (`getAllNames()`).
- Dla każdej nazwy wyliczany jest wynik dopasowania (`score`) względem podanej nazwy.
- Z wszystkich wyników wybierany jest **najwyższy wynik**.

### 3. Próg akceptowalności (70)
- Wynik dopasowania jest opakowany w `MatchScore`.
- Jeśli wynik jest **>= 70**, uznajemy dopasowanie za akceptowalne i dodajemy szkołę do wyników.

### 4. Sortowanie wyników
- Wyniki są sortowane malejąco po `score`, tak aby najlepiej dopasowane szkoły były na początku.

