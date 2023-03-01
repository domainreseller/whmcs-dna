## Installation and Integration guide (Kurulum ve Entegrasyon rehberi)


### Minimum Requirements (Minimum Gereksinimler)

- WHMCS 7.8 or higher - (WHMCS 7.8 veya üstü)
- PHP7.4 or higher (Recommended 8.1) - (PHP7.4 veya daha üstü (Önerilen 8.1) )
- PHP SOAPClient plugin must be active. - PHP SOAPClient eklentisi aktif olmalıdır.
- Customer T.C. Customfields containing identity information / Tax Number / Tax Office information. (Optional) - Müşteri T.C. kimlik bilgisi / Vergi Numarası/ Vergi Dairesi bilgilerini içeren customfield lar. (Opsiyonal)

## Setup (Kurulum)

!!!! Attention !!!! (!!!! Dikkat !!!!)

_**If you are upgrading, back up your old files before installation. - (Eğer sürüm yükseltiyorsanız Kurulumdan önce eski dosyalarınızı yedekleyiniz.)**_

Put the "modules" folder in the folder you downloaded into the folder where Whmcs is installed. (Example: /home/whmcs/public_html)
Do not discard .gitinore, README.md, LICENSE files. - (İndirdiğiniz klasör içindeki "modules" klasörünün Whmcs kurulu olduğu klasörün içine atın. (Örnek: /home/whmcs/public_html)
.gitinore, README.md, LICENSE dosyalarını atmayın.
)

<a href="https://user-images.githubusercontent.com/3975986/209725636-b6b41019-3810-412c-8c52-616aab3760ad.png"><img width="400"  alt="image" src="https://user-images.githubusercontent.com/3975986/209725636-b6b41019-3810-412c-8c52-616aab3760ad.png"></a>

- Go to System Settings Section, - (System Settings Bölümüne gelin,)

<hr>

<a href="https://user-images.githubusercontent.com/3975986/209725739-96ab634d-9cc4-486d-a258-88065ab55c0b.png"><img width="400"  alt="image" src="https://user-images.githubusercontent.com/3975986/209725739-96ab634d-9cc4-486d-a258-88065ab55c0b.png"></a>

- Go to the Domain Registrar Section, - (Domain Registrar Bölümüne gelin,)

<hr>

<a href="https://user-images.githubusercontent.com/3975986/209726687-fbf56bd3-e78a-457c-a118-86f87b9db6f0.png"><img width="400"  alt="image" src="https://user-images.githubusercontent.com/3975986/209726687-fbf56bd3-e78a-457c-a118-86f87b9db6f0.png"></a>

- On the page you entered, if you left the module files in the correct folder, "Domain Name API" will appear. - (Girdiğiniz sayfada eğer modül dosyalarını doğru klasöre bıraktıysanız "Domain Name API" görünecektir.)
- After activating, enter the username and password obtained by us. - (Aktive ettikten sonra tarafımızdan edindiğiniz kullanıcı adı şifreyi girin.)
- After saving, your username and current balance will be visible. - (Kaydettikten sonra Kullanıcı adınız ve mevcut bakiyeniz görünüyor olacaktır.)
- Match the TR Identity Number and Tax Number Information to be used to obtain the .tr domain name of your users, if any, from the settings you have seen. - (Kullanıcılarınıza ait .tr alan adını almak için kullanılacak TC Kimlik numarası ve Vergi Numarası Bilgilerini varsa görmüş olduğunuz ayarlardan eşleştiriniz.)
- If you are using single-primary currency except USD You may set "Exchange Convertion For TLD Sync" setting. (This setting is using for only pricing sync for regional TLD imports. Otherwise you do not need to change) - (USD dışında bir tek ana para birimi kullanıyorsanız "Exchange Convertion For TLD Sync" ayarını yapabilirsiniz.)	( Bu ayar sadece bölgesel TLD importlarında fiyatlandırma senkronizasyonu için kullanılıyor. Aksi takdirde değiştirmenize gerek yoktur.)


<a href="https://youtu.be/LEw_iMnquSo">+ Youtube link </a>


<hr>

## Pricing, TLD Attribution and Lookup Settings - (Fiyatlandırma , TLD İlişkilendirme ve Lookup Ayarları)


<a href="https://user-images.githubusercontent.com/3975986/209727461-dd79f4a8-ed49-45cd-b305-26a5d37c6fd9.png"><img width="400"  alt="image" src="https://user-images.githubusercontent.com/3975986/209727461-dd79f4a8-ed49-45cd-b305-26a5d37c6fd9.png"></a>

-Go to Domain Pricing from System Settings. - (System Settings den Domain Pricing bölümüne gelin.)
<hr>

<a href="https://user-images.githubusercontent.com/3975986/209728124-fe1aabdc-b0b0-4b7c-be2a-ff3b572a56a4.png"><img width="400"  alt="image" src="https://user-images.githubusercontent.com/3975986/209728124-fe1aabdc-b0b0-4b7c-be2a-ff3b572a56a4.png"></a>

- Determine the TLD you want to sell. (Example: .com.tr) - (Satış yapmak istediğiniz TLD i belirleyin. (Örnek: .com.tr))
- Select "Domain Name API" for auto registration. - (Auto registration için "Domain Name API" seçeneğini seçin.)
- Select the EPP code Option. - (EPP code Seçeneğini seçin.)
- For pricing, you can enter manually. You can also set a Bulk Price. (will be explained in the next section). - (Fiyatlama için elle de girebilirsiniz Toplu Fiyat belirleme ile de yapabilirsiniz.(bir sonraki bölümde anlatılacaktır.))

<a href="https://user-images.githubusercontent.com/3975986/209728748-51ae6bbe-018c-42a2-b85d-ab5f37cd6559.png"><img width="400"  alt="image" src="https://user-images.githubusercontent.com/3975986/209728748-51ae6bbe-018c-42a2-b85d-ab5f37cd6559.png"></a> 

-Instead of using public Whois servers as a domain query source, you can use the domainname api. For this, press the "Change" button in the "Lookup provider" section, select the "DomainNameApi" option that appears at the bottom after the domain registry option, then choose which TLDs to use. - (Domain sorgulama kaynağı olarak public Whois serverlarını kullanmak yerine domainname apiyi kullanabilirsiniz. Bunun için "Lookup provider" kısmındaki "Change" butonuna basın, domain registrar seçeneğinden sonra altta görünen "DomainNameApi" seçeneğini seçin, sonrasında hangi TLD ler için kullanılacağını seçin.)


For more information : <a href="https://docs.whmcs.com/Domain_Pricing">Whmcs Domain Pricing</a> - (Daha fazla bilgi için : <a href="https://docs.whmcs.com/Domain_Pricing">Whmcs Domain Fiyatlama</a>)
<hr>

## Bulk Pricing & & Automated Pricing - (Toplu Fiyat Belirleme && Otomaik Fiyatlandırma)

<a href="https://user-images.githubusercontent.com/3975986/209730191-0b796b2f-7f90-4dba-9a17-8ed2e11e11b8.png"><img width="400"  alt="image" src="https://user-images.githubusercontent.com/3975986/209730191-0b796b2f-7f90-4dba-9a17-8ed2e11e11b8.png"></a>

<a href="https://user-images.githubusercontent.com/3975986/209730869-5f667f65-4da7-401e-b39c-fa91d23d2682.png"><img width="400"  alt="image" src="https://user-images.githubusercontent.com/3975986/209730869-5f667f65-4da7-401e-b39c-fa91d23d2682.png"></a>

- Go to Registrar TLD Sync from Utilities section. Select "DomainNameApi" from the screen that comes up, wait a bit. - (Utilites bölümünden Registrar TLD Sync bölümüne gelin. Gelen ekrandan "DomainNameApi" seçin biraz bekleyin.)
- On the next screen, all tlds in our system are cross-compared with all tlds on whmcs, profit margin and loss are calculated and displayed in bulk, allowing import.
For more information : <a href="https://docs.whmcs.com/Registrar_TLD_Sync">Whmcs TLD Sync</a> - (Gelecek olan ekranda sistemimizde olan tüm tldler whmcs üzerindeki olan-olmayan tüm tldlerle çapraz karşılaştırılır, kar marjı ve zararı hesaplanarak toplu halde gösterilir ve içe aktarıma imkan tanır.
Daha fazla bilgi için : <a href="https://docs.whmcs.com/Registrar_TLD_Sync">Whmcs TLD Senkronizasyonu</a>
)

<hr>

## Manager's Perspective - (Yönetici Gözünden)
<a href="https://user-images.githubusercontent.com/3975986/209735794-6f2d6dbe-c4e2-463c-b768-1d79fe3b6d81.png"><img width="400"  alt="image" src="https://user-images.githubusercontent.com/3975986/209735794-6f2d6dbe-c4e2-463c-b768-1d79fe3b6d81.png"></a>

- You can send a "Deletion request" for the domain name. - (Alan adı için "Silme isteği" gönderebilirsiniz.)
- You can send "Transfer Cancellation" for the domain name. - (Alan adı için "Transfer İptali" gönderebilirsiniz.)
- You can see the live status, instant start and end of the domain name - (Alan adının Canlı durumunu , anlık başlangıç ve bitişini görebilirsiniz)
- You can list your subs - (Subnslerini listeleyebilirsiniz)
- You can view additional field information - (Ek alan bilgilerini görüntüleyebilirsiniz)
<hr>

## General Settings - (Genel Ayarlar)
<a href="https://user-images.githubusercontent.com/3975986/209731622-51b3cd62-1c23-4257-a30c-ce3a00d10bf3.png"><img width="400"  alt="image" src="https://user-images.githubusercontent.com/3975986/209731622-51b3cd62-1c23-4257-a30c-ce3a00d10bf3.png"></a>
<a href="https://user-images.githubusercontent.com/3975986/209732098-7dba4e20-220d-4450-be3b-0ad1f9b8083d.png"><img width="400"  alt="image" src="https://user-images.githubusercontent.com/3975986/209732098-7dba4e20-220d-4450-be3b-0ad1f9b8083d.png"></a>

- Go to General Settings from System Settings, select the domain tab. - (System Settings bölümünden General Settings bölümüne gelin, domain tabını seçin.)
- Activate the 'Allow clients to register domains with you' option if you want your customers to be able to register domain names themselves. - (Müşterilerinizin alan adı kaydını kendisi yapabilmesini istiyorsanız 'Allow clients to register domains with you' seçeneğini aktif edin.)
- Activate the 'Allow clients to transfer a domain to you' option if you want your customers to be able to transfer the domain name themselves. - (Müşterilerinizin alan adı transferini kendisi yapabilmesini istiyorsanız 'Allow clients to transfer a domain to you' seçeneğini aktif edin.)
- Activate the 'Enable Renewal Orders' option if you want your customers to be able to renew their domain name before the maturity date. - (Müşterilerinizin alan adı yenilemesini vadeden önce yapabilmesini istiyorsanız 'Enable Renewal Orders' seçeneğini aktif edin.)
- Activate the 'Auto Renew on Payment' option if you want your customers to be reflected in the payment renewal at the same time. - (Müşterilerinizin ödemesi yenilemeye aynı anda yansımasını istiyorsanız 'Auto Renew on Payment' seçeneğini aktif edin.)
- Activate the 'Domain Sync Enabled' option if you want the current domain to be checked and synchronized at regular intervals. We recommend enabling this option. - (Mevcut alan belirli aralıklarla kontrolünü ve senkronizasyonunun yapılmasını istiyorsanız 'Domain Sync Enabled' seçeneğini aktif edin. Bu seçeneği aktif etmenizi öneriyoruz.)
- If you want to manage Turkish, Hebrew, Arabic, Russian etc. domain names, activate the 'Allow IDN Domains' option. - (Türkçe , ibranice , arapça , rusca vb alan adları yönetmek istiyorsanız 'Allow IDN Domains' seçeneğini aktif edin.)
- In the 'Default Nameserver' information, enter your nameserver information. - ('Default Nameserver' bilgilerine, size ait nameserver bilgilerini giriniz.)

<hr>

## Sync Settings - (Senkronizasyon Ayarları)
<a href="https://user-images.githubusercontent.com/3975986/209734789-de8a1692-281f-452d-900a-ab662f2aa4f6.png"><img width="400"  alt="image" src="https://user-images.githubusercontent.com/3975986/209734789-de8a1692-281f-452d-900a-ab662f2aa4f6.png"></a>
<a href="https://user-images.githubusercontent.com/3975986/209734883-a96c13d8-6275-4fb3-b500-fc3a05b6c11f.png"><img width="400"  alt="image" src="https://user-images.githubusercontent.com/3975986/209734883-a96c13d8-6275-4fb3-b500-fc3a05b6c11f.png"></a>

- Go to Automation settings from System Settings. Go to the Domain Sync Settings section. - (System Settings bölümünden Automation settings bölümüne gelin. Domain Sync Settings bölümüne gelin.)
- Turn on domain synchronization, - (Domain seknronizasyonunu açın,)
- Activate the "Sync Next Due Date" option if you want the end date to be changed in the update. - (Bitiş tarihinin güncellemede değiştirilebilmesini istiyorsanız "Sync Next Due Date" seçeneğini aktif edin.)
- Adjust other settings according to the intensity of your system. - (Diğer ayarları sisteminizin yoğunluğuna göre ayarlayın.)

<hr>

## Error - Detail View - (Hata - Detay Görüntüleme)
<a href="https://user-images.githubusercontent.com/3975986/209735161-1455e50b-e25c-4cab-9069-b1eb746b3a65.png"><img width="400"  alt="image" src="https://user-images.githubusercontent.com/3975986/209735161-1455e50b-e25c-4cab-9069-b1eb746b3a65.png"></a>
<a href="https://user-images.githubusercontent.com/3975986/209735249-54826bd6-7f03-4827-94e1-110e6929da97.png"><img width="400"  alt="image" src="https://user-images.githubusercontent.com/3975986/209735249-54826bd6-7f03-4827-94e1-110e6929da97.png"></a>

- Go to the Module Log section on the right from the System Logs option. - (System Logs seçeneğinden sağdaki Module Log bölümüne gelin.)
- Find the relevant log and click on the date - (İlgili logu bulun ve tarihin üzerine tıklayın)
- You can view detailed request, reply and filtered reply. - (detaylı istek , cevap  ve süzülen cevabı görüntüleyebilirsiniz.)

!! We recommend that the system log is closed for daily use in terms of system performance. For detailed information : <a href="https://docs.whmcs.com/System_Logs">Whmcs Logging</a> - (!! Sistem logu günlük kullanım için kapalı olmasını sistem performansı açısından öneriyoruz. Detaylı bilgi için : <a href="https://docs.whmcs.com/System_Logs">Whmcs Logging</a>)



## Tests - (Testler)



| Test Name      | GTLD | TRTLD |
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

-

| Test Adı       | GTLD | TRTLD |
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


## Troubleshooting - (Sorun Giderme)
- I alredy added new customfields but in settings i cannot see them. - (Yeni customfield ekledim fakat ayarlarda göremiyorum.)

- The cache may have expired. Delete all files in cache folder. - (Önbellek süresi dolmamış olabilir. Cache klasöründeki tüm dosyaları silin.)
<hr>

- I got error "Parsing WSDL: Couldn't load from..." - (Hata "Parsing WSDL: Couldn't load from..." )

- Looks like network problem. Your Server's Ip address might be blocked by registry. Reach us for solving.  - (Muhtemelen ağ ve IP problemleri. Sunucunuzun IP adresi kayıt kuruluşu tarafından engellenmiş olabilir. Çözüm için bizimle iletişime geçin.)
