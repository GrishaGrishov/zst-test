# zst-test
Aplikacja do testów wiedzy

W apache httpd.conf dopisać:

Alias /zst-test "C:/Users/xxxx/Documents/myapp/zst-test"

<Directory "C:/Users/xxxx/Documents/myapp/zst-test">
    AllowOverride All
    Require all granted
</Directory>