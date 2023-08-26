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


## Kurulum ve Entegrasyon Kılavuzu

### Minimum Gereksinimler

- WHMCS 7.8 veya daha yüksek
- PHP7.4 veya daha yüksek (Önerilen 8.1)
- PHP SOAPClient eklentisi etkin olmalı
- Kimlik bilgileri / Vergi Numarası / Vergi Dairesi bilgisi içeren Müşteri T.C. Özel Alanları (İsteğe bağlı)

## Kurulum

!!!! Dikkat !!!!

_**Güncelleme yapıyorsanız, kurulumdan önce eski dosyalarınızı yedekleyin.**_

İndirdiğiniz dosyanın içindeki "modules" klasörünü Whmcs'nin kurulu olduğu klasöre kopyalayın. (Örnek: /home/whmcs/public_html) .gitinore, README.md, LICENSE dosyalarını silin.

<a href="https://user-images.githubusercontent.com/3975986/209725636-b6b41019-3810-412c-8c52-616aab3760ad.png"><img width="400"  alt="image" src="https://user-images.githubusercontent.com/3975986/209725636-b6b41019-3810-412c-8c52-616aab3760ad.png"></a>

- Sistem Ayarları bölümüne gidin

<hr>

<a href="https://user-images.githubusercontent.com/3975986/209725739-96ab634d-9cc4-486d-a258-88065ab55c0b.png"><img width="400"  alt="image" src="https://user-images.githubusercontent.com/3975986/209725739-96ab634d-9cc4-486d-a258-88065ab55c0b.png"></a>

- Alan Adı Kaydedici bölümüne gidin

<hr>

<a href="https://user-images.githubusercontent.com/3975986/209726687-fbf56bd3-e78a-457c-a118-86f87b9db6f0.png"><img width="400"  alt="image" src="https://user-images.githubusercontent.com/3975986/209726687-fbf56bd3-e78a-457c-a118-86f87b9db6f0.png"></a>

- Girdiğiniz sayfada modül dosyalarını doğru klasöre bıraktıysanız, "DomainNameAPI" görünecektir.
- Aktivasyonu yaptıktan sonra, tarafımızdan alınan kullanıcı adı ve şifreyi girin.
- Kaydettikten sonra, kullanıcı adınız ve mevcut bakiyeniz görünecektir.
- Kullanıcılarınızın .tr alan adını almak için kullanılacak TR Kimlik Numarası ve Vergi Numarası Bilgisini ayarlardan eşleştirin.
- USD dışında tek bir öncelikli para birimi kullanıyorsanız "TLD Senkronizasyonu için Döviz Dönüşümü" ayarını yapabilirsiniz. (Bu ayar yalnızca bölgesel TLD ithalatı için fiyat senkronizasyonu için kullanılmaktadır. Aksi takdirde değiştirmeniz gerekmez)


<a href="https://youtu.be/LEw_iMnquSo">+ Youtube bağlantısı </a>


<hr>

## Fiyatlandırma, TLD Atama ve Arama Ayarları


<a href="https://user-images.githubusercontent.com/3975986/209727461-dd79f4a8-ed49-45cd-b305-26a5d37c6fd9.png"><img width="400"  alt="image" src="https://user-images.githubusercontent.com/3975986/209727461-dd79f4a8-ed49-45cd-b305-26a5d37c6fd9.png"></a>

- Sistem Ayarlarından Alan Adı Fiyatlandırma bölümüne gidin.
<hr>

<a href="https://user-images.githubusercontent.com/3975986/209728124-fe1aabdc-b0b0-4b7c-be2a-ff3b572a56a4.png"><img width="400"  alt="image" src="https://user-images.githubusercontent.com/3975986/209728124-fe1aabdc-b0b0-4b7c-be2a-ff3b572a56a4.png"></a>

- Satmak istediğiniz TLD'yi belirleyin. (Örnek: .com.tr)
- Otomatik kayıt için "Domain Name API" seçin.
- EPP kodu seçeneğini belirleyin.
- Fiyatlandırma için manuel olarak girebilirsiniz. Ayrıca toplu bir fiyat belirleyebilirsiniz. (sonraki bölümde açıklanacaktır).

<a href="https://user-images.githubusercontent.com/3975986/209728748-51ae6bbe-018c-42a2-b85d-ab5f37cd6559.png"><img width="400"  alt="image" src="https://user-images.githubusercontent.com/3975986/209728748-51ae6bbe-018c-42a2-b85d-ab5f37cd6559.png"></a> 

- Alan sorgusu kaynağı olarak genel Whois sunucuları yerine domainname API'sini kullanabilirsiniz. Bunun için "Arama sağlayıcısı" bölümünde "Değiştir" düğmesine basın, domain kaydı seçeneğinden sonra görünen "DomainNameApi" seçeneğini seçin, ardından hangi TLD'leri kullanmak istediğinizi seçin.


Daha fazla bilgi için: <a href="https://docs.whmcs.com/Domain_Pricing">Whmcs Alan Adı Fiyatlandırma</a>
<hr>

## Toplu Fiyatlandırma ve Otomatik Fiyatlandırma

<a href="https://user-images.githubusercontent.com/3975986/209730191-0b796b2f-7f90-4dba-9a17-8ed2e11e11b8.png"><img width="400"  alt="image" src="https://user-images.githubusercontent.com/3975986/209730191-0b796b2f-7f90-4dba-9a17-8ed2e11e11b8.png"></a>

<a href="https://user-images.githubusercontent.com/3975986/209730869-5f667f65-4da7-401e-b39c-fa91d23d2682.png"><img width="400"  alt="image" src="https://user-images.githubusercontent.com/3975986/209730869-5f667f65-4da7-401e-b39c-fa91d23d2682.png"></a>

- Araçlar bölümünden Alan Adı Kaydedici TLD Senkronizasyonuna gidin. Açılan ekrandan "DomainNameApi" seçeneğini seçin ve biraz bekleyin.
- Sonraki ekranda, sistemdeki tüm TLD'ler, whmcs'deki tüm TLD'lerle karşılaştırılır, kar marjı ve zarar hesaplanır ve toplu olarak görüntülenir, böylece ithalat yapmanıza olanak sağlanır.
Daha fazla bilgi için: <a href="https://docs.whmcs.com/Registrar_TLD_Sync">Whmcs TLD Senkronizasyonu</a>


<hr>

## Yönetici Bakış Açısı
<a href="https://user-images.githubusercontent.com/3975986/209735794-6f2d6dbe-c4e2-463c-b768-1d79fe3b6d81.png"><img width="400"  alt="image" src="https://user-images.githubusercontent.com/3975986/209735794-6f2d6dbe-c4e2-463c-b768-1d79fe3b6d81.png"></a>

- Alan adı için "Silme talebi" gönderebilirsiniz.
- Alan adı için "Transfer İptali" gönderebilirsiniz.
- Canlı durumu, anlık başlama ve bitişini görebilirsiniz.
- Alt domainlerinizi listeleyebilirsiniz.
- Ek alan bilgilerini görüntüleyebilirsiniz.
<hr>

## Genel Ayarlar
<a href="https://user-images.githubusercontent.com/3975986/209731622-51b3cd62-1c23-4257-a30c-ce3a00d10bf3.png"><img width="400"  alt="image" src="https://user-images.githubusercontent.com/3975986/209731622-51b3cd62-1c23-4257-a30c-ce3a00d10bf3.png"></a>
<a href="https://user-images.githubusercontent.com/3975986/209732098-7dba4e20-220d-4450-be3b-0ad1f9b8083d.png"><img width="400"  alt="image" src="https://user-images.githubusercontent.com/3975986/209732098-7dba4e20-220d-4450-be3b-0ad1f9b8083d.png"></a>

- Sistem Ayarlarından Genel Ayarlar bölümüne gidin, alan sekmesini seçin.
- Müşterilerinizin kendi alan adlarını kaydetmelerine izin vermek istiyorsanız 'Müşterilere alan adı kaydetmelerine izin ver' seçeneğini etkinleştirin.
- Müşterilerinizin alan adını transfer etmelerine izin vermek istiyorsanız 'Müşterilere alan adı transfer etmelerine izin ver' seçeneğini etkinleştirin.
- Müşterilerinizin vadesi dolmadan alan adlarını yenilemelerine izin vermek istiyorsanız 'Yenileme Siparişlerini Etkinleştir' seçeneğini etkinleştirin.
- Müşterilerinizin ödemeyle otomatik olarak yenilemelerinin yapılmasını istiyorsanız 'Ödemeyle Otomatik Yenileme' seçeneğini etkinleştirin.
- Mevcut alan adının düzenli aralıklarla kontrol edilmesini ve senkronize edilmesini istiyorsanız 'Alan Adı Senkronizasyonu Etkin' seçeneğini etkinleştirin. Bu seçeneği etkinlemenizi öneririz.
- Türkçe, İbranice, Arapça, Rusça vb. alan adlarını yönetmek istiyorsanız 'IDN Alan Adlarına İzin Ver' seçeneğini etkinleştirin.
- 'Varsayılan Ad Sunucusu' bilgilerine kendi ad sunucusu bilgilerinizi girin.

<hr>

## Senkronizasyon Ayarları

<a href="https://user-images.githubusercontent.com/3975986/209734789-de8a1692-281f-452d-900a-ab662f2aa4f6.png"><img width="400"  alt="image" src="https://user-images.githubusercontent.com/3975986/209734789-de8a1692-281f-452d-900a-ab662f2aa4f6.png"></a>
<a href="https://user-images.githubusercontent.com/3975986/209734883-a96c13d8-6275-4fb3-b500-fc3a05b6c11f.png"><img width="400"  alt="image" src="https://user-images.githubusercontent.com/3975986/209734883-a96c13d8-6275-4fb3-b500-fc3a05b6c11f.png"></a>

- Sistem Ayarlarından Otomasyon ayarlarına gidin. Alan Adı Senkronizasyonu Ayarları bölümüne gidin.
- Alan adı senkronizasyonunu açın,
- Sonraki vadesi tarihini güncelleme isterseniz 'Sonraki Vadesini Senkronize Et' seçeneğini etkinleştirin.
- Diğer ayarları sistem yoğunluğuna göre ayarlayın.

<hr>

## Hata - Detay Görüntüleme

<a href="https://user-images.githubusercontent.com/3975986/209735161-1455e50b-e25c-4cab-9069-b1eb746b3a65.png"><img width="400"  alt="image" src="https://user-images.githubusercontent.com/3975986/209735161-1455e50b-e25c-4cab-9069-b1eb746b3a65.png"></a>
<a href="https://user-images.githubusercontent.com/3975986/209735249-54826bd6-7f03-4827-94e1-110e6929da97.png"><img width="400"  alt="image" src="https://user-images.githubusercontent.com/3975986/209735249-54826bd6-7f03-4827-94e1-110e6929da97.png"></a>

- Sistem Günlükleri seçeneğinden Sağ taraftaki Modül Günlüğü bölümüne gidin.
- İlgili günlüğü bulun ve tarihine tıklayın.
- Ayrıntılı isteği, yanıtı ve filtrelenmiş yanıtı görüntüleyebilirsiniz.

!! Sistem günlüğünün, sistem performansı açısından günlük kullanımda kapalı olmasını öneririz. Daha fazla bilgi için: <a href="https://docs.whmcs.com/System_Logs">Whmcs Günlükleme</a>



## Testler

| Test Adı       | GTLD | TRTLD |
|----------------|------|-------|
| Kaydet         | ✓    | ✓     |
| Transfer       | ✓    | ✓     | 
| Yenile         | ✓    | ✓     | 
| Ad Sunucusu    | ✓    | ✓     | 
| Registrar Kilit | ✓    | ✓     | 
| İletişim       | ✓    | ✓     | 
| EPP            | ✓    | ✓     | 
| Sil            | ✓    | ✓     | 
| Alt Ad Sunucusu | ✓    | ✓     | 
| Kullanılabilirlik | ✓  | ✓     | 
| Fiyatlandırma Senkronizasyonu | ✓ | ✓ |
| Transfer İptali | ✓    | ✓     | 
| Senkronize      | ✓    | ✓     | 
| Transfer Senkronizasyonu | ✓ | ✓ |



## Sorun Giderme
- Yeni özel alanları ekledim ancak ayarlarda göremiyorum.
- Önbellek süresi dolmuş olabilir. Önbellek klasöründeki tüm dosyaları silin.
<hr>

- "Parsing WSDL: Couldn't load from..." hatası alıyorum.
- Ağ sorunu gibi görünüyor. Sunucunuzun IP adresi kayıt tarafından engellenmiş olabilir. Sorunu çözmek için bize ulaşın.


## Dönüş ve Hata Kodları ile Açıklamaları

| Kod  | Açıklama                                        | Detay                                                                                                                                                                         |
|------|-------------------------------------------------|-------------------------------------------------------------------------------------------------------------------------------------------------------------------------------|
| 1000 | Command completed successfully                  | İşlem başarılı.                                                                                                                                                               |
| 1001 | Command completed successfully; action pending. | İşlem başarılı. Fakat işlem şu an tamamlanmak için kuyruğa alındı.                                                                                                            |
| 2003 | Required parameter missing                      | Parametre eksik hatası. Örneğin; Kontak bilgisinde telefon girişi yapılmaması.                                                                                                |
| 2105 | Object is not eligible for renewal              | Domain durumu yenilemeye müsait değil, güncelleme işlemlerine kilitlenmiştir. Durum durumu "clientupdateprohibited" olmamalı. Diğer durum durumlarından kaynaklanabilir.      |
| 2200 | Authentication error                            | Yetki hatası, güvenlik kodu hatalı veya domain başka bir kayıt firmasında bulunuyor.                                                                                          |
| 2302 | Object exists                                   | Domain adı veya name server bilgisi veritabanında mevcut. Kayıt edilemez.                                                                                                     |
| 2303 | Object does not exist                           | Domain adı veya name server bilgisi veritabanında mevcut değil. Yeni kayıt oluşturulmalı.                                                                                     |
| 2304 | Object status prohibits operation               | Domain durumu güncellemeye müsait değildir, güncelleme işlemlerine kilitlenmiştir. Durum durumu "clientupdateprohibited" olmamalı. Diğer durum durumlarından kaynaklanabilir. |

