## Kurulum ve Entegrasyon rehberi

### Minimum Gereksinimler

- WHMCS 7.8 veya üstü
- PHP7.4 veya daha üstü (Önerilen 8.1) 
- PHP SOAPClient eklentisi 
- Müşteri T.C. kimlik bilgisi / Vergi Numarası/ Vergi Dairesi bilgilerini içeren customfield lar. (Opsiyonal)

## Kurulum

İndirdiğiniz klasör içindeki "modules" klasörünün Whmcs kurulu olduğu klasörün içine atın. (Örnek: /home/whmcs/public_html)
.gitinore, README.md, LICENSE dosyalarını atmayın.

<img width="453" alt="image" src="https://user-images.githubusercontent.com/3975986/209725636-b6b41019-3810-412c-8c52-616aab3760ad.png">

- System Settings Bölümüne gelin,

<hr>

<img width="1235" alt="image" src="https://user-images.githubusercontent.com/3975986/209725739-96ab634d-9cc4-486d-a258-88065ab55c0b.png">

- Domain Registrar Bölümüne gelin,

<hr>

<img width="1525" alt="image" src="https://user-images.githubusercontent.com/3975986/209726687-fbf56bd3-e78a-457c-a118-86f87b9db6f0.png">

- Girdiğiniz sayfada eğer modül dosyalarını doğru klasöre bıraktıysanız "Domain Name API" görünecektir.
- Aktive ettikten sonra tarafımızdan edindiğiniz kullanıcı adı şifreyi girin.
- Kaydettikten sonra Kullanıcı adınız ve mevcut bakiyeniz görünüyor olacaktır.
- Kullanıcılarınıza ait .tr alan adını almak için kullanılacak TC Kimlik numarası ve Vergi Numarası Bilgilerini varsa görmüş olduğunuz ayarlardan eşleştiriniz.

<hr>

## Fiyatlandırma , TLD İlişkilendirme ve Lookup Ayarları


<img width="1227" alt="image" src="https://user-images.githubusercontent.com/3975986/209727461-dd79f4a8-ed49-45cd-b305-26a5d37c6fd9.png">

-System Settings den Domain Pricing bölümüne gelin.
<hr>

<img width="1527" alt="image" src="https://user-images.githubusercontent.com/3975986/209728124-fe1aabdc-b0b0-4b7c-be2a-ff3b572a56a4.png">

- Satış yapmak istediğiniz TLD i belirleyin. (Örnek: .com.tr)
- Auto registration için "Domain Name API" seçeneğini seçin.
- EPP code Seçeneğini seçin.
- Fiyatlama için elle de girebilirsiniz Toplu Fiyat belirleme ile de yapabilirsiniz.(bir sonraki bölümde anlatılacaktır.)

<img width="809" alt="image" src="https://user-images.githubusercontent.com/3975986/209728748-51ae6bbe-018c-42a2-b85d-ab5f37cd6559.png"> 

- Domain sorgulama kaynağı olarak public Whois serverlarını kullanmak yerine domainname apiyi kullanabilirsiniz. Bunun için "Lookup provider" kısmındaki "Change" butonuna basın, domain registrar seçeneğinden sonra altta görünen "DomainNameApi" seçeneğini seçin, sonrasında hangi TLD ler için kullanılacağını seçin.


Daha fazla bilgi için : <a href="https://docs.whmcs.com/Domain_Pricing">Whmcs Domain Fiyatlama</a>
<hr>

## Yönetici Gözünden
<img width="1208" alt="image" src="https://user-images.githubusercontent.com/3975986/209735794-6f2d6dbe-c4e2-463c-b768-1d79fe3b6d81.png">

- Alan adı için "Silme isteği" gönderebilirsiniz.
- Alan adı için "Transfer İptali" gönderebilirsiniz.
- Alan adının Canlı durumunu , anlık başlangıç ve bitişini görebilirsiniz
- Subnslerini listeleyebilirsiniz
- Ek alan bilgilerini görüntüleyebilirsiniz

## Toplu Fiyat Belirleme && Otomaik Fiyatlandırma

<img width="870" alt="image" src="https://user-images.githubusercontent.com/3975986/209730191-0b796b2f-7f90-4dba-9a17-8ed2e11e11b8.png">
<img width="1370" alt="image" src="https://user-images.githubusercontent.com/3975986/209730869-5f667f65-4da7-401e-b39c-fa91d23d2682.png">

- Utilites bölümünden Registrar TLD Sync bölümüne gelin. Gelen ekrandan "DomainNameApi" seçin biraz bekleyin.
- gelecek olan ekranda sistemimizde olan tüm tldler whmcs üzerindeki olan-olmayan tüm tldlerle çapraz karşılaştırılır, kar marjı ve zararı hesaplanarak toplu halde gösterilir ve içe aktarıma imkan tanır.
Daha fazla bilgi için : <a href="https://docs.whmcs.com/Registrar_TLD_Sync">Whmcs TLD Senkronizasyonu</a>

<hr>

## Genel Ayarlar
<img width="1137" alt="image" src="https://user-images.githubusercontent.com/3975986/209731622-51b3cd62-1c23-4257-a30c-ce3a00d10bf3.png">
<img width="1523" alt="image" src="https://user-images.githubusercontent.com/3975986/209732098-7dba4e20-220d-4450-be3b-0ad1f9b8083d.png">

- System Settings bölümünden General Settings bölümüne gelin, domain tabını seçin.
- Müşterilerinizin alan adı kaydını kendisi yapabilmesini istiyorsanız 'Allow clients to register domains with you' seçeneğini aktif edin.
- Müşterilerinizin alan adı transferini kendisi yapabilmesini istiyorsanız 'Allow clients to transfer a domain to you' seçeneğini aktif edin.
- Müşterilerinizin alan adı yenilemesini vadeden önce yapabilmesini istiyorsanız 'Enable Renewal Orders' seçeneğini aktif edin.
- Müşterilerinizin ödemesi yenilemeye aynı anda yansımasını istiyorsanız 'Auto Renew on Payment' seçeneğini aktif edin.
- Mevcut alan belirli aralıklarla kontrolünü ve senkronizasyonunun yapılmasını istiyorsanız 'Domain Sync Enabled' seçeneğini aktif edin. Bu seçeneği aktif etmenizi öneriyoruz.
- Türkçe , ibranice , arapça , rusca vb alan adları yönetmek istiyorsanız 'Allow IDN Domains' seçeneğini aktif edin.
- 'Default Nameserver' bilgilerine, size ait nameserver bilgilerini giriniz.

<hr>

## Senkronizasyon Ayarları
<img width="1231" alt="image" src="https://user-images.githubusercontent.com/3975986/209734789-de8a1692-281f-452d-900a-ab662f2aa4f6.png">
<img width="1332" alt="image" src="https://user-images.githubusercontent.com/3975986/209734883-a96c13d8-6275-4fb3-b500-fc3a05b6c11f.png">

- System Settings bölümünden Automation settings bölümüne gelin. Domain Sync Settings bölümüne gelin.
- Domain seknronizasyonunu açın,
- Bitiş tarihinin güncellemede değiştirilebilmesini istiyorsanız "Sync Next Due Date" seçeneğini aktif edin.
- Diğer ayarları sisteminizin yoğunluğuna göre ayarlayın.

<hr>

## Hata - Detay Görüntüleme
<img width="1529" alt="image" src="https://user-images.githubusercontent.com/3975986/209735161-1455e50b-e25c-4cab-9069-b1eb746b3a65.png">
<img width="1532" alt="image" src="https://user-images.githubusercontent.com/3975986/209735249-54826bd6-7f03-4827-94e1-110e6929da97.png">

- System Logs seçeneğinden sağdaki Module Log bölümüne gelin.
- İlgili logu bulun ve tarihin üzerine tıklayın
- detaylı istek , cevap  ve süzülen cevabı görüntüleyebilirsiniz.

!! Sistem logu günlük kullanım için kapalı olmasını sistem performansı açısından öneriyoruz. Detaylı bilgi için : <a href="https://docs.whmcs.com/System_Logs">Whmcs Logging</a>



