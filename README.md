# zst-test
Aplikacja do testów wiedzy

W apache httpd.conf dopisać:

Alias /zst-test "C:/Users/xxxx/Documents/myapp/zst-test"

<Directory "C:/Users/xxxx/Documents/myapp/zst-test">
    AllowOverride All
    Require all granted
</Directory>

== TODO ==

- odpowiedzi wielokrotne (checkboxy zamiast radiobuttonów)
- wymaganie potwierdzenia zakończenia, gdy nie wypełnione wszystkie odpowiedzi
- wyświetlenie skali ocen u ucznia
- wyświetlanie wyników (odpowiedzi) u ucznia po zakończeniu testu u wszystkich
- zmniejszenie ilości informacji w wierszach tabelki (tylko na stronie, w csv tak jak było)
	id, temat, autor nad tabelką
- zwijanie informacji o klasach
- podział wyników na klasy, tematy
- sortowanie także wg daty/czasu
- zapisywanie wyników przez ajax (żeby nie stracić strony z odpowiedziami)

== DONE ==
- sortowanie wyników z ignorowaniem wielkości znaków (lub zamiana znaków np. u ucznia)
- wybór skali ocen i wyświetlenie skali u nauczyciela

