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


## Installatie- en integratiehandleiding

### Minimale vereisten

- WHMCS 7.8 of hoger
- PHP7.4 of hoger (Aanbevolen 8.1)
- PHP SOAPClient-plug-in moet actief zijn.
- Klant T.C. Aangepaste velden met identiteitsinformatie / BTW-nummer / Belastingkantoorinformatie. (Optioneel)

## Installatie

!!!! Let op !!!!

_**Maak een back-up van uw oude bestanden voordat u de installatie uitvoert.**_

Plaats de map "modules" in de map die u hebt gedownload in de map waar Whmcs is geïnstalleerd. (Bijvoorbeeld: /home/whmcs/public_html) Verwijder .gitinore, README.md, LICENTIE-bestanden.


<a href="https://user-images.githubusercontent.com/3975986/209725636-b6b41019-3810-412c-8c52-616aab3760ad.png"><img width="400"  alt="afbeelding" src="https://user-images.githubusercontent.com/3975986/209725636-b6b41019-3810-412c-8c52-616aab3760ad.png"></a>

- Ga naar het gedeelte Systeeminstellingen

<hr>

<a href="https://user-images.githubusercontent.com/3975986/209725739-96ab634d-9cc4-486d-a258-88065ab55c0b.png"><img width="400"  alt="afbeelding" src="https://user-images.githubusercontent.com/3975986/209725739-96ab634d-9cc4-486d-a258-88065ab55c0b.png"></a>

- Ga naar het gedeelte Domeinregistrar

<hr>

<a href="https://user-images.githubusercontent.com/3975986/209726687-fbf56bd3-e78a-457c-a118-86f87b9db6f0.png"><img width="400"  alt="afbeelding" src="https://user-images.githubusercontent.com/3975986/209726687-fbf56bd3-e78a-457c-a118-86f87b9db6f0.png"></a>

- Op de pagina die u hebt ingevoerd, als u de modulebestanden in de juiste map hebt achtergelaten, verschijnt "DomainNameAPI".
- Nadat u deze heeft geactiveerd, voert u de gebruikersnaam en het wachtwoord in die u van ons heeft ontvangen.
- Nadat u heeft opgeslagen, is uw gebruikersnaam en huidige saldo zichtbaar.
- Stem het TR-identiteitsnummer en de btw-nummerinformatie af die wordt gebruikt om de .tr-domeinnaam van uw gebruikers te verkrijgen, indien aanwezig, op de instellingen die u hebt gezien.
- Als u een enkele primaire valuta gebruikt behalve USD, kunt u de instelling "Exchange Convertion For TLD Sync" instellen. (Deze instelling wordt alleen gebruikt voor prijssynchronisatie voor regionale TLD-imports. Anders hoeft u niets te wijzigen)


<a href="https://youtu.be/LEw_iMnquSo">+ Youtube-link </a>


<hr>

## Prijsstelling, TLD-attributie en Zoekinstellingen


<a href="https://user-images.githubusercontent.com/3975986/209727461-dd79f4a8-ed49-45cd-b305-26a5d37c6fd9.png"><img width="400"  alt="afbeelding" src="https://user-images.githubusercontent.com/3975986/209727461-dd79f4a8-ed49-45cd-b305-26a5d37c6fd9.png"></a>

-Ga naar Domeinprijzen in Systeeminstellingen.
<hr>

<a href="https://user-images.githubusercontent.com/3975986/209728124-fe1aabdc-b0b0-4b7c-be2a-ff3b572a56a4.png"><img width="400"  alt="afbeelding" src="https://user-images.githubusercontent.com/3975986/209728124-fe1aabdc-b0b0-4b7c-be2a-ff3b572a56a4.png"></a>

- Bepaal de TLD die u wilt verkopen. (Bijvoorbeeld: .com.tr)
- Selecteer "Domain Name API" voor automatische registratie.
- Selecteer de EPP-codeoptie.
- Voor prijsstelling kunt u handmatig invoeren. U kunt ook een bulkprijs instellen. (wordt uitgelegd in de volgende sectie).

<a href="https://user-images.githubusercontent.com/3975986/209728748-51ae6bbe-018c-42a2-b85d-ab5f37cd6559.png"><img width="400"  alt="afbeelding" src="https://user-images.githubusercontent.com/3975986/209728748-51ae6bbe-018c-42a2-b85d-ab5f37cd6559.png"></a> 

- In plaats van openbare Whois-servers te gebruiken als een domeinquerybron, kunt u de domainname-api gebruiken. Druk hiervoor op de knop "Wijzigen" in het gedeelte "Lookup-provider", selecteer de optie "DomainNameApi" die onderaan verschijnt na de domeinregistratie-optie, kies vervolgens welke TLD's u wilt gebruiken.


Voor meer informatie: <a href="https://docs.whmcs.com/Domain_Pricing">Whmcs Domain Pricing</a>
<hr>

## Bulk-prijzen & & Geautomatiseerde prijzen

<a href="https://user-images.githubusercontent.com/3975986/209730191-0b796b2f-7f90-4dba-9a17-8ed2e11e11b8.png"><img width="400"  alt="afbeelding" src="https://user-images.githubusercontent.com/3975986/209730191-0b796b2f-7f90-4dba-9a17-8ed2e11e11b8.png"></a>

<a href="https://user-images.githubusercontent.com/3975986/209730869-5f667f65-4da7-401e-b39c-fa91d23d2682.png"><img width="400"  alt="afbeelding" src="https://user-images.githubusercontent.com/3975986/209730869-5f667f65-4da7-401e-b39c-fa91d23d2682.png"></a>

- Ga naar Registrar TLD Sync vanuit het gedeelte Hulpprogramma's. Selecteer "DomainNameApi" op het scherm dat verschijnt, wacht even.
- Op het volgende scherm worden alle TLD's in ons systeem gekruist met alle TLD's op whmcs, winstmarge en verlies worden berekend en weergegeven in bulk, zodat import mogelijk is.
Voor meer informatie: <a href="https://docs.whmcs.com/Registrar_TLD_Sync">Whmcs TLD Sync</a>


<hr>

## Perspectief van de beheerder
<a href="https://user-images.githubusercontent.com/3975986/209735794-6f2d6dbe-c4e2-463c-b768-1d79fe3b6d81.png"><img width="400"  alt="afbeelding" src="https://user-images.githubusercontent.com/3975986/209735794-6f2d6dbe-c4e2-463c-b768-1d79fe3b6d81.png"></a>

- U kunt een "Verwijderingsverzoek" sturen voor de domeinnaam.
- U kunt een "Overdrachtsannulering" sturen voor de domeinnaam.
- U kunt de live status zien, het directe begin en einde van de domeinnaam
- U kunt uw subs op lijst zetten
- U kunt aanvullende veldinformatie bekijken
<hr>

## Algemene instellingen
<a href="https://user-images.githubusercontent.com/3975986/209731622-51b3cd62-1c23-4257-a30c-ce3a00d10bf3.png"><img width="400"  alt="afbeelding" src="https://user-images.githubusercontent.com/3975986/209731622-51b3cd62-1c23-4257-a30c-ce3a00d10bf3.png"></a>
<a href="https://user-images.githubusercontent.com/3975986/209732098-7dba4e20-220d-4450-be3b-0ad1f9b8083d.png"><img width="400"  alt="afbeelding" src="https://user-images.githubusercontent.com/3975986/209732098-7dba4e20-220d-4450-be3b-0ad1f9b8083d.png"></a>

- Ga naar Algemene instellingen vanuit Systeeminstellingen, selecteer het domein tabblad.
- Activeer de optie 'Klanten toestaan ​​om domeinen bij u te registreren' als u wilt dat uw klanten zelf domeinnamen kunnen registreren.
- Activeer de optie 'Klanten toestaan ​​een domein naar u over te dragen' als u wilt dat uw klanten zelf de domeinnaam kunnen overdragen.
- Activeer de optie 'Bestellingen vernieuwen inschakelen' als u wilt dat uw klanten hun domeinnaam kunnen vernieuwen voor de vervaldatum.
- Activeer de optie 'Automatisch vernieuwen bij betaling' als u wilt dat uw klanten op hetzelfde moment worden verlengd in de betalingsvernieuwing.
- Activeer de optie 'Domein Sync ingeschakeld' als u wilt dat het huidige domein regelmatig wordt gecontroleerd en gesynchroniseerd. Wij raden aan deze optie in te schakelen.
- Als u Turkse, Hebreeuwse, Arabische, Russische, enz. Domeinnamen wilt beheren, activeert u de optie 'IDN-domeinen toestaan'.
- Voer in de 'Standaardnameserver' informatie uw nameserver-informatie in.

<hr>

## Synchronisatie-instellingen

<a href="https://user-images.githubusercontent.com/3975986/209734789-de8a1692-281f-452d-900a-ab662f2aa4f6.png"><img width="400"  alt="afbeelding" src="https://user-images.githubusercontent.com/3975986/209734789-de8a1692-281f-452d-900a-ab662f2aa4f6.png"></a>
<a href="https://user-images.githubusercontent.com/3975986/209734883-a96c13d8-6275-4fb3-b500-fc3a05b6c11f.png"><img width="400"  alt="afbeelding" src="https://user-images.githubusercontent.com/3975986/209734883-a96c13d8-6275-4fb3-b500-fc3a05b6c11f.png"></a>

- Ga naar Automatiseringsinstellingen vanuit Systeeminstellingen. Ga naar het gedeelte Synchronisatie-instellingen voor domein.
- Schakel domeinsynchronisatie in,
- Activeer de optie "Volgende vervaldatum synchroniseren" als u wilt dat de einddatum wordt gewijzigd bij de update.
- Pas andere instellingen aan op basis van de intensiteit van uw systeem.

<hr>

## Fout - Gedetailleerde weergave

<a href="https://user-images.githubusercontent.com/3975986/209735161-1455e50b-e25c-4cab-9069-b1eb746b3a65.png"><img width="400"  alt="afbeelding" src="https://user-images.githubusercontent.com/3975986/209735161-1455e50b-e25c-4cab-9069-b1eb746b3a65.png"></a>
<a href="https://user-images.githubusercontent.com/3975986/209735249-54826bd6-7f03-4827-94e1-110e6929da97.png"><img width="400"  alt="afbeelding" src="https://user-images.githubusercontent.com/3975986/209735249-54826bd6-7f03-4827-94e1-110e6929da97.png"></a>

- Ga naar de sectie Modulelogboek aan de rechterkant van de optie Systeemlogboeken.
- Zoek het relevante logboek en klik op de datum
- U kunt gedetailleerd verzoek, antwoord en gefilterd antwoord bekijken.

!! We raden aan om het systeemlogboek gesloten te houden voor dagelijks gebruik wat betreft de systeemprestaties. Voor gedetailleerde informatie: <a href="https://docs.whmcs.com/System_Logs">Whmcs Logging</a>



## Probleemoplossing
- Ik heb al nieuwe aangepaste velden toegevoegd, maar ik kan ze niet zien in de instellingen.
- De cache is mogelijk verlopen. Verwijder alle bestanden in de cache-map.
<hr>

- Ik kreeg de foutmelding "Parsing WSDL: Couldn't load from..."
- Het lijkt op een netwerkprobleem. Het IP-adres van uw server is mogelijk geblokkeerd door de registry. Neem contact met ons op om het op te lossen.
