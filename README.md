# Massage Pricelist

To jest wtyczka do edytora Wordpressa Gutenberg. Wtyczka służy do tworzenia listy usług - w tym przypadku masaży, zabiegów i pakietów na stronie studia masażu Słow Flow. 
Wtyczka napisana jest w php i javascript oraz ostylowana w css.

---

## Struktura projektu

Wtyczka wykorzystuje nowoczesne podejście oparte o metadane w formacie JSON oraz skrypty `@wordpress/scripts`:

* massage-pricelist.php – Główny plik PHP wtyczki. Odpowiada za inicjalizację wtyczki oraz rejestrację bloku po stronie serwera.
  * edit.js – Główny punkt wejścia dla logiki bloku
  * view.js – Filtracja usług po stronie frontendu
  * block.json – Plik konfiguracyjny (metadane bloku: nazwa, tytuł, kategoria, ikona, powiązane skrypty i style).
  * style.css – Style CSS/SCSS dla edytora oraz front-endu.

---

## Wymagania wstępne

Aby pracować nad projektem i go budować, potrzebujesz:
* WordPress w wersji **6.0+**
* PHP w wersji **7.4+** (zalecane 8.0+)
* Node.js (wersja LTS zalecana) oraz npm
