## Installation and Integration guide

### Minimum Requirements

- WHMCS 7.8 or higher
- PHP7.4 or higher (Recommended 8.1)
- PHP SOAPClient plugin must be active.
- Customer T.C. Customfields containing identity information / Tax Number / Tax Office information. (Optional)

## Setup

!!!! Attention !!!!

_**If you are upgrading, back up your old files before installation.**_

Put the "modules" folder in the folder you downloaded into the folder where Whmcs is installed. (Example: /home/whmcs/public_html)
Do not discard .gitinore, README.md, LICENSE files.

<a href="https://user-images.githubusercontent.com/3975986/209725636-b6b41019-3810-412c-8c52-616aab3760ad.png"><img width="400"  alt="image" src="https://user-images.githubusercontent.com/3975986/209725636-b6b41019-3810-412c-8c52-616aab3760ad.png"></a>

- Go to System Settings Section,

<hr>

<a href="https://user-images.githubusercontent.com/3975986/209725739-96ab634d-9cc4-486d-a258-88065ab55c0b.png"><img width="400"  alt="image" src="https://user-images.githubusercontent.com/3975986/209725739-96ab634d-9cc4-486d-a258-88065ab55c0b.png"></a>

- Go to the Domain Registrar Section,

<hr>

<a href="https://user-images.githubusercontent.com/3975986/209726687-fbf56bd3-e78a-457c-a118-86f87b9db6f0.png"><img width="400"  alt="image" src="https://user-images.githubusercontent.com/3975986/209726687-fbf56bd3-e78a-457c-a118-86f87b9db6f0.png"></a>

- On the page you entered, if you left the module files in the correct folder, "Domain Name API" will appear.
- After activating, enter the username and password obtained by us.
- After saving, your username and current balance will be visible.
- Match the TR Identity Number and Tax Number Information to be used to obtain the .tr domain name of your users, if any, from the settings you have seen.

<a href="https://youtu.be/LEw_iMnquSo">+ Youtube link </a>


<hr>

## Pricing, TLD Attribution and Lookup Settings


<a href="https://user-images.githubusercontent.com/3975986/209727461-dd79f4a8-ed49-45cd-b305-26a5d37c6fd9.png"><img width="400"  alt="image" src="https://user-images.githubusercontent.com/3975986/209727461-dd79f4a8-ed49-45cd-b305-26a5d37c6fd9.png"></a>

-Go to Domain Pricing from System Settings.
<hr>

<a href="https://user-images.githubusercontent.com/3975986/209728124-fe1aabdc-b0b0-4b7c-be2a-ff3b572a56a4.png"><img width="400"  alt="image" src="https://user-images.githubusercontent.com/3975986/209728124-fe1aabdc-b0b0-4b7c-be2a-ff3b572a56a4.png"></a>

- Determine the TLD you want to sell. (Example: .com.tr)
- Select "Domain Name API" for auto registration.
- Select the EPP code Option.
- For pricing, you can enter manually. You can also set a Bulk Price. (will be explained in the next section).

<a href="https://user-images.githubusercontent.com/3975986/209728748-51ae6bbe-018c-42a2-b85d-ab5f37cd6559.png"><img width="400"  alt="image" src="https://user-images.githubusercontent.com/3975986/209728748-51ae6bbe-018c-42a2-b85d-ab5f37cd6559.png"></a> 

-Instead of using public Whois servers as a domain query source, you can use the domainname api. For this, press the "Change" button in the "Lookup provider" section, select the "DomainNameApi" option that appears at the bottom after the domain registry option, then choose which TLDs to use.


For more information : <a href="https://docs.whmcs.com/Domain_Pricing">Whmcs Domain Pricing</a>
<hr>

## Bulk Pricing & & Automated Pricing

<a href="https://user-images.githubusercontent.com/3975986/209730191-0b796b2f-7f90-4dba-9a17-8ed2e11e11b8.png"><img width="400"  alt="image" src="https://user-images.githubusercontent.com/3975986/209730191-0b796b2f-7f90-4dba-9a17-8ed2e11e11b8.png"></a>

<a href="https://user-images.githubusercontent.com/3975986/209730869-5f667f65-4da7-401e-b39c-fa91d23d2682.png"><img width="400"  alt="image" src="https://user-images.githubusercontent.com/3975986/209730869-5f667f65-4da7-401e-b39c-fa91d23d2682.png"></a>

- Go to Registrar TLD Sync from Utilities section. Select "DomainNameApi" from the screen that comes up, wait a bit.
- On the next screen, all tlds in our system are cross-compared with all tlds on whmcs, profit margin and loss are calculated and displayed in bulk, allowing import.
For more information : <a href="https://docs.whmcs.com/Registrar_TLD_Sync">Whmcs TLD Sync</a>

<hr>

## Manager's Perspective
<a href="https://user-images.githubusercontent.com/3975986/209735794-6f2d6dbe-c4e2-463c-b768-1d79fe3b6d81.png"><img width="400"  alt="image" src="https://user-images.githubusercontent.com/3975986/209735794-6f2d6dbe-c4e2-463c-b768-1d79fe3b6d81.png"></a>

- You can send a "Deletion request" for the domain name.
- You can send "Transfer Cancellation" for the domain name.
- You can see the live status, instant start and end of the domain name
- You can list your subs
- You can view additional field information
<hr>

## General Settings
<a href="https://user-images.githubusercontent.com/3975986/209731622-51b3cd62-1c23-4257-a30c-ce3a00d10bf3.png"><img width="400"  alt="image" src="https://user-images.githubusercontent.com/3975986/209731622-51b3cd62-1c23-4257-a30c-ce3a00d10bf3.png"></a>
<a href="https://user-images.githubusercontent.com/3975986/209732098-7dba4e20-220d-4450-be3b-0ad1f9b8083d.png"><img width="400"  alt="image" src="https://user-images.githubusercontent.com/3975986/209732098-7dba4e20-220d-4450-be3b-0ad1f9b8083d.png"></a>

- Go to General Settings from System Settings, select the domain tab.
- Activate the 'Allow clients to register domains with you' option if you want your customers to be able to register domain names themselves.
- Activate the 'Allow clients to transfer a domain to you' option if you want your customers to be able to transfer the domain name themselves.
- Activate the 'Enable Renewal Orders' option if you want your customers to be able to renew their domain name before the maturity date.
- Activate the 'Auto Renew on Payment' option if you want your customers to be reflected in the payment renewal at the same time.
- Activate the 'Domain Sync Enabled' option if you want the current domain to be checked and synchronized at regular intervals. We recommend enabling this option.
- If you want to manage Turkish, Hebrew, Arabic, Russian etc. domain names, activate the 'Allow IDN Domains' option.
- In the 'Default Nameserver' information, enter your nameserver information.

<hr>

## Sync Settings
<a href="https://user-images.githubusercontent.com/3975986/209734789-de8a1692-281f-452d-900a-ab662f2aa4f6.png"><img width="400"  alt="image" src="https://user-images.githubusercontent.com/3975986/209734789-de8a1692-281f-452d-900a-ab662f2aa4f6.png"></a>
<a href="https://user-images.githubusercontent.com/3975986/209734883-a96c13d8-6275-4fb3-b500-fc3a05b6c11f.png"><img width="400"  alt="image" src="https://user-images.githubusercontent.com/3975986/209734883-a96c13d8-6275-4fb3-b500-fc3a05b6c11f.png"></a>

- Go to Automation settings from System Settings. Go to the Domain Sync Settings section.
- Turn on domain synchronization,
- Activate the "Sync Next Due Date" option if you want the end date to be changed in the update.
- Adjust other settings according to the intensity of your system.

<hr>

## Error - Detail View
<a href="https://user-images.githubusercontent.com/3975986/209735161-1455e50b-e25c-4cab-9069-b1eb746b3a65.png"><img width="400"  alt="image" src="https://user-images.githubusercontent.com/3975986/209735161-1455e50b-e25c-4cab-9069-b1eb746b3a65.png"></a>
<a href="https://user-images.githubusercontent.com/3975986/209735249-54826bd6-7f03-4827-94e1-110e6929da97.png"><img width="400"  alt="image" src="https://user-images.githubusercontent.com/3975986/209735249-54826bd6-7f03-4827-94e1-110e6929da97.png"></a>

- Go to the Module Log section on the right from the System Logs option.
- Find the relevant log and click on the date
- You can view detailed request, reply and filtered reply.

!! We recommend that the system log is closed for daily use in terms of system performance. For detailed information : <a href="https://docs.whmcs.com/System_Logs">Whmcs Logging</a>



## Tests



| Test Name       | GTLD | TRTLD |
|----------------|------|-------|
| Register       | ✓    | ✓     |
| Transfer       | ✓    | ✓     | 
| Renew          | ✓    | ✓     | 
| Nameserver     | ✓    | ✓     | 
| RegistrarLock  | ✓    | ✓     | 
| Contact        | ✓    | ✓     | 
| EPP            | ✓    | ✓     | 
| Delete         | ✓    | ✓     | 
| SubNameserver  | ✓    | ✓     | 
| Availability   | ✓    | ✓     | 
| PricingSnyc    | ✓    | ✓     | 
| CancelTransfer | ✓    | ✓     | 
| Sync           | ✓    | ✓     | 
| TransferSync   | ✓    | ✓     | 


