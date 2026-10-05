<?php

session_start();


// ==================================================
// USTAWIENIA ŚCIEŻEK
// ==================================================

#$plikPytania = __DIR__ . "/pytania.txt";
$plikUstawienia = __DIR__ . "/ustawienia.txt";

$folderWyniki = __DIR__ . "/odpowiedzi";
$folderLogo = __DIR__ . "/logo";
$folderRysunki = __DIR__ . "/rysunek";

$folderPytania = __DIR__ . "/pytania";
$plikPytania = "pytania.txt";


// ==================================================
// FUNKCJE
// ==================================================

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
        "temat" => "Test",
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


        $klucz = trim($czesci[0]);

        $wartosc = trim($czesci[1]);


        // usunięcie BOM UTF-8
        $klucz = preg_replace(
            '/^\xEF\xBB\xBF/',
            '',
            $klucz
        );


        if ($klucz == "liczba_pytan") {

            $ustawienia["liczba_pytan"] =
                max(
                    1,
                    intval($wartosc)
                );
        }


        elseif ($klucz == "czas_minuty") {

            $ustawienia["czas_minuty"] =
                max(
                    1,
                    intval($wartosc)
                );
        }


        elseif ($klucz == "test_id") {

            $ustawienia["test_id"] =
                $wartosc;
        }

        elseif ($klucz == "temat") {

            $ustawienia["temat"] =
                $wartosc;
        }


        elseif ($klucz == "klasa") {

            $ustawienia["klasa"] =
                $wartosc;
        }


        elseif ($klucz == "autor") {

            $ustawienia["autor"] =
                $wartosc;
        }
    }


    return $ustawienia;
}


// --------------------------------------------------
// WCZYTANIE PYTAŃ
// --------------------------------------------------

function wczytajPytania($plik)
{
    $pytania = [];
    
    if (!file_exists($plik)) {
        return $pytania;
    }

    $linie = file(
        $plik,
        FILE_IGNORE_NEW_LINES
    );


    if (count($linie) > 0) {

        $linie[0] =
            preg_replace(
                '/^\xEF\xBB\xBF/',
                '',
                $linie[0]
            );
    }


    // Usunięcie pustych linii
    $linie = array_values(
        array_filter(
            $linie,
            function ($wartosc) {
                return trim($wartosc) !== "";
            }
        )
    );


    // Każde pytanie = 8 linii
    for (
        $i = 0;
        $i + 7 < count($linie);
        $i += 8
    ) {

        $pytania[] = [

            "numer" =>
                trim($linie[$i]),

            "tresc" =>
                trim($linie[$i + 1]),

            "odpowiedzi" => [

                trim($linie[$i + 2]),

                trim($linie[$i + 3]),

                trim($linie[$i + 4]),

                trim($linie[$i + 5]),

                trim($linie[$i + 6])

            ],

            "poprawna" =>
                strtolower(
                    trim(
                        $linie[$i + 7]
                    )
                )
        ];
    }


    return $pytania;
}


// --------------------------------------------------
// WYSZUKIWANIE LOGO
// --------------------------------------------------

function znajdzLogo($folder)
{
    if (!is_dir($folder)) {
        return null;
    }


    $pliki = scandir($folder);


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


        if (!is_file($sciezka)) {
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

            $nazwa =
                pathinfo(
                    $plik,
                    PATHINFO_FILENAME
                );


            if (
                strtolower($nazwa)
                === "test_logo"
            ) {

                return $plik;
            }
        }
    }


    return null;
}


// --------------------------------------------------
// WYSZUKIWANIE RYSUNKU
// --------------------------------------------------

function znajdzRysunek(
    $numerPytania,
    $folder
) {

    if (!is_dir($folder)) {
        return null;
    }


    $numerPytania =
        trim(
            (string)$numerPytania
        );


    // Usunięcie BOM
    $numerPytania =
        preg_replace(
            '/^\xEF\xBB\xBF/',
            '',
            $numerPytania
        );


    if ($numerPytania === "") {
        return null;
    }


    $pliki = scandir($folder);


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


        if (!is_file($sciezka)) {
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


        $nazwaBezRozszerzenia =
            pathinfo(
                $plik,
                PATHINFO_FILENAME
            );


        if (
            trim(
                $nazwaBezRozszerzenia
            )
            ===
            $numerPytania
        ) {

            return $plik;
        }
    }


    return null;
}


// ==================================================
// WCZYTANIE USTAWIEŃ I PYTAŃ
// ==================================================

$ustawienia =
    wczytajUstawienia(
        $plikUstawienia
    );


$wszystkiePytania =
    wczytajPytania($folderPytania . "/" . $ustawienia["test_id"] ."/" . $plikPytania);


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
// SPRAWDZENIE PYTAŃ
// ==================================================

if (
    count($wszystkiePytania) == 0
) {

    die(
        "Brak pytań w pliku pytania.txt."
    );
}


if (
    $liczbaPytan >
    count($wszystkiePytania)
) {

    $liczbaPytan =
        count($wszystkiePytania);
}


// ==================================================
// LOGO
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


// ==================================================
// ROZPOCZĘCIE TESTU
// ==================================================

if (
    $_SERVER["REQUEST_METHOD"] === "POST" &&
    isset($_POST["akcja"]) &&
    $_POST["akcja"] === "start_test"
) {

    $imie =
        trim(
            $_POST["imie"] ?? ""
        );


    $nazwisko =
        trim(
            $_POST["nazwisko"] ?? ""
        );


    // Klasa pochodzi od nauczyciela
    $klasa =
        trim(
            $klasaTestu
        );


    if (
        $imie === "" ||
        $nazwisko === ""
    ) {

        $bladStart =
            "Należy podać imię oraz nazwisko.";

    }

    elseif (
        $klasa === ""
    ) {

        $bladStart =
            "Nauczyciel nie ustawił klasy testu.";

    }

    else {

        // ------------------------------------------
        // LOSOWANIE PYTAŃ
        // ------------------------------------------

        $indeksy =
            range(
                0,
                count($wszystkiePytania) - 1
            );


        shuffle($indeksy);


        $wybraneIndeksy =
            array_slice(
                $indeksy,
                0,
                $liczbaPytan
            );


        // ------------------------------------------
        // ZAPIS W SESJI
        // ------------------------------------------

        $_SESSION["test_questions"] =
            $wybraneIndeksy;


        $_SESSION["test_start"] =
            time();


        $_SESSION["test_duration"] =
            $czasMinuty * 60;


        $_SESSION["test_completed"] =
            false;


        $_SESSION["uczen"] = [

            "imie" =>
                $imie,

            "nazwisko" =>
                $nazwisko,

            "klasa" =>
                $klasa
        ];


        $_SESSION["test_id"] =
            $test_id;

        $_SESSION["temat_testu"] =
            $temat;


        $_SESSION["autor_testu"] =
            $autorTestu;


        // ------------------------------------------
        // PRZEKIEROWANIE
        // ------------------------------------------

        header(
            "Location: index.php"
        );

        exit;
    }
}


// ==================================================
// PRZYGOTOWANIE WYBRANYCH PYTAŃ
// ==================================================

$wybranePytania = [];


if (
    isset($_SESSION["test_questions"]) &&
    is_array($_SESSION["test_questions"])
) {

    foreach (
        $_SESSION["test_questions"]
        as $indeks
    ) {

        if (
            isset(
                $wszystkiePytania[$indeks]
            )
        ) {

            $wybranePytania[] =
                $wszystkiePytania[$indeks];
        }
    }
}


// ==================================================
// WYNIK TESTU
// ==================================================

$wynik = null;


// ==================================================
// ZAKOŃCZENIE TESTU
// ==================================================

if (
    $_SERVER["REQUEST_METHOD"] === "POST" &&
    isset($_POST["akcja"]) &&
    $_POST["akcja"] === "zakoncz_test"
) {

    // ----------------------------------------------
    // SPRAWDZENIE AKTYWNEGO TESTU
    // ----------------------------------------------

    if (
        !isset(
            $_SESSION["test_questions"]
        ) ||
        !isset(
            $_SESSION["uczen"]
        ) ||
        !isset(
            $_SESSION["test_start"]
        )
    ) {

        die(
            "Nie znaleziono aktywnego testu."
        );
    }


    if (
        isset(
            $_SESSION["test_completed"]
        ) &&
        $_SESSION["test_completed"] === true
    ) {

        die(
            "Ten test został już zakończony."
        );
    }


    // ----------------------------------------------
    // DANE UCZNIA
    // ----------------------------------------------

    $imie =
        $_SESSION["uczen"]["imie"];


    $nazwisko =
        $_SESSION["uczen"]["nazwisko"];


    $klasa =
        $_SESSION["uczen"]["klasa"];


    $test_id =
        $_SESSION["test_id"]
        ?? $test_id;

    $tematTestu =
        $_SESSION["temat_testu"]
        ?? $temat;


    $autorTestu =
        $_SESSION["autor_testu"]
        ?? $autorTestu;


    // ----------------------------------------------
    // SPRAWDZENIE CZASU
    // ----------------------------------------------

    $czasUplynal =
        (
            time() -
            $_SESSION["test_start"]
        )
        >=
        $_SESSION["test_duration"];


    // ----------------------------------------------
    // ODPOWIEDZI
    // ----------------------------------------------

    $odpowiedziUcznia =
        $_POST["odpowiedz"]
        ?? [];


    $punkty = 0;


    $liczbaPytanTestu =
        count(
            $wybranePytania
        );


    $szczegoly = [];


    // ----------------------------------------------
    // SPRAWDZANIE ODPOWIEDZI
    // ----------------------------------------------

    foreach (
        $wybranePytania
        as $nr =>
        $pytanie
    ) {

        $odpowiedzUcznia =
            strtolower(
                trim(
                    $odpowiedziUcznia[$nr]
                    ?? ""
                )
            );


        $poprawna =
            strtolower(
                trim(
                    $pytanie["poprawna"]
                )
            );


        $czyPoprawna =
            (
                $odpowiedzUcznia !== "" &&
                $odpowiedzUcznia === $poprawna
            );


        if ($czyPoprawna) {

            $punkty++;
        }


        $szczegoly[] = [

            "numer" =>
                $nr + 1,

            "pytanie" =>
                $pytanie["tresc"],

            "odpowiedzi" =>
                $pytanie["odpowiedzi"],

            "odpowiedz_ucznia" =>
                $odpowiedzUcznia,

            "poprawna" =>
                $poprawna,

            "czy_poprawna" =>
                $czyPoprawna
        ];
    }


    // ----------------------------------------------
    // PROCENT
    // ----------------------------------------------

    $procent = 0;


    if (
        $liczbaPytanTestu > 0
    ) {

        $procent =
            round(
                (
                    $punkty /
                    $liczbaPytanTestu
                ) * 100,
                2
            );
    }


    // ----------------------------------------------
    // OCENA
    // ----------------------------------------------

    if ($procent >= 90) {

        $ocena = 6;

    }

    elseif ($procent >= 75) {

        $ocena = 5;

    }

    elseif ($procent >= 50) {

        $ocena = 4;

    }

    elseif ($procent >= 35) {

        $ocena = 3;

    }

    else {

        $ocena = 1;
    }


    // ----------------------------------------------
    // FOLDER KLASY
    // ----------------------------------------------

    $bezpiecznaKlasa =
        bezpiecznaNazwa(
            $klasa
        );


    $folderKlasy =
        $folderWyniki .
        "/" .
        $bezpiecznaKlasa;


    if (
        !is_dir($folderKlasy)
    ) {

        mkdir(
            $folderKlasy,
            0777,
            true
        );
    }


    // ----------------------------------------------
    // NAZWA PLIKU
    // ----------------------------------------------

    $bezpieczneNazwisko =
        bezpiecznaNazwa(
            $nazwisko
        );


    $bezpieczneImie =
        bezpiecznaNazwa(
            $imie
        );


    $data =
        date(
            "Y-m-d_H-i-s"
        );


    $losowy =
        rand(
            1000,
            9999
        );


    $nazwaPliku =
        $folderKlasy .
        "/" .
        $bezpieczneNazwisko .
        "_" .
        $bezpieczneImie .
        "_" .
        $data .
        "_" .
        $losowy .
        ".txt";


    // ----------------------------------------------
    // ZAPIS WYNIKU
    // ----------------------------------------------

    $tekst =
        "\xEF\xBB\xBF";


    $tekst .=
        "Imię: " .
        $imie .
        PHP_EOL;


    $tekst .=
        "Nazwisko: " .
        $nazwisko .
        PHP_EOL;


    $tekst .=
        "Stanowisko: " .
        gethostname() .
        PHP_EOL;
        

    $tekst .=
        "Klasa: " .
        $klasa .
        PHP_EOL;


    $tekst .=
        "Identyfikator: " .
        $test_id .
        PHP_EOL;

    $tekst .=
        "Temat: " .
        $tematTestu .
        PHP_EOL;


    $tekst .=
        "Autor: " .
        $autorTestu .
        PHP_EOL;


    $tekst .=
        "Data: " .
        date(
            "Y-m-d H:i:s"
        ) .
        PHP_EOL;


    $tekst .=
        "Liczba pytań: " .
        $liczbaPytanTestu .
        PHP_EOL;


    $tekst .=
        "Uzyskane punkty: " .
        $punkty .
        "/" .
        $liczbaPytanTestu .
        PHP_EOL;


    $tekst .=
        "Procent: " .
        $procent .
        "%" .
        PHP_EOL;


    $tekst .=
        "Ocena: " .
        $ocena .
        PHP_EOL;


    if ($czasUplynal) {

        $tekst .=
            "Test zakończony z powodu upływu czasu." .
            PHP_EOL;
    }


    $tekst .=
        PHP_EOL .
        "SZCZEGÓŁY:" .
        PHP_EOL;


    $tekst .=
        "========================================" .
        PHP_EOL;


    foreach (
        $szczegoly
        as $sz
    ) {

        $tekst .=
            PHP_EOL;


        $tekst .=
            "Pytanie " .
            $sz["numer"] .
            ": " .
            $sz["pytanie"] .
            PHP_EOL;


        foreach (
            $sz["odpowiedzi"]
            as $indeks =>
            $odpowiedz
        ) {

            $litera =
                chr(
                    97 +
                    $indeks
                );


            $tekst .=
                $litera .
                ") " .
                $odpowiedz .
                PHP_EOL;
        }


        $tekst .=
            "Odpowiedź ucznia: " .
            (
                $sz["odpowiedz_ucznia"] !== ""
                    ? $sz["odpowiedz_ucznia"]
                    : "brak odpowiedzi"
            ) .
            PHP_EOL;


        $tekst .=
            "Poprawna odpowiedź: " .
            $sz["poprawna"] .
            PHP_EOL;


        $tekst .=
            "Wynik: " .
            (
                $sz["czy_poprawna"]
                    ? "POPRAWNA"
                    : "BŁĘDNA"
            ) .
            PHP_EOL;
    }


    file_put_contents(
        $nazwaPliku,
        $tekst
    );


    // ----------------------------------------------
    // ZAKOŃCZENIE TESTU
    // ----------------------------------------------

    $_SESSION["test_completed"] =
        true;


    $_SESSION["wynik_testu"] = [

        "punkty" =>
            $punkty,

        "liczba" =>
            $liczbaPytanTestu,

        "procent" =>
            $procent,

        "ocena" =>
            $ocena,

        "czas" =>
            $czasUplynal,

        "test_id" =>
            $test_id,

        "temat" =>
            $tematTestu,

        "autor" =>
            $autorTestu
    ];


    $wynik =
        $_SESSION["wynik_testu"];
}


// ==================================================
// ODCZYT WYNIKU PO ODŚWIEŻENIU
// ==================================================

if (
    isset(
        $_SESSION["test_completed"]
    ) &&
    $_SESSION["test_completed"] === true &&
    isset(
        $_SESSION["wynik_testu"]
    )
) {

    $wynik =
        $_SESSION["wynik_testu"];
}

?>

<!DOCTYPE html>

<html lang="pl">

<head>

<meta charset="UTF-8">

<meta name="viewport"
      content="width=device-width, initial-scale=1.0">

<title>
<?= htmlspecialchars(
    $temat,
    ENT_QUOTES,
    "UTF-8"
) ?>
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
        900px;

    margin:
        auto;

    background:
        white;

    padding:
        30px;

    border-radius:
        12px;

    box-shadow:
        0 2px 10px
        rgba(0,0,0,0.08);
}


/* ==================================================
   LOGO
   ================================================== */

.logo {

    text-align:
        center;

    margin-bottom:
        20px;
}


.logo img {

    max-width:
        300px;

    max-height:
        180px;

    width:
        auto;

    height:
        auto;

    display:
        inline-block;

    object-fit:
        contain;
}


/* ==================================================
   NAGŁÓWKI
   ================================================== */

.start-title {

    text-align:
        center;

    margin-bottom:
        10px;
}


.temat {

    text-align:
        center;

    font-size:
        25px;

    font-weight:
        bold;

    margin-bottom:
        30px;
}


/* ==================================================
   FORMULARZ STARTOWY
   ================================================== */

.form-dane {

    max-width:
        500px;

    margin:
        0 auto
        30px
        auto;
}


.form-dane label {

    display:
        block;

    margin-top:
        15px;

    font-weight:
        bold;
}


.form-dane input[type="text"] {

    width:
        100%;

    padding:
        12px;

    font-size:
        16px;

    box-sizing:
        border-box;

    margin-top:
        5px;
}


.btn-start {

    margin-top:
        25px;

    padding:
        15px
        30px;

    font-size:
        18px;

    cursor:
        pointer;

    width:
        100%;
}


/* ==================================================
   TIMER
   ================================================== */

.timer {

    position:
        sticky;

    top:
        0;

    z-index:
        10;

    background:
        #fff3cd;

    padding:
        15px;

    text-align:
        center;

    font-size:
        24px;

    font-weight:
        bold;

    border:
        1px solid
        #ffeeba;

    margin-bottom:
        20px;

    border-radius:
        8px;
}


/* ==================================================
   INFORMACJE UCZNIA
   ================================================== */

.uczen-info {

    background:
        #f5f5f5;

    padding:
        15px;

    margin-bottom:
        20px;

    border-radius:
        8px;
}


/* ==================================================
   PYTANIE
   ================================================== */

.pytanie {

    border:
        1px solid
        #ccc;

    padding:
        20px;

    margin-bottom:
        20px;

    border-radius:
        8px;
}


/* ==================================================
   ODPOWIEDŹ
   ================================================== */

.odpowiedz {

    margin:
        12px 0;
}


/* ==================================================
   RYSUNEK
   ================================================== */

.rysunek-pytania {

    text-align:
        center;

    margin:
        20px 0;
}


.rysunek-pytania img {

    max-width:
        100%;

    max-height:
        500px;

    width:
        auto;

    height:
        auto;

    display:
        inline-block;

    border-radius:
        6px;
}


/* ==================================================
   PRZYCISK ZAKOŃCZENIA
   ================================================== */

.btn-submit {

    padding:
        15px
        30px;

    font-size:
        18px;

    cursor:
        pointer;
}


/* ==================================================
   WYNIK
   ================================================== */

.wynik {

    text-align:
        center;

    padding:
        30px;

    background:
        #e8f5e9;

    border-radius:
        10px;
}


.wynik .duzy {

    font-size:
        30px;

    margin:
        10px;
}


/* ==================================================
   BŁĄD
   ================================================== */

.blad {

    color:
        #b00020;

    background:
        #ffebee;

    padding:
        15px;

    border-radius:
        8px;

    margin-bottom:
        20px;
}

</style>

</head>


<body>


<div class="container">


<?php

// ==================================================
// WYNIK TESTU
// ==================================================

if (
    $wynik !== null
):

?>


    <?php if ($logoUrl !== null): ?>

    <div class="logo">

        <img
            src="<?= htmlspecialchars(
                $logoUrl,
                ENT_QUOTES,
                "UTF-8"
            ) ?>"
            alt="Logo szkoły"
        >

    </div>

    <?php endif; ?>


    <h1 class="start-title">

        Test zakończony

    </h1>


    <div class="temat">

        <?= htmlspecialchars(
            $wynik["temat"],
            ENT_QUOTES,
            "UTF-8"
        ) ?>

    </div>


    <?php
    if (
        isset(
            $_SESSION["uczen"]
        )
    ):
    ?>

    <div class="uczen-info">

        <strong>
            Uczeń:
        </strong>

        <?= htmlspecialchars(
            $_SESSION["uczen"]["imie"],
            ENT_QUOTES,
            "UTF-8"
        ) ?>

        <?= htmlspecialchars(
            $_SESSION["uczen"]["nazwisko"],
            ENT_QUOTES,
            "UTF-8"
        ) ?>

        <br>

        <strong>
            Klasa:
        </strong>

        <?= htmlspecialchars(
            $_SESSION["uczen"]["klasa"],
            ENT_QUOTES,
            "UTF-8"
        ) ?>


        <?php
        if (
            !empty(
                $wynik["autor"]
            )
        ):
        ?>

        <br>

        <strong>
            Autor testu:
        </strong>

        <?= htmlspecialchars(
            $wynik["autor"],
            ENT_QUOTES,
            "UTF-8"
        ) ?>

        <?php endif; ?>

    </div>

    <?php endif; ?>


    <div class="wynik">


        <p class="duzy">

            Wynik:

            <strong>

                <?= $wynik["punkty"] ?>

                /

                <?= $wynik["liczba"] ?>

            </strong>

        </p>


        <p class="duzy">

            <?= $wynik["procent"] ?>%

        </p>


        <p class="duzy">

            Ocena:

            <strong>

                <?= $wynik["ocena"] ?>

            </strong>

        </p>


        <?php if ($wynik["czas"]): ?>

        <p>

            <strong>

                Test został zakończony
                z powodu upływu czasu.

            </strong>

        </p>

        <?php endif; ?>


    </div>


<?php

// ==================================================
// AKTYWNY TEST
// ==================================================

elseif (
    isset(
        $_SESSION["test_questions"]
    ) &&
    isset(
        $_SESSION["uczen"]
    )
):

?>


    <?php if ($logoUrl !== null): ?>

    <div class="logo">

        <img
            src="<?= htmlspecialchars(
                $logoUrl,
                ENT_QUOTES,
                "UTF-8"
            ) ?>"
            alt="Logo szkoły"
        >

    </div>

    <?php endif; ?>


    <h1 class="start-title">

        Test

    </h1>


    <div class="temat">

        <?= htmlspecialchars(
            $_SESSION["temat_testu"]
            ?? $temat,
            ENT_QUOTES,
            "UTF-8"
        ) ?>

    </div>


    <div class="uczen-info">

        <strong>
            Uczeń:
        </strong>

        <?= htmlspecialchars(
            $_SESSION["uczen"]["imie"],
            ENT_QUOTES,
            "UTF-8"
        ) ?>

        <?= htmlspecialchars(
            $_SESSION["uczen"]["nazwisko"],
            ENT_QUOTES,
            "UTF-8"
        ) ?>

        <br>

        <strong>
            Klasa:
        </strong>

        <?= htmlspecialchars(
            $_SESSION["uczen"]["klasa"],
            ENT_QUOTES,
            "UTF-8"
        ) ?>

    </div>


    <?php

    $pozostalyCzas =
        $_SESSION["test_duration"] -
        (
            time() -
            $_SESSION["test_start"]
        );


    if (
        $pozostalyCzas < 0
    ) {

        $pozostalyCzas = 0;
    }

    ?>


    <div class="timer">

        Pozostały czas:

        <span id="timer">
            --:--
        </span>

    </div>


    <form
        method="post"
        id="testForm"
    >


        <input
            type="hidden"
            name="akcja"
            value="zakoncz_test"
        >


        <?php

        foreach (
            $wybranePytania
            as $nr =>
            $pytanie
        ):

        ?>


        <div class="pytanie">


            <h3>

                Pytanie
                <?= $nr + 1 ?>

            </h3>


            <p>

                <strong>

                    <?= htmlspecialchars(
                        $pytanie["tresc"],
                        ENT_QUOTES,
                        "UTF-8"
                    ) ?>

                </strong>

            </p>


            <?php

            // ----------------------------------------
            // RYSUNEK
            // ----------------------------------------

            $nazwaRysunku =
                znajdzRysunek(
                    $pytanie["numer"],
                    $folderRysunki
                );

            ?>


            <?php
            if (
                $nazwaRysunku !== null
            ):
            ?>


            <div class="rysunek-pytania">

                <img
                    src="rysunek/<?= rawurlencode(
                        $nazwaRysunku
                    ) ?>"
                    alt="Rysunek do pytania"
                >

            </div>


            <?php endif; ?>


            <?php

            foreach (
                $pytanie["odpowiedzi"]
                as $indeks =>
                $odpowiedz
            ):

            ?>


            <?php

            $litera =
                chr(
                    97 +
                    $indeks
                );

            ?>


            <div class="odpowiedz">

                <label>

                    <input
                        type="radio"
                        name="odpowiedz[<?= $nr ?>]"
                        value="<?= $litera ?>"
                    >

                    <?= $litera ?>)

                    <?= htmlspecialchars(
                        $odpowiedz,
                        ENT_QUOTES,
                        "UTF-8"
                    ) ?>

                </label>

            </div>


            <?php endforeach; ?>


        </div>


        <?php endforeach; ?>


        <p style="text-align:center;">

            <button
                type="submit"
                class="btn-submit"
            >

                Zakończ test

            </button>

        </p>


    </form>


<script>

let pozostalyCzas =
    <?= $pozostalyCzas ?>;


const timer =
    document.getElementById(
        "timer"
    );


const formularz =
    document.getElementById(
        "testForm"
    );


let testWyslany =
    false;


function aktualizujTimer()
{

    let minuty =
        Math.floor(
            pozostalyCzas / 60
        );


    let sekundy =
        pozostalyCzas % 60;


    if (
        sekundy < 10
    ) {

        sekundy =
            "0" + sekundy;
    }


    timer.textContent =
        minuty +
        ":" +
        sekundy;


    if (
        pozostalyCzas <= 0
    ) {

        if (
            !testWyslany
        ) {

            testWyslany =
                true;


            alert(
                "Czas na rozwiązanie testu minął."
            );


            formularz.submit();
        }


        return;
    }


    pozostalyCzas--;


    setTimeout(
        aktualizujTimer,
        1000
    );
}


aktualizujTimer();

</script>


<?php

// ==================================================
// STRONA STARTOWA
// ==================================================

else:

?>


    <?php if ($logoUrl !== null): ?>

    <div class="logo">

        <img
            src="<?= htmlspecialchars(
                $logoUrl,
                ENT_QUOTES,
                "UTF-8"
            ) ?>"
            alt="Logo szkoły"
        >

    </div>

    <?php endif; ?>


    <h1 class="start-title">

        Rozpoczęcie testu

    </h1>


    <div class="temat">

        <?= htmlspecialchars(
            $temat,
            ENT_QUOTES,
            "UTF-8"
        ) ?>

    </div>


    <?php if ($klasaTestu !== ""): ?>

    <p style="text-align:center;">

        Klasa:
        <strong>

            <?= htmlspecialchars(
                $klasaTestu,
                ENT_QUOTES,
                "UTF-8"
            ) ?>

        </strong>

    </p>

    <?php endif; ?>


    <?php if ($autorTestu !== ""): ?>

    <p style="text-align:center;">

        Autor:
        <strong>

            <?= htmlspecialchars(
                $autorTestu,
                ENT_QUOTES,
                "UTF-8"
            ) ?>

        </strong>

    </p>

    <?php endif; ?>


    <?php if (isset($bladStart)): ?>

    <div class="blad">

        <?= htmlspecialchars(
            $bladStart,
            ENT_QUOTES,
            "UTF-8"
        ) ?>

    </div>

    <?php endif; ?>


    <div class="form-dane">


        <form method="post">


            <input
                type="hidden"
                name="akcja"
                value="start_test"
            >


            <label for="imie">

                Imię:

            </label>


            <input
                type="text"
                id="imie"
                name="imie"
                required
                autocomplete="given-name"
            >


            <label for="nazwisko">

                Nazwisko:

            </label>


            <input
                type="text"
                id="nazwisko"
                name="nazwisko"
                required
                autocomplete="family-name"
            >


            <button
                type="submit"
                class="btn-start"
            >

                Rozpocznij test

            </button>


        </form>


    </div>


    <p
        style="
            text-align:center;
            color:#666;
        "
    >

        Liczba pytań:

        <strong>

            <?= $liczbaPytan ?>

        </strong>


        &nbsp;&nbsp; | &nbsp;&nbsp;


        Czas:

        <strong>

            <?= $czasMinuty ?>
            min

        </strong>

    </p>


<?php endif; ?>


</div>

</body>

</html>