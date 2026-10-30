
const przyciski =
	document.querySelectorAll(
		".przyciski button"
	);

const zawartosci =
	document.querySelectorAll(
		".zawartosc"
	);

przyciski.forEach(przycisk => {
	przycisk.addEventListener("click", () => {
		
		// Wyłącz wszystkie zakładki
		przyciski.forEach(p =>
			p.classList.remove("aktywna")
		);

		zawartosci.forEach(z =>
			z.classList.remove("aktywna")
		);

		// Aktywuj wybraną zakładkę
		przycisk.classList.add("aktywna");

		document.getElementById(
			przycisk.dataset.zakladka
		).classList.add("aktywna");
	});
});

function onSubmit() {
	v = document.getElementById("skala").selectedIndex;
	if (v <= 0) {
		alert("Nie wybrano skali ocen");
		return false;
	}
	return true;
}

