<div align="center">  
  <a href="README.md"   >   TR <img style="padding-top: 8px" src="https://raw.githubusercontent.com/yammadev/flag-icons/master/png/TR.png" alt="TR" height="20" /></a>  
  <a href="README-EN.md"> | EN <img style="padding-top: 8px" src="https://raw.githubusercontent.com/yammadev/flag-icons/master/png/US.png" alt="EN" height="20" /></a>  
  <a href="README-CN.md"> | CN <img style="padding-top: 8px" src="https://raw.githubusercontent.com/yammadev/flag-icons/master/png/CN.png" alt="CN" height="20" /></a>  
  <a href="README-AZ.md"> | AZ <img style="padding-top: 8px" src="https://raw.githubusercontent.com/yammadev/flag-icons/master/png/AZ.png" alt="AZ" height="20" /></a>  
  <a href="README-DE.md"> | DE <img style="padding-top: 8px" src="https://raw.githubusercontent.com/yammadev/flag-icons/master/png/DE.png" alt="DE" height="20" /></a>  
  <a href="README-FR.md"> | FR <img style="padding-top: 8px" src="https://raw.githubusercontent.com/yammadev/flag-icons/master/png/FR.png" alt="FR" height="20" /></a>  
  <a href="README-AR.md"> | SA <img style="padding-top: 8px" src="https://raw.githubusercontent.com/yammadev/flag-icons/master/png/SA.png" alt="AR" height="20" /></a>  
  <a href="README-NL.md"> | NL <img style="padding-top: 8px" src="https://raw.githubusercontent.com/yammadev/flag-icons/master/png/NL.png" alt="NL" height="20" /></a>  
</div>


## Installations- und Integrationsanleitung

### Mindestanforderungen

- WHMCS 7.8 oder höher
- PHP7.4 oder höher (empfohlen: 8.1)
- Aktiviertes PHP SOAPClient-Plugin
- Kundenspezifische Felder für türkische T.C. mit Identitätsinformationen / Steuernummer / Steuerbüroinformationen (optional)

## Setup

!!!! Achtung !!!!

_**Wenn Sie ein Upgrade durchführen, sichern Sie Ihre alten Dateien vor der Installation.**_

Legen Sie den "modules"-Ordner in den Ordner, den Sie heruntergeladen haben, in den Ordner, in dem WHMCS installiert ist (Beispiel: /home/whmcs/public_html). Löschen Sie die Dateien .gitignore, README.md und LICENSE.

<a href="https://user-images.githubusercontent.com/3975986/209725636-b6b41019-3810-412c-8c52-616aab3760ad.png"><img width="400" alt="image" src="https://user-images.githubusercontent.com/3975986/209725636-b6b41019-3810-412c-8c52-616aab3760ad.png"></a>

- Gehen Sie zum Abschnitt "Systemeinstellungen"

<hr>

<a href="https://user-images.githubusercontent.com/3975986/209725739-96ab634d-9cc4-486d-a258-88065ab55c0b.png"><img width="400" alt="image" src="https://user-images.githubusercontent.com/3975986/209725739-96ab634d-9cc4-486d-a258-88065ab55c0b.png"></a>

- Gehen Sie zum Abschnitt "Domainregistrar"

<hr>

<a href="https://user-images.githubusercontent.com/3975986/209726687-fbf56bd3-e78a-457c-a118-86f87b9db6f0.png"><img width="400" alt="image" src="https://user-images.githubusercontent.com/3975986/209726687-fbf56bd3-e78a-457c-a118-86f87b9db6f0.png"></a>

- Wenn Sie die Moduldateien in den richtigen Ordner verschoben haben, wird "DomainNameAPI" auf der eingegebenen Seite angezeigt.
- Nach der Aktivierung geben Sie den Benutzernamen und das von uns erhaltene Passwort ein.
- Nach dem Speichern werden Ihr Benutzername und das aktuelle Guthaben angezeigt.
- Stimmen Sie die in den Einstellungen angezeigten Informationen zur türkischen Identifikationsnummer und Steuernummer ab, um den .tr-Domänennamen Ihrer Benutzer zu erhalten, sofern vorhanden.
- Wenn Sie außer USD eine Einheitswährung verwenden, können Sie die Einstellung "Exchange Convertion For TLD Sync" festlegen. (Diese Einstellung dient nur der Preissynchronisierung für regionale TLD-Imports. Andernfalls müssen Sie nichts ändern.)

<a href="https://youtu.be/LEw_iMnquSo">+ YouTube-Link</a>

<hr>

## Preisgestaltung, TLD-Zuordnung und Such-Einstellungen

<a href="https://user-images.githubusercontent.com/3975986/209727461-dd79f4a8-ed49-45cd-b305-26a5d37c6fd9.png"><img width="400" alt="image" src="https://user-images.githubusercontent.com/3975986/209727461-dd79f4a8-ed49-45cd-b305-26a5d37c6fd9.png"></a>

- Gehen Sie zu "Domainpreise" unter "Systemeinstellungen".
<hr>

<a href="https://user-images.githubusercontent.com/3975986/209728124-fe1aabdc-b0b0-4b7c-be2a-ff3b572a56a4.png"><img width="400" alt="image" src="https://user-images.githubusercontent.com/3975986/209728124-fe1aabdc-b0b0-4b7c-be2a-ff3b572a56a4.png"></a>

- Wählen Sie die TLD aus, die Sie verkaufen möchten (Beispiel: .com.tr).
- Wählen Sie "Domain Name API" für die automatische Registrierung.
- Wählen Sie die EPP-Code-Option.
- Geben Sie die Preise manuell ein oder legen Sie einen Gruppenpreis fest (wird im nächsten Abschnitt erklärt).

<a href="https://user-images.githubusercontent.com/3975986/209728748-51ae6bbe-018c-42a2-b85d-ab5f37cd6559.png"><img width="400" alt="image" src="https://user-images.githubusercontent.com/3975986/209728748-51ae6bbe-018c-42a2-b85d-ab5f37cd6559.png"></a>

- Anstatt öffentliche Whois-Server als Domainabfragenquelle zu verwenden, können Sie die Domainname-API verwenden. Klicken Sie dazu auf die Schaltfläche "Ändern" im Abschnitt "Suchanbieter", wählen Sie die Option "DomainNameApi" aus, die am Ende nach der Option "Domainregistrator" angezeigt wird, und wählen Sie dann aus, welche TLDs verwendet werden sollen.

Weitere Informationen finden Sie unter: <a href="https://docs.whmcs.com/Domain_Pricing">Whmcs Domainpreise</a>
<hr>

## Massenpreisgestaltung und automatische Preisgestaltung

<a href="https://user-images.githubusercontent.com/3975986/209730191-0b796b2f-7f90-4dba-9a17-8ed2e11e11b8.png"><img width="400" alt="image" src="https://user-images.githubusercontent.com/3975986/209730191-0b796b2f-7f90-4dba-9a17-8ed2e11e11b8.png"></a>

<a href="https://user-images.githubusercontent.com/3975986/209730869-5f667f65-4da7-401e-b39c-fa91d23d2682.png"><img width="400" alt="image" src="https://user-images.githubusercontent.com/3975986/209730869-5f667f65-4da7-401e-b39c-fa91d23d2682.png"></a>

- Gehen Sie zu "Registrar TLD Sync" im Bereich "Dienstprogramme". Wählen Sie "DomainNameApi" aus, warten Sie einen Moment.
- Auf der nächsten Seite werden alle TLDs in unserem System mit allen TLDs in WHMCS verglichen. Der Gewinn- und Verlustmarge wird berechnet und in Stapelverarbeitung angezeigt, was den Import ermöglicht.

Weitere Informationen finden Sie unter: <a href="https://docs.whmcs.com/Registrar_TLD_Sync">Whmcs TLD Sync</a>
<hr>

## Sicht des Managers

<a href="https://user-images.githubusercontent.com/3975986/209735794-6f2d6dbe-c4e2-463c-b768-1d79fe3b6d81.png"><img width="400" alt="image" src="https://user-images.githubusercontent.com/3975986/209735794-6f2d6dbe-c4e2-463c-b768-1d79fe3b6d81.png"></a>

- Sie können eine "Löschungsanfrage" für den Domainnamen senden.
- Sie können eine "Transferstornierung" für den Domainnamen senden.
- Sie können den aktuellen Status, den sofortigen Start und das Ende des Domainnamens anzeigen.
- Sie können Ihre Subdomains auflisten.
- Sie können zusätzliche Feldinformationen anzeigen.

<hr>

## Allgemeine Einstellungen

<a href="https://user-images.githubusercontent.com/3975986/209731622-51b3cd62-1c23-4257-a30c-ce3a00d10bf3.png"><img width="400" alt="image" src="https://user-images.githubusercontent.com/3975986/209731622-51b3cd62-1c23-4257-a30c-ce3a00d10bf3.png"></a>
<a href="https://user-images.githubusercontent.com/3975986/209732098-7dba4e20-220d-4450-be3b-0ad1f9b8083d.png"><img width="400" alt="image" src="https://user-images.githubusercontent.com/3975986/209732098-7dba4e20-220d-4450-be3b-0ad1f9b8083d.png"></a>

- Gehen Sie zu "Allgemeine Einstellungen" in den Systemeinstellungen und wählen Sie den Tab "Domain" aus.
- Aktivieren Sie die Option "Kundenregistrierung von Domains zulassen", wenn Sie möchten, dass Ihre Kunden selbst Domainnamen registrieren können.
- Aktivieren Sie die Option "Kundentransfer einer Domain zu Ihnen zulassen", wenn Sie möchten, dass Ihre Kunden die Domain selbst übertragen können.
- Aktivieren Sie die Option "Erneuerungsbestellungen zulassen", wenn Sie möchten, dass Ihre Kunden ihren Domainnamen vor dem Fälligkeitsdatum erneuern können.
- Aktivieren Sie die Option "Automatische Verlängerung bei Zahlung", wenn Sie möchten, dass die Zahlungserneuerung gleichzeitig bei Ihren Kunden durchgeführt wird.
- Aktivieren Sie die Option "Domänensynchronisierung aktiviert", wenn Sie möchten, dass die aktuelle Domäne in regelmäßigen Abständen überprüft und synchronisiert wird. Wir empfehlen, diese Option zu aktivieren.
- Aktivieren Sie die Option "IDN-Domains zulassen", wenn Sie türkische, hebräische, arabische, russische usw. Domainnamen verwalten möchten.
- Geben Sie Ihre Nameserver-Informationen in das Feld "Standard-Nameserver" ein.

<hr>

## Synchronisierungseinstellungen

<a href="https://user-images.githubusercontent.com/3975986/209734789-de8a1692-281f-452d-900a-ab662f2aa4f6.png"><img width="400" alt="image" src="https://user-images.githubusercontent.com/3975986/209734789-de8a1692-281f-452d-900a-ab662f2aa4f6.png"></a>
<a href="https://user-images.githubusercontent.com/3975986/209734883-a96c13d8-6275-4fb3-b500-fc3a05b6c11f.png"><img width="400" alt="image" src="https://user-images.githubusercontent.com/3975986/209734883-a96c13d8-6275-4fb3-b500-fc3a05b6c11f.png"></a>

- Gehen Sie zu "Automatisierungseinstellungen" in den Systemeinstellungen. Gehen Sie zum Abschnitt "Domänen-Sync-Einstellungen".
- Aktivieren Sie die Domänensynchronisierung.
- Aktivieren Sie die Option "Nächste Fälligkeitsdatum synchronisieren", wenn Sie möchten, dass das Enddatum bei Aktualisierungen geändert wird.
- Passen Sie die anderen Einstellungen je nach Intensität Ihres Systems an.

<hr>

## Fehlerbehebung - Detailansicht

<a href="https://user-images.githubusercontent.com/3975986/209735161-1455e50b-e25c-4cab-9069-b1eb746b3a65.png"><img width="400" alt="image" src="https://user-images.githubusercontent.com/3975986/209735161-1455e50b-e25c-4cab-9069-b1eb746b3a65.png"></a>
<a href="https://user-images.githubusercontent.com/3975986/209735249-54826bd6-7f03-4827-94e1-110e6929da97.png"><img width="400" alt="image" src="https://user-images.githubusercontent.com/3975986/209735249-54826bd6-7f03-4827-94e1-110e6929da97.png"></a>

- Gehen Sie zur Modul-Protokollansicht in den Systemprotokollen auf der rechten Seite.
- Suchen Sie das relevante Protokoll und klicken Sie auf das Datum.
- Sie können detaillierte Anfragen, Antworten und gefilterte Antworten anzeigen.

!! Wir empfehlen, das Systemprotokoll für den täglichen Gebrauch aus Gründen der Systemleistung zu deaktivieren. Weitere Informationen finden Sie unter: <a href="https://docs.whmcs.com/System_Logs">Whmcs Logging</a>

## Tests

| Testname        | GTLD | TRTLD |
|-----------------|------|-------|
| Registrierung   | ✓    | ✓     |
| Transfer        | ✓    | ✓     |
| Verlängerung    | ✓    | ✓     |
| Nameserver      | ✓    | ✓     |
| RegistrarLock   | ✓    | ✓     |
| Kontakt         | ✓    | ✓     |
| EPP             | ✓    | ✓     |
| Löschen         | ✓    | ✓     |
| SubNameserver   | ✓    | ✓     |
| Verfügbarkeit   | ✓    | ✓     |
| Preissynchronisierung | ✓ | ✓ |
| Transferabbrechen | ✓ | ✓   |
| Synchronisierung | ✓ | ✓    |
| Transfersynchronisierung | ✓ | ✓ |

## Fehlerbehebung

- Ich habe bereits neue benutzerdefinierte Felder hinzugefügt, aber in den Einstellungen kann ich sie nicht sehen.
- Der Cache ist möglicherweise abgelaufen. Löschen Sie alle Dateien im Cache-Ordner.

<hr>

- Ich erhalte den Fehler "Parsing WSDL: Couldn't load from..."
- Es scheint ein Netzwerkproblem vorzuliegen. Die IP-Adresse Ihres Servers könnte vom Register blockiert sein. Kontaktieren Sie uns zur Lösung.
