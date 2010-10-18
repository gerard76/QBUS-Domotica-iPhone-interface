de functie qbus_get_status() in qbus.lib.php roept een tidy functie aan. Die moet je dus wel op het systeem geinstalleerd hebben. Dit omdat de qbus nogal slechte html uitspuugt.

Ik heb zelf hier een webcam hangen die ik ook in de interface gestopt heb. Die moet je er maar uit slopen.

In config.inc.php even de juiste db gegevens zetten en het ip ed. van je qbus installatie

functions.lib.php bevat allerlei algemene functies die ik in allerlei projecten heb gebruikt. zullen ook wel veel dingen in staan die niet gebruikt worden.

In de qbus lib staat nog een beginnetje van een qbus class die direct over de socket praat. ik heb dit nooit afgemaakt. en kun je negeren. wordt nu niet gebruikt. Huidige versie gebruikt de html interface van de qbus.

Het menu moet je zelf maken op de qbus mbv de standaard qbus software. Daarna moet je in de database van deze app in Modules table definieren hoe je menu er op de qbus uitziet anders weet de app niet welke knop wat doet. Reteomslachtig, maar het is ook alleen maar een proof of concept.1ste knop op de qbus interface krijgt id 1, 2de knop 2 etc.

pages, page_modules is de interface definitie die je op de iphone ziet.
users lijkt me duidelijk.
