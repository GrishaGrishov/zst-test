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
- Wymaganie potwierdzenia zakończenia, gdy nie wypełnione wszystkie odpowiedzi
- wybór skali ocen i wyświetlenie skali u nauczyciela
- wyświetlenie skali ocen u ucznia
- sortowanie wyników z ignorowaniem wielkości znaków (lub zamiana znaków np. u ucznia)
- zmniejszenie ilości informacji w wierszach tabelki (tylko na stronie, w csv tak jak było)
	id, temat, autor nad tabelką
- zwijanie informacji o klasach

