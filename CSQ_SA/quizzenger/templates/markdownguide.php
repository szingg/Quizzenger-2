<?php

?>
<div class="panel panel-default">
	<div class="panel-heading">
		<strong>Markdown Guide</strong>
	</div>
	<div class="panel-body" style="max-width:600px; ">
	Hier bekommst du einen Überblick über die Auszeichnung von Fragetexten mittels der “Markdown”-Beschreibungssprache. Weitere Details können <strong><a class="text-primary" href="http://daringfireball.net/projects/markdown/syntax." >hier</a></strong> nachgelesen werden.
	<br><br>
	<h4>Absätze & Zeilenumbrüche</h4>
	Einzelne Zeilenumbrüche werden nicht berücksichtigt, d.h. sie zählen zum gleichen Absatz. Falls ein neuer Absatz erstellt werden soll, müssen zwei Leerzeilen geschrieben werden.
	<br>
	<h4>Escape-Sequenzen</h4>
	Einige Zeichen dienen der Textformatierung. Falls diese Zeichen direkt verwendet werden sollen, müssen Sie mit einem Backslash (“\”) gekenntzeichnet werden. Folgende Zeichen können davon betroffen sein, je nach Kontext: “\ ` * _ { } [ ] ( ) # + - !”.
	<br>
	<h4>Kursiv & Fett</h4>
	Mittels Sternchen können Textstellen hervorgehoben werden.<br />
	&emsp;*Dieser Text ist kursiv.*<br />
	&emsp;**Dieser Text ist fett.**
	<br />
	<h4>Überschriften</h4>
	Zeilen, welche mit Rauten (“#”) anfangen leiten Überschriften ein. Die Anzahl der Rauten legt dabei die Stufe der Überschrift fest.<br />
	&emsp;# Überschrift 01<br />
	&emsp;## Überschrift 02<br />
	&emsp;### Überschrift 03
	<br />
	<h4>Zitate</h4>
	Blockzitate werden mit dem Vergleichszeichen “>” eingeleitet.<br />
	&emsp;> Dies ist ein Zitat<br />
	&emsp;> über mehrere Zeilen
	<br />
	<h4>Listen</h4>
	Es gibt zwei Varianten von Listen, geordnete und ungeordnete. Das folgende Format beschreibt eine ungeordnete Liste von Einträgen.<br />
	&emsp;* Eintrag 01<br />
	&emsp;* Eintrag 02<br />
	Eine geordnete (nummerierte) Liste wird durch eine Zahl eingeleitet:<br />
	&emsp;0. Eintrag 01<br />
	&emsp;0. Eintrag 02<br />
	<i>Merke: Die eigentliche Zahl zu Anfang hat keine Bedeutung, die Liste wird stets automatisch und korrekt nummeriert angezeigt.</i>
	<br />
	<h4>Code-Auszüge</h4>
	Code wird ganz einfach durch Einrückung ausgezeichnet. Dabei ist zu beachten, das mindestens vier Leerzeichen verwendet werden.<br />
	&emsp;Programm:<br />
	&emsp;&emsp;int main() {<br />
	&emsp;&emsp;&emsp;std::cout << “Hello World” << std::endl;<br />
	&emsp;&emsp;}<br />
	Alternativ kann Code auch innerhalb eines Satzes mittels Backtick (“`”) formatiert werden.<br />
	&emsp;Der Dateiname ist `install.sh`.
	<br />
	<h4>Horizontale Trennlinien</h4>
	Trennlinien erfordern lediglich einige Sternchen.<br />
	&emsp;Text oberhalb.<br />
	&emsp;***************<br />
	&emsp;Text unterhalb.
	<br />
	<h4>Hyperlinks</h4>
	Mittels einem zweifachen Klammernpaar können Hyperlinks definiert werden.<br />
	&emsp;[Ein Klick zu Google](http://www.google.com/)
	<br />
	<h4>Anhänge (“Attachments”)</h4>
	Es ist möglich, einer Frage einen Anhang (Bild / YouTube Video, …) hinzuzufügen. Dieser Anhang kann wiefolgt in die Fragestellung eingebettet werden:<br />
	&emsp;Hier ein Bild zur Frage:<br />
	&emsp;[attachment]<br />
	&emsp;Was ist auf dem obenstehenden Bild zu erkennen?<br />
	</div>
</div>
