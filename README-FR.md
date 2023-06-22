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


## Guide d'installation et d'intégration

### Exigences minimales

- WHMCS 7.8 ou version ultérieure
- PHP7.4 ou version ultérieure (Recommandé 8.1)
- Le plugin PHP SOAPClient doit être actif.
- Champs personnalisés du client contenant des informations d'identité / Numéro de taxe / Informations sur le bureau des impôts. (Facultatif)

## Configuration

!!!! Attention !!!!

_**Si vous effectuez une mise à niveau, sauvegardez vos anciens fichiers avant l'installation.**_

Placez le dossier "modules" dans le dossier que vous avez téléchargé à l'emplacement où Whmcs est installé. (Exemple: /home/whmcs/public_html) Supprimez les fichiers .gitignore, README.md, LICENSE.

<a href="https://user-images.githubusercontent.com/3975986/209725636-b6b41019-3810-412c-8c52-616aab3760ad.png"><img width="400" alt="image" src="https://user-images.githubusercontent.com/3975986/209725636-b6b41019-3810-412c-8c52-616aab3760ad.png"></a>

- Allez dans la section Paramètres du système.

<hr>

<a href="https://user-images.githubusercontent.com/3975986/209725739-96ab634d-9cc4-486d-a258-88065ab55c0b.png"><img width="400" alt="image" src="https://user-images.githubusercontent.com/3975986/209725739-96ab634d-9cc4-486d-a258-88065ab55c0b.png"></a>

- Allez dans la section Registrar de domaine.

<hr>

<a href="https://user-images.githubusercontent.com/3975986/209726687-fbf56bd3-e78a-457c-a118-86f87b9db6f0.png"><img width="400" alt="image" src="https://user-images.githubusercontent.com/3975986/209726687-fbf56bd3-e78a-457c-a118-86f87b9db6f0.png"></a>

- Sur la page où vous êtes entré, si vous avez laissé les fichiers du module dans le dossier correct, "DomainNameAPI" apparaîtra.
- Après l'activation, saisissez le nom d'utilisateur et le mot de passe obtenus auprès de nous.
- Après l'enregistrement, votre nom d'utilisateur et votre solde actuel seront visibles.
- Faites correspondre le numéro d'identification TR et les informations sur le numéro de taxe à utiliser pour obtenir le nom de domaine .tr de vos utilisateurs, le cas échéant, à partir des paramètres que vous avez vus.
- Si vous utilisez une seule devise principale autre que l'USD, vous pouvez définir le paramètre "Exchange Convertion For TLD Sync". (Ce paramètre est utilisé uniquement pour la synchronisation des prix pour les importations de TLD régionaux. Sinon, vous n'avez pas besoin de le modifier)


<a href="https://youtu.be/LEw_iMnquSo">+ Lien Youtube</a>


<hr>

## Paramètres de tarification, d'attribution de TLD et de recherche

<a href="https://user-images.githubusercontent.com/3975986/209727461-dd79f4a8-ed49-45cd-b305-26a5d37c6fd9.png"><img width="400" alt="image" src="https://user-images.githubusercontent.com/3975986/209727461-dd79f4a8-ed49-45cd-b305-26a5d37c6fd9.png"></a>

- Accédez à la tarification des domaines depuis les paramètres du système.
<hr>

<a href="https://user-images.githubusercontent.com/3975986/209728124-fe1aabdc-b0b0-4b7c-be2a-ff3b572a56a4.png"><img width="400" alt="image" src="https://user-images.githubusercontent.com/3975986/209728124-fe1aabdc-b0b0-4b7c-be2a-ff3b572a56a4.png"></a>

- Déterminez le TLD que vous souhaitez vendre. (Exemple: .com.tr)
- Sélectionnez "Domain Name API" pour l'enregistrement automatique.
- Sélectionnez l'option du code EPP.
- Pour les tarifs, vous pouvez les saisir manuellement. Vous pouvez également définir un tarif en vrac. (sera expliqué dans la section suivante).

<a href="https://user-images.githubusercontent.com/3975986/209728748-51ae6bbe-018c-42a2-b85d-ab5f37cd6559.png"><img width="400" alt="image" src="https://user-images.githubusercontent.com/3975986/209728748-51ae6bbe-018c-42a2-b85d-ab5f37cd6559.png"></a>

- Au lieu d'utiliser des serveurs Whois publics comme source de requête de domaine, vous pouvez utiliser l'API domainname. Pour cela, appuyez sur le bouton "Changer" dans la section "Fournisseur de recherche", sélectionnez l'option "DomainNameApi" qui apparaît en bas après l'option d'enregistrement de domaine, puis choisissez les TLD à utiliser.


Pour plus d'informations : <a href="https://docs.whmcs.com/Domain_Pricing">Tarification des domaines Whmcs</a>
<hr>

## Tarification en vrac et tarification automatisée

<a href="https://user-images.githubusercontent.com/3975986/209730191-0b796b2f-7f90-4dba-9a17-8ed2e11e11b8.png"><img width="400" alt="image" src="https://user-images.githubusercontent.com/3975986/209730191-0b796b2f-7f90-4dba-9a17-8ed2e11e11b8.png"></a>

<a href="https://user-images.githubusercontent.com/3975986/209730869-5f667f65-4da7-401e-b39c-fa91d23d2682.png"><img width="400" alt="image" src="https://user-images.githubusercontent.com/3975986/209730869-5f667f65-4da7-401e-b39c-fa91d23d2682.png"></a>

- Accédez à la synchronisation TLD du registrar depuis la section Utilitaires. Sélectionnez "DomainNameApi" à partir de l'écran qui apparaît, attendez un peu.
- Sur l'écran suivant, tous les TLD de notre système sont comparés à tous les TLD sur whmcs, la marge bénéficiaire et la perte sont calculées et affichées en vrac, permettant l'importation.
Pour plus d'informations : <a href="https://docs.whmcs.com/Registrar_TLD_Sync">Synchronisation TLD Whmcs</a>


<hr>

## Perspective du gestionnaire
<a href="https://user-images.githubusercontent.com/3975986/209735794-6f2d6dbe-c4e2-463c-b768-1d79fe3b6d81.png"><img width="400" alt="image" src="https://user-images.githubusercontent.com/3975986/209735794-6f2d6dbe-c4e2-463c-b768-1d79fe3b6d81.png"></a>

- Vous pouvez envoyer une "Demande de suppression" pour le nom de domaine.
- Vous pouvez envoyer une "Annulation de transfert" pour le nom de domaine.
- Vous pouvez voir l'état en direct, le début et la fin instantanés du nom de domaine.
- Vous pouvez répertorier vos sous-domaines.
- Vous pouvez afficher des informations supplémentaires sur les champs.
<hr>

## Paramètres généraux
<a href="https://user-images.githubusercontent.com/3975986/209731622-51b3cd62-1c23-4257-a30c-ce3a00d10bf3.png"><img width="400" alt="image" src="https://user-images.githubusercontent.com/3975986/209731622-51b3cd62-1c23-4257-a30c-ce3a00d10bf3.png"></a>
<a href="https://user-images.githubusercontent.com/3975986/209732098-7dba4e20-220d-4450-be3b-0ad1f9b8083d.png"><img width="400" alt="image" src="https://user-images.githubusercontent.com/3975986/209732098-7dba4e20-220d-4450-be3b-0ad1f9b8083d.png"></a>

- Accédez aux paramètres généraux depuis les paramètres du système, sélectionnez l'onglet domaine.
- Activez l'option 'Autoriser les clients à enregistrer des domaines avec vous' si vous souhaitez que vos clients puissent enregistrer eux-mêmes des noms de domaine.
- Activez l'option 'Autoriser les clients à transférer un domaine vers vous' si vous souhaitez que vos clients puissent transférer eux-mêmes le nom de domaine.
- Activez l'option 'Activer les commandes de renouvellement' si vous souhaitez que vos clients puissent renouveler leur nom de domaine avant la date d'échéance.
- Activez l'option 'Renouvellement automatique lors du paiement' si vous souhaitez que vos clients soient automatiquement renouvelés lors du paiement.
- Activez l'option 'Synchronisation de domaine activée' si vous souhaitez que le domaine actuel soit vérifié et synchronisé à intervalles réguliers. Nous vous recommandons d'activer cette option.
- Si vous souhaitez gérer des noms de domaine turcs, hébreux, arabes, russes, etc., activez l'option 'Autoriser les domaines IDN'.
- Dans les informations 'Serveur de noms par défaut', saisissez les informations de vos serveurs de noms.

<hr>

## Paramètres de synchronisation

<a href="https://user-images.githubusercontent.com/3975986/209734789-de8a1692-281f-452d-900a-ab662f2aa4f6.png"><img width="400" alt="image" src="https://user-images.githubusercontent.com/3975986/209734789-de8a1692-281f-452d-900a-ab662f2aa4f6.png"></a>
<a href="https://user-images.githubusercontent.com/3975986/209734883-a96c13d8-6275-4fb3-b500-fc3a05b6c11f.png"><img width="400" alt="image" src="https://user-images.githubusercontent.com/3975986/209734883-a96c13d8-6275-4fb3-b500-fc3a05b6c11f.png"></a>

- Accédez aux paramètres d'automatisation depuis les paramètres du système. Accédez à la section Paramètres de synchronisation de domaine.
- Activez la synchronisation de domaine.
- Activez l'option "Synchroniser la prochaine date d'échéance" si vous souhaitez que la date de fin soit modifiée lors de la mise à jour.
- Ajustez les autres paramètres en fonction de l'intensité de votre système.

<hr>

## Erreur - Vue détaillée

<a href="https://user-images.githubusercontent.com/3975986/209735161-1455e50b-e25c-4cab-9069-b1eb746b3a65.png"><img width="400" alt="image" src="https://user-images.githubusercontent.com/3975986/209735161-1455e50b-e25c-4cab-9069-b1eb746b3a65.png"></a>
<a href="https://user-images.githubusercontent.com/3975986/209735249-54826bd6-7f03-4827-94e1-110e6929da97.png"><img width="400" alt="image" src="https://user-images.githubusercontent.com/3975986/209735249-54826bd6-7f03-4827-94e1-110e6929da97.png"></a>

- Accédez à la section Journal du module à droite de l'option Journaux système.
- Trouvez le journal correspondant et cliquez sur la date.
- Vous pouvez consulter les demandes détaillées, les réponses et les réponses filtrées.

!! Nous recommandons de fermer le journal du système pour une utilisation quotidienne en termes de performances du système. Pour plus d'informations : <a href="https://docs.whmcs.com/System_Logs">Journalisation Whmcs</a>



## Tests

| Nom du test    | GTLD | TRTLD |
|----------------|------|-------|
| Enregistrement | ✓    | ✓     |
| Transfert      | ✓    | ✓     |
| Renouvellement | ✓    | ✓     |
| Serveur de noms| ✓    | ✓     |
| Verrou du registrar | ✓ | ✓     |
| Contact        | ✓    | ✓     |
| EPP            | ✓    | ✓     |
| Supprimer      | ✓    | ✓     |
| Sous-domaine   | ✓    | ✓     |
| Disponibilité  | ✓    | ✓     |
| Synchronisation des prix | ✓ | ✓ |
| Annuler le transfert | ✓    | ✓     |
| Synchronisation | ✓    | ✓     |
| Synchronisation du transfert | ✓ | ✓ |

## Dépannage
- J'ai déjà ajouté de nouveaux champs personnalisés mais je ne peux pas les voir dans les paramètres.
- Le cache peut être expiré. Supprimez tous les fichiers du dossier de cache.
<hr>

- J'ai obtenu une erreur "Parsing WSDL: Couldn't load from..."
- Il semble y avoir un problème de réseau. L'adresse IP de votre serveur peut être bloquée par le registre. Contactez-nous pour résoudre le problème.
