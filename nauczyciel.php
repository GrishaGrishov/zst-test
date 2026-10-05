<?php

session_start();


// ==================================================
// USTAWIENIA
// ==================================================

$hasloNauczyciela = "Nauczyciel123!";

$plikUstawienia = __DIR__ . "/ustawienia.txt";

$folderPytania = __DIR__ . "/pytania";

$folderWyniki = __DIR__ . "/odpowiedzi";

$folderLogo = __DIR__ . "/logo";

$test_ids = getTestIds($folderPytania);


// ==================================================
// FUNKCJE
// ==================================================


// --------------------------------------------------
// BEZPIECZNA NAZWA
// --------------------------------------------------

function bezpiecznaNazwa($tekst)
{
    $tekst = trim($tekst);

    $tekst = preg_replace(
        '/[^a-zA-Z0-9ąćęłńóśźżĄĆĘŁŃÓŚŹŻ_-]/u',
        "_",
        $tekst
    );

    if ($tekst === "") {
        $tekst = "brak";
    }

    return $tekst;
}


// --------------------------------------------------
// WCZYTANIE USTAWIEŃ
// --------------------------------------------------

function wczytajUstawienia($plik)
{
    $ustawienia = [

        "liczba_pytan" => 10,

        "czas_minuty" => 30,

        "test_id" => "",

        "temat" => "",

        "klasa" => "",

        "autor" => ""
    ];


    if (!file_exists($plik)) {
        return $ustawienia;
    }


    $linie = file(
        $plik,
        FILE_IGNORE_NEW_LINES
    );


    foreach ($linie as $wiersz) {

        $czesci = explode(
            "=",
            $wiersz,
            2
        );


        if (count($czesci) != 2) {
            continue;
        }


        $klucz = trim(
            $czesci[0]
        );


        $wartosc = trim(
            $czesci[1]
        );


        // Usunięcie BOM UTF-8
        $klucz = preg_replace(
            '/^\xEF\xBB\xBF/',
            '',
            $klucz
        );


        if (
            $klucz === "liczba_pytan"
        ) {

            $ustawienia["liczba_pytan"] =
                max(
                    1,
                    intval($wartosc)
                );
        }


        elseif (
            $klucz === "czas_minuty"
        ) {

            $ustawienia["czas_minuty"] =
                max(
                    1,
                    intval($wartosc)
                );
        }


        elseif (
            $klucz === "test_id"
        ) {

            $ustawienia["test_id"] =
                $wartosc;
        }

        elseif (
            $klucz === "temat"
        ) {

            $ustawienia["temat"] =
                $wartosc;
        }


        elseif (
            $klucz === "klasa"
        ) {

            $ustawienia["klasa"] =
                $wartosc;
        }


        elseif (
            $klucz === "autor"
        ) {

            $ustawienia["autor"] =
                $wartosc;
        }
    }


    return $ustawienia;
}


// --------------------------------------------------
// WCZYTANIE WYNIKU
// --------------------------------------------------

function wczytajWynik($plik)
{
    if (!file_exists($plik)) {

        return [

            "dane" => [],

            "tekst" => ""
        ];
    }


    $tekst =
        file_get_contents(
            $plik
        );


    // Usunięcie BOM UTF-8
    $tekst =
        preg_replace(
            '/^\xEF\xBB\xBF/',
            '',
            $tekst
        );


    $dane = [];


    $linie =
        preg_split(
            "/\r\n|\n|\r/",
            $tekst
        );


    foreach ($linie as $wiersz) {

        $czesci =
            explode(
                ":",
                $wiersz,
                2
            );


        if (
            count($czesci) == 2
        ) {

            $klucz =
                trim(
                    $czesci[0]
                );


            $wartosc =
                trim(
                    $czesci[1]
                );


            // Usunięcie BOM
            $klucz =
                preg_replace(
                    '/^\xEF\xBB\xBF/',
                    '',
                    $klucz
                );


            $dane[$klucz] =
                $wartosc;
        }
    }


    return [

        "dane" => $dane,

        "tekst" => $tekst
    ];
}


// --------------------------------------------------
// WYSZUKIWANIE LOGO
// --------------------------------------------------

function znajdzLogo($folder)
{
    if (!is_dir($folder)) {
        return null;
    }


    $pliki =
        scandir(
            $folder
        );


    if ($pliki === false) {
        return null;
    }


    foreach ($pliki as $plik) {

        if (
            $plik === "." ||
            $plik === ".."
        ) {
            continue;
        }


        $sciezka =
            $folder .
            DIRECTORY_SEPARATOR .
            $plik;


        if (
            !is_file($sciezka)
        ) {
            continue;
        }


        $rozszerzenie =
            strtolower(
                pathinfo(
                    $plik,
                    PATHINFO_EXTENSION
                )
            );


        if (
            !in_array(
                $rozszerzenie,
                [
                    "png",
                    "jpg",
                    "jpeg",
                    "gif",
                    "webp"
                ],
                true
            )
        ) {
            continue;
        }


        $nazwa =
            pathinfo(
                $plik,
                PATHINFO_FILENAME
            );


        if (
            strtolower($nazwa)
            ===
            "test_logo"
        ) {

            return $plik;
        }
    }


    return null;
}


function getTestIds($folder) {
        
    $ids = array();
    
    if (!is_dir($folder)) {
        return null;
    }


    $pliki =
        scandir(
            $folder
        );


    if ($pliki === false) {
        return null;
    }


    foreach ($pliki as $plik) {

        if ($plik === "." ||
            $plik === "..") {
            continue;
        }


        $sciezka =
            $folder .
            DIRECTORY_SEPARATOR .
            $plik;


        if (!is_dir($sciezka)) {
            continue;
        }

        $nazwa =
            pathinfo(
                $plik,
                PATHINFO_FILENAME
            );
            
        $readme = $sciezka .
                DIRECTORY_SEPARATOR .
                "readme.txt";
        
        
        $temat = $plik;
        if (is_file($readme)) {
            
            $linie = file(
                $readme,
                FILE_IGNORE_NEW_LINES
            );

            foreach ($linie as $wiersz) {

                $czesci = explode(
                    ":",
                    $wiersz,
                    2
                );

                if (count($czesci) != 2) {
                    continue;
                }


                $klucz = trim(
                    $czesci[0]
                );
                
                
                if ($klucz == "temat") {
                    $temat = trim($czesci[1]);
                }
            }
        }
        $ids[] = array($plik, $temat);

    }


    return $ids;
}

// --------------------------------------------------
// USUNIĘCIE STAREGO LOGO
// --------------------------------------------------

function usunStareLogo($folder)
{
    if (!is_dir($folder)) {
        return;
    }


    $pliki =
        scandir(
            $folder
        );


    if ($pliki === false) {
        return;
    }


    foreach ($pliki as $plik) {

        if (
            $plik === "." ||
            $plik === ".."
        ) {
            continue;
        }


        $sciezka =
            $folder .
            DIRECTORY_SEPARATOR .
            $plik;


        if (
            !is_file($sciezka)
        ) {
            continue;
        }


        $rozszerzenie =
            strtolower(
                pathinfo(
                    $plik,
                    PATHINFO_EXTENSION
                )
            );


        $nazwa =
            pathinfo(
                $plik,
                PATHINFO_FILENAME
            );


        if (
            strtolower($nazwa)
            ===
            "test_logo"
            &&
            in_array(
                $rozszerzenie,
                [
                    "png",
                    "jpg",
                    "jpeg",
                    "gif",
                    "webp"
                ],
                true
            )
        ) {

            unlink(
                $sciezka
            );
        }
    }
}


// ==================================================
// LOGOWANIE
// ==================================================

if (
    isset(
        $_POST["logowanie"]
    )
) {

    if (
        isset(
            $_POST["haslo"]
        ) &&
        $_POST["haslo"]
            ===
        $hasloNauczyciela
    ) {

        $_SESSION["nauczyciel"] =
            true;

    } else {

        $blad =
            "Nieprawidłowe hasło.";
    }
}


// ==================================================
// WYLOGOWANIE
// ==================================================

if (
    isset(
        $_GET["wyloguj"]
    )
) {

    unset(
        $_SESSION["nauczyciel"]
    );


    header(
        "Location: nauczyciel.php"
    );

    exit;
}


// ==================================================
// FORMULARZ LOGOWANIA
// ==================================================

if (
    !isset(
        $_SESSION["nauczyciel"]
    )
):

?>

<!DOCTYPE html>

<html lang="pl">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>

<title>
Panel nauczyciela
</title>

<style>

body {

    font-family:
        Arial,
        sans-serif;

    background:
        #f2f2f2;

    margin:
        0;
}


.panel {

    width:
        400px;

    max-width:
        calc(100% - 40px);

    margin:
        100px auto;

    background:
        white;

    padding:
        30px;

    border-radius:
        10px;

    box-sizing:
        border-box;
}


input {

    width:
        100%;

    padding:
        10px;

    margin-bottom:
        15px;

    box-sizing:
        border-box;
}


button {

    padding:
        12px 20px;

    cursor:
        pointer;
}


.blad {

    color:
        #b00020;
}

</style>
</head>


<body>

<div class="panel">

<h1>
Panel nauczyciela
</h1>


<?php if (isset($blad)): ?>

<p class="blad">

<?= htmlspecialchars(
    $blad,
    ENT_QUOTES,
    "UTF-8"
) ?>

</p>

<?php endif; ?>


<form method="post">

<input
    type="password"
    name="haslo"
    placeholder="Hasło nauczyciela"
    required
>


<button
    type="submit"
    name="logowanie"
>
Zaloguj
</button>

</form>


</div>


</body>

</html>

<?php

exit;

endif;


// ==================================================
// UTWORZENIE KATALOGÓW
// ==================================================

if (!is_dir($folderWyniki)) {

    mkdir(
        $folderWyniki,
        0777,
        true
    );
}


if (!is_dir($folderLogo)) {

    mkdir(
        $folderLogo,
        0777,
        true
    );
}


// ==================================================
// WCZYTANIE USTAWIEŃ
// ==================================================

$ustawienia =
    wczytajUstawienia(
        $plikUstawienia
    );


$liczbaPytan =
    $ustawienia["liczba_pytan"];


$czasMinuty =
    $ustawienia["czas_minuty"];


$test_id =
    $ustawienia["test_id"];

$temat =
    $ustawienia["temat"];


$klasaTestu =
    $ustawienia["klasa"];


$autorTestu =
    $ustawienia["autor"];


// ==================================================
// ZAPIS USTAWIEŃ
// ==================================================

if (
    isset(
        $_POST["zapisz_ustawienia"]
    )
) {

    $liczbaPytan =
        max(
            1,
            intval(
                $_POST["liczba_pytan"]
                ?? 1
            )
        );


    $czasMinuty =
        max(
            1,
            intval(
                $_POST["czas_minuty"]
                ?? 1
            )
        );


    $test_id =
        trim(
            $_POST["test_id"]
            ?? ""
        );

    $temat =
        trim(
            $_POST["temat"]
            ?? ""
        );


    $klasaTestu =
        trim(
            $_POST["klasa"]
            ?? ""
        );


    $autorTestu =
        trim(
            $_POST["autor"]
            ?? ""
        );


    if (
        $temat === ""
    ) {

        $temat =
            "Test";
    }


    // ----------------------------------------------
    // ZAPIS DO USTAWIENIA.TXT
    // ----------------------------------------------

    $tekstUstawien =
        "\xEF\xBB\xBF" .

        "liczba_pytan=" .
        $liczbaPytan .
        PHP_EOL .

        "czas_minuty=" .
        $czasMinuty .
        PHP_EOL .

        "test_id=" .
        $test_id .
        PHP_EOL .

        "temat=" .
        $temat .
        PHP_EOL .

        "klasa=" .
        $klasaTestu .
        PHP_EOL .

        "autor=" .
        $autorTestu .
        PHP_EOL;


    file_put_contents(
        $plikUstawienia,
        $tekstUstawien
    );


    $komunikat =
        "Ustawienia zostały zapisane.";
}


// ==================================================
// WGRYWANIE LOGO
// ==================================================

if (
    isset(
        $_POST["wgraj_logo"]
    )
) {

    if (
        !isset(
            $_FILES["logo"]
        ) ||
        $_FILES["logo"]["error"]
            !==
        UPLOAD_ERR_OK
    ) {

        $bladLogo =
            "Nie wybrano pliku lub wystąpił błąd podczas przesyłania.";

    } else {

        $plik =
            $_FILES["logo"];


        // ------------------------------------------
        // MAKSYMALNY ROZMIAR 2 MB
        // ------------------------------------------

        if (
            $plik["size"]
            >
            2 * 1024 * 1024
        ) {

            $bladLogo =
                "Logo jest za duże. Maksymalny rozmiar to 2 MB.";

        } else {


            // --------------------------------------
            // SPRAWDZENIE CZY TO OBRAZ
            // --------------------------------------

            $informacje =
                getimagesize(
                    $plik["tmp_name"]
                );


            if (
                $informacje === false
            ) {

                $bladLogo =
                    "Wybrany plik nie jest prawidłowym obrazem.";

            } else {


                $dozwoloneTypy = [

                    "image/jpeg" =>
                        "jpg",

                    "image/png" =>
                        "png",

                    "image/gif" =>
                        "gif",

                    "image/webp" =>
                        "webp"

                ];


                $typ =
                    $informacje["mime"];


                if (
                    !isset(
                        $dozwoloneTypy[$typ]
                    )
                ) {

                    $bladLogo =
                        "Dozwolone formaty: JPG, PNG, GIF oraz WEBP.";

                } else {


                    $rozszerzenie =
                        $dozwoloneTypy[$typ];


                    // ------------------------------
                    // USUNIĘCIE POPRZEDNIEGO LOGO
                    // ------------------------------

                    usunStareLogo(
                        $folderLogo
                    );


                    // ------------------------------
                    // NOWA ŚCIEŻKA
                    // ------------------------------

                    $sciezkaLogo =
                        $folderLogo .
                        "/test_logo." .
                        $rozszerzenie;


                    if (
                        move_uploaded_file(
                            $plik["tmp_name"],
                            $sciezkaLogo
                        )
                    ) {

                        $komunikatLogo =
                            "Logo zostało zapisane.";

                    } else {

                        $bladLogo =
                            "Nie udało się zapisać logo.";
                    }
                }
            }
        }
    }
}


// ==================================================
// USUWANIE LOGO
// ==================================================

if (
    isset(
        $_GET["usun_logo"]
    )
) {

    usunStareLogo(
        $folderLogo
    );


    header(
        "Location: nauczyciel.php"
    );

    exit;
}


// ==================================================
// USUWANIE WYNIKU
// ==================================================

if (
    isset($_GET["usun"]) &&
    isset($_GET["klasa"])
) {

    $plik =
        basename(
            $_GET["usun"]
        );


    $klasa =
        bezpiecznaNazwa(
            $_GET["klasa"]
        );


    $sciezka =
        $folderWyniki .
        "/" .
        $klasa .
        "/" .
        $plik;


    if (
        file_exists(
            $sciezka
        )
    ) {

        unlink(
            $sciezka
        );
    }


    header(
        "Location: nauczyciel.php"
    );

    exit;
}


// ==================================================
// EKSPORT CSV
// ==================================================

if (
    isset($_GET["csv"]) &&
    $_GET["csv"] !== ""
) {

    $klasa =
        bezpiecznaNazwa(
            $_GET["csv"]
        );


    $folder =
        $folderWyniki .
        "/" .
        $klasa;


    if (
        !is_dir($folder)
    ) {

        die(
            "Nie znaleziono klasy."
        );
    }


    $nazwaPliku =
        "wyniki_" .
        $klasa .
        "_" .
        date(
            "Y-m-d_H-i-s"
        ) .
        ".csv";


    header(
        "Content-Type: text/csv; charset=UTF-8"
    );


    header(
        'Content-Disposition: attachment; filename="' .
        $nazwaPliku .
        '"'
    );


    // ----------------------------------------------
    // BOM UTF-8 DLA EXCELA
    // ----------------------------------------------

    echo "\xEF\xBB\xBF";


    $plikCSV =
        fopen(
            "php://output",
            "w"
        );


    // ----------------------------------------------
    // NAGŁÓWKI
    // ----------------------------------------------

    fputcsv(
        $plikCSV,
        [
            "Imię",
            "Nazwisko",
            "Stanowisko",
            "Klasa",
            "Identyfikator",
            "Temat",
            "Autor",
            "Data",
            "Punkty",
            "Procent",
            "Ocena"
        ],
        ";"
    );


    $pliki =
        glob(
            $folder .
            "/*.txt"
        );


    foreach (
        $pliki
        as $plik
    ) {

        $wynik =
            wczytajWynik(
                $plik
            );


        $dane =
            $wynik["dane"];


        $imie =
            $dane["Imię"]
            ?? "";


        $nazwisko =
            $dane["Nazwisko"]
            ?? "";

        $stanowisko =
            $dane["Stanowisko"]
            ?? "";

        $klasaWyniku =
            $dane["Klasa"]
            ?? $klasa;


        $testIdWyniku =
            $dane["Identyfikator"]
            ?? "";

        $tematWyniku =
            $dane["Temat"]
            ?? "";

        $autorWyniku =
            $dane["Autor"]
            ?? "";


        $data =
            $dane["Data"]
            ?? "";


        $punkty =
            $dane["Uzyskane punkty"]
            ?? "";


        $procent =
            $dane["Procent"]
            ?? "";


        $ocena =
            $dane["Ocena"]
            ?? "";


        // ------------------------------------------
        // ZABEZPIECZENIE PRZED DATĄ W EXCELU
        // ------------------------------------------

        if (
            $punkty !== ""
        ) {

            $punkty =
                "'" .
                $punkty;
        }


        fputcsv(
            $plikCSV,
            [
                $imie,
                $nazwisko,
                $stanowisko,
                $klasaWyniku,
                $testIdWyniku,
                $tematWyniku,
                $autorWyniku,
                $data,
                $punkty,
                $procent,
                $ocena
            ],
            ";"
        );
    }


    fclose(
        $plikCSV
    );


    exit;
}


// ==================================================
// LOGO - ADRES DLA PRZEGLĄDARKI
// ==================================================

$nazwaLogo =
    znajdzLogo(
        $folderLogo
    );


$logoUrl = null;


if (
    $nazwaLogo !== null
) {

    $logoUrl =
        "logo/" .
        rawurlencode(
            $nazwaLogo
        );
}

?>

<!DOCTYPE html>

<html lang="pl">

<head>

<meta charset="UTF-8">

<meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
>


<title>
Panel nauczyciela
</title>


<style>

body {

    font-family:
        Arial,
        sans-serif;

    background:
        #f2f2f2;

    margin:
        0;

    padding:
        20px;
}


.container {

    max-width:
        1200px;

    margin:
        auto;

    background:
        white;

    padding:
        30px;

    border-radius:
        10px;

    box-sizing:
        border-box;
}


input,
textarea {

    padding:
        10px;

    box-sizing:
        border-box;
}


.input-wide {

    width:
        100%;

    max-width:
        700px;
}


button {

    padding:
        10px 15px;

    cursor:
        pointer;
}


table {

    width:
        100%;

    border-collapse:
        collapse;

    margin-top:
        15px;
}


th,
td {

    border:
        1px solid #ccc;

    padding:
        8px;

    text-align:
        left;
}


th {

    background:
        #eeeeee;
}


.klasa {

    margin-top:
        30px;

    border:
        1px solid #ccc;

    padding:
        20px;

    border-radius:
        8px;

    overflow-x:
        auto;
}


.logo-panel {

    margin:
        20px 0;

    padding:
        20px;

    background:
        #f7f7f7;

    border-radius:
        8px;
}


.logo-panel img {

    max-width:
        300px;

    max-height:
        180px;

    width:
        auto;

    height:
        auto;

    display:
        block;

    margin-bottom:
        15px;

    object-fit:
        contain;
}


.info {

    background:
        #e8f5e9;

    padding:
        12px;

    margin:
        15px 0;

    border-radius:
        6px;
}


.blad {

    background:
        #ffebee;

    color:
        #b00020;

    padding:
        12px;

    margin:
        15px 0;

    border-radius:
        6px;
}


.podglad {

    background:
        #f5f5f5;

    padding:
        20px;

    border:
        1px solid #ccc;

    border-radius:
        8px;

    white-space:
        pre-wrap;

    overflow:
        auto;
}


a {

    text-decoration:
        none;
}


hr {

    margin:
        30px 0;
}

</style>

<script>
    <?php 
    echo "test_ids = {\n";
    foreach ($test_ids as $value) {
        $id = $value[0];
        $temat = $value[1];
        echo "    '$id': '$temat',\n";
    }
    echo "    }\n";
    ?>
    
    function onChangeId() {
        id = document.getElementById("test_id").value
        temat = ""
        if (id != "") {
            temat = test_ids[id]
        }
        document.getElementById("temat").value = temat
    }

</script>

</head>


<body>


<div class="container">


<h1>
Panel nauczyciela
</h1>


<p>

<a
    href="nauczyciel.php?wyloguj=1"
>
Wyloguj
</a>

</p>


<?php if (isset($komunikat)): ?>

<div class="info">

<?= htmlspecialchars(
    $komunikat,
    ENT_QUOTES,
    "UTF-8"
) ?>

</div>

<?php endif; ?>


<?php if (isset($komunikatLogo)): ?>

<div class="info">

<?= htmlspecialchars(
    $komunikatLogo,
    ENT_QUOTES,
    "UTF-8"
) ?>

</div>

<?php endif; ?>


<?php if (isset($bladLogo)): ?>

<div class="blad">

<?= htmlspecialchars(
    $bladLogo,
    ENT_QUOTES,
    "UTF-8"
) ?>

</div>

<?php endif; ?>


<!-- ==================================================
     USTAWIENIA TESTU
     ================================================== -->

<h2>
Ustawienia testu
</h2>


<form method="post">


<p>

<label>
<strong>
Identyfikator testu:
</strong>
</label>

<br>

<select
    name="test_id"
    id="test_id"
    class="input-wide"
    onchange="onChangeId()"
    >
    <option value="">&lt;WYBIERZ TEST&gt;</option>";
    <?php 
    foreach ($test_ids as $value) {
        $id = $value[0];
        $temat = $value[1];
        $sel = "";
        if ($test_id == $id) {
            $sel = " selected=1";
        }
        echo "<option value=$id$sel>$id</option>";
    }
    ?>
</select>
</p>


<p>

<label>
<strong>
Temat testu:
</strong>
</label>

<br>

<input
    type="text"
    name="temat"
    id="temat"
    class="input-wide"
    placeholder="np. Informatyka - sieci komputerowe"
    value="<?= htmlspecialchars(
        $temat,
        ENT_QUOTES,
        "UTF-8"
    ) ?>"
    required
>

</p>


<p>

<label>
<strong>
Klasa:
</strong>
</label>

<br>

<input
    type="text"
    name="klasa"
    class="input-wide"
    value="<?= htmlspecialchars(
        $klasaTestu,
        ENT_QUOTES,
        "UTF-8"
    ) ?>"
    placeholder="np. 8A"
    required
>

</p>


<p>

<label>
<strong>
Autor testu:
</strong>
</label>

<br>

<input
    type="text"
    name="autor"
    class="input-wide"
    value="<?= htmlspecialchars(
        $autorTestu,
        ENT_QUOTES,
        "UTF-8"
    ) ?>"
    placeholder="np. Jan Kowalski"
    required
>

</p>


<p>

<label>
<strong>
Liczba pytań dla ucznia:
</strong>
</label>

<br>

<input
    type="number"
    name="liczba_pytan"
    value="<?= $liczbaPytan ?>"
    min="1"
    required
>

</p>


<p>

<label>
<strong>
Czas testu w minutach:
</strong>
</label>

<br>

<input
    type="number"
    name="czas_minuty"
    value="<?= $czasMinuty ?>"
    min="1"
    required
>

</p>


<button
    type="submit"
    name="zapisz_ustawienia"
>

Zapisz ustawienia

</button>


</form>


<hr>


<!-- ==================================================
     LOGO
     ================================================== -->

<h2>
Logo na stronie testu
</h2>


<div class="logo-panel">


<?php if ($logoUrl !== null): ?>

<p>

<strong>
Aktualne logo:
</strong>

</p>


<img
    src="<?= htmlspecialchars(
        $logoUrl,
        ENT_QUOTES,
        "UTF-8"
    ) ?>"
    alt="Aktualne logo"
>


<p>

<a
    href="nauczyciel.php?usun_logo=1"
    onclick="return confirm('Czy na pewno usunąć logo?')"
>

<button type="button">
Usuń logo
</button>

</a>

</p>


<?php else: ?>

<p>
Logo nie zostało jeszcze dodane.
</p>

<?php endif; ?>


<form
    method="post"
    enctype="multipart/form-data"
>


<p>

<label>

<strong>
Wybierz nowe logo:
</strong>

</label>

</p>


<input
    type="file"
    name="logo"
    accept=".jpg,.jpeg,.png,.gif,.webp,image/jpeg,image/png,image/gif,image/webp"
    required
>


<p>

Dozwolone formaty:
JPG, PNG, GIF, WEBP.

<br>

Maksymalny rozmiar:
2 MB.

</p>


<button
    type="submit"
    name="wgraj_logo"
>

Wgraj logo

</button>


</form>


</div>


<hr>


<!-- ==================================================
     WYNIKI
     ================================================== -->

<h2>
Wyniki uczniów
</h2>


<?php

if (
    !is_dir(
        $folderWyniki
    )
) {

    echo
        "<p>Brak wyników.</p>";

} else {


    $klasy =
        glob(
            $folderWyniki .
            "/*",
            GLOB_ONLYDIR
        );


    if (!$klasy) {

        echo
            "<p>Brak wyników.</p>";

    } else {


        foreach (
            $klasy
            as $folderKlasy
        ):


            $nazwaKlasy =
                basename(
                    $folderKlasy
                );


            $pliki =
                glob(
                    $folderKlasy .
                    "/*.txt"
                );


            $sumaProcent =
                0;


            $liczbaOcen =
                0;


            // --------------------------------------
            // STATYSTYKA
            // --------------------------------------

            foreach (
                $pliki
                as $plik
            ) {

                $wynik =
                    wczytajWynik(
                        $plik
                    );


                $dane =
                    $wynik["dane"];


                if (
                    isset(
                        $dane["Procent"]
                    )
                ) {

                    $procent =
                        floatval(
                            str_replace(
                                "%",
                                "",
                                $dane["Procent"]
                            )
                        );


                    $sumaProcent +=
                        $procent;


                    $liczbaOcen++;
                }
            }


            $srednia =
                $liczbaOcen > 0
                ? round(
                    $sumaProcent /
                    $liczbaOcen,
                    2
                )
                : 0;

?>


<div class="klasa">


<h3>

Klasa:
<?= htmlspecialchars(
    $nazwaKlasy,
    ENT_QUOTES,
    "UTF-8"
) ?>

</h3>


<p>

Liczba wyników:

<strong>

<?= count($pliki) ?>

</strong>


<br>


Średni wynik:

<strong>

<?= $srednia ?>%

</strong>

</p>


<p>

<a
    href="nauczyciel.php?csv=<?= urlencode(
        $nazwaKlasy
    ) ?>"
>

<button type="button">

Eksportuj wyniki do CSV

</button>

</a>

</p>


<?php if ($pliki): ?>


<table>


<tr>

<th>
Imię
</th>

<th>
Nazwisko
</th>

<th>
Stanowisko
</th>

<th>
Klasa
</th>

<th>
Identyfikator
</th>

<th>
Temat
</th>

<th>
Autor
</th>

<th>
Data
</th>

<th>
Punkty
</th>

<th>
Procent
</th>

<th>
Ocena
</th>

<th>
Akcje
</th>

</tr>


<?php

foreach (
    $pliki
    as $plik
):


    $wynik =
        wczytajWynik(
            $plik
        );


    $dane =
        $wynik["dane"];


    $nazwaPliku =
        basename(
            $plik
        );

?>


<tr>


<td>

<?= htmlspecialchars(
    $dane["Imię"] ?? "",
    ENT_QUOTES,
    "UTF-8"
) ?>

</td>


<td>

<?= htmlspecialchars(
    $dane["Nazwisko"] ?? "",
    ENT_QUOTES,
    "UTF-8"
) ?>

</td>

<td>

<?= htmlspecialchars(
    $dane["Stanowisko"] ?? "",
    ENT_QUOTES,
    "UTF-8"
) ?>

</td>


<td>

<?= htmlspecialchars(
    $dane["Klasa"]
    ??
    $nazwaKlasy,
    ENT_QUOTES,
    "UTF-8"
) ?>

</td>


<td>

<?= htmlspecialchars(
    $dane["Identyfikator"] ?? "",
    ENT_QUOTES,
    "UTF-8"
) ?>

</td>

<td>

<?= htmlspecialchars(
    $dane["Temat"] ?? "",
    ENT_QUOTES,
    "UTF-8"
) ?>

</td>


<td>

<?= htmlspecialchars(
    $dane["Autor"] ?? "",
    ENT_QUOTES,
    "UTF-8"
) ?>

</td>


<td>

<?= htmlspecialchars(
    $dane["Data"] ?? "",
    ENT_QUOTES,
    "UTF-8"
) ?>

</td>


<td>

<?= htmlspecialchars(
    $dane["Uzyskane punkty"] ?? "",
    ENT_QUOTES,
    "UTF-8"
) ?>

</td>


<td>

<?= htmlspecialchars(
    $dane["Procent"] ?? "",
    ENT_QUOTES,
    "UTF-8"
) ?>

</td>


<td>

<?= htmlspecialchars(
    $dane["Ocena"] ?? "",
    ENT_QUOTES,
    "UTF-8"
) ?>

</td>


<td>


<a
    href="nauczyciel.php?klasa=<?= urlencode(
        $nazwaKlasy
    ) ?>&plik=<?= urlencode(
        $nazwaPliku
    ) ?>"
>

Podgląd

</a>


&nbsp;|&nbsp;


<a
    href="nauczyciel.php?usun=<?= urlencode(
        $nazwaPliku
    ) ?>&klasa=<?= urlencode(
        $nazwaKlasy
    ) ?>"
    onclick="return confirm('Czy na pewno usunąć ten wynik?')"
>

Usuń

</a>


</td>


</tr>


<?php

endforeach;

?>


</table>


<?php else: ?>


<p>

Brak zapisanych wyników dla tej klasy.

</p>


<?php endif; ?>


</div>


<?php

        endforeach;
    }
}

?>


<!-- ==================================================
     PODGLĄD WYNIKU
     ================================================== -->

<?php

if (
    isset($_GET["plik"]) &&
    isset($_GET["klasa"])
) {


    $plik =
        basename(
            $_GET["plik"]
        );


    $klasa =
        bezpiecznaNazwa(
            $_GET["klasa"]
        );


    $sciezka =
        $folderWyniki .
        "/" .
        $klasa .
        "/" .
        $plik;


    if (
        file_exists(
            $sciezka
        )
    ) {


        $wynik =
            wczytajWynik(
                $sciezka
            );


        $tresc =
            $wynik["tekst"];

?>


<hr>


<h2>
Podgląd wyniku
</h2>


<pre class="podglad"><?= htmlspecialchars(
    $tresc,
    ENT_QUOTES,
    "UTF-8"
) ?></pre>


<?php

    }
}

?>


</div>

</body>

</html>