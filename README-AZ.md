
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


## Yükləmə və Enteqrasiya Təlimatı

### Minimum Tələblər

- WHMCS 7.8 və ya daha yüksək
- PHP7.4 və ya daha yüksək (Tövsiyə olunan 8.1)
- PHP SOAPClient plugini aktiv olmalıdır.
- Müştəri T.C. şəxsiyyət məlumatı / Vergi nömrəsi / Vergi idarəsi məlumatı əks etdirən Xüsusi Sahələr. (İsteğe bağlı)

## Quraşdırma

!!!! Diqqət !!!!

_**Təkmilləşdirmə edirsinizsə, quraşdırmadan əvvəl köhnə fayllarınızın ehtiyat nüsxəsini alın.**_

Yüklədiyiniz qovluğa "modules" qovluğunu WHMCS-in quraşdırıldığı qovluğa yerləşdirin. (Nümunə: /home/whmcs/public_html) .gitinore, README.md, LICENSE fayllarını atın.


<a href="https://user-images.githubusercontent.com/3975986/209725636-b6b41019-3810-412c-8c52-616aab3760ad.png"><img width="400"  alt="image" src="https://user-images.githubusercontent.com/3975986/209725636-b6b41019-3810-412c-8c52-616aab3760ad.png"></a>

- Sistem Ayarları bölməsinə keçin

<hr>

<a href="https://user-images.githubusercontent.com/3975986/209725739-96ab634d-9cc4-486d-a258-88065ab55c0b.png"><img width="400"  alt="image" src="https://user-images.githubusercontent.com/3975986/209725739-96ab634d-9cc4-486d-a258-88065ab55c0b.png"></a>

- Alan Adı Qeydiyyatçısı bölməsinə keçin

<hr>

<a href="https://user-images.githubusercontent.com/3975986/209726687-fbf56bd3-e78a-457c-a118-86f87b9db6f0.png"><img width="400"  alt="image" src="https://user-images.githubusercontent.com/3975986/209726687-fbf56bd3-e78a-457c-a118-86f87b9db6f0.png"></a>

- Daxil olduğunuz səhifədə əgər modul fayllarını doğru qovluqda qoymuşsanız, "DomainNameAPI" görünəcəkdir.
- Aktivləşdirdikdən sonra, tərəfimizdən əldə edilmiş istifadəçi adını və şifrəni daxil edin.
- Yaddaşa aldıqdan sonra, istifadəçi adınız və cari balansınız görünür olacaq.
- Köhnər müştərilərinizin .tr domen adını əldə etmək üçün istifadə ediləcək TR Şəxsiyyət Vəsiqəsi Nömrəsi və Vergi Nömrəsi Məlumatını quraşdırmış olduğunuz parametrlərlə uyğunlaşdırın.
- Əgər USD-dan başqa bir əsas valyuta istifadə edirsinizsə, "TLD Sync üçün Exchange Konvertasiya" parametrini təyin edə bilərsiniz. (Bu parametr yalnız bölgəvi TLD girişləri üçün qiymət sinxronizasiyası üçün istifadə edilir. Başqa tənzimləməyə ehtiyacınız yoxdur)


<a href="https://youtu.be/LEw_iMnquSo">+ Youtube link </a>


<hr>

## Qiymətləndirmə, TLD Təyinatı və Axtarış Parametrləri


<a href="https://user-images.githubusercontent.com/3975986/209727461-dd79f4a8-ed49-45cd-b305-26a5d37c6fd9.png"><img width="400"  alt="image" src="https://user-images.githubusercontent.com/3975986/209727461-dd79f4a8-ed49-45cd-b305-26a5d37c6fd9.png"></a>

- Sistem Ayarlarından Alan Adı Qiymətləndirməsinə keçin.
<hr>

<a href="https://user-images.githubusercontent.com/3975986/209728124-fe1aabdc-b0b0-4b7c-be2a-ff3b572a56a4.png"><img width="400"  alt="image" src="https://user-images.githubusercontent.com/3975986/209728124-fe1aabdc-b0b0-4b7c-be2a-ff3b572a56a4.png"></a>

- Satmaq istədiyiniz TLD-ni təyin edin. (Nümunə: .com.tr)
- Avtomatik qeydiyyat üçün "Domain Adı API"-ni seçin.
- EPP kodu Variantını seçin.
- Qiymətləndirmə üçün əllə daxil edə bilərsiniz. Həmçinin Toptan Qiymət təyin edə bilərsiniz. (növbəti bölmədə izah ediləcək).

<a href="https://user-images.githubusercontent.com/3975986/209728748-51ae6bbe-018c-42a2-b85d-ab5f37cd6559.png"><img width="400"  alt="image" src="https://user-images.githubusercontent.com/3975986/209728748-51ae6bbe-018c-42a2-b85d-ab5f37cd6559.png"></a> 

- Bölgəvi TLD sorğu mənbəyi olaraq ictimai Whois serverlərindən istifadə etmək yerinə, domainname api istifadə edə bilərsiniz. Bunun üçün, "Lookup provider" bölməsində "Change" düyməsinə basın, domain qeydiyyatı seçimindən sonra görünən "DomainNameApi" seçimini seçin və istifadə edəcəyiniz TLD-ləri seçin.


Daha ətraflı məlumat üçün : <a href="https://docs.whmcs.com/Domain_Pricing">Whmcs Domain Qiymətləndirməsi</a>
<hr>

## Toptan Qiymətləndirmə və Avtomatik Qiymətləndirmə 

<a href="https://user-images.githubusercontent.com/3975986/209730191-0b796b2f-7f90-4dba-9a17-8ed2e11e11b8.png"><img width="400"  alt="image" src="https://user-images.githubusercontent.com/3975986/209730191-0b796b2f-7f90-4dba-9a17-8ed2e11e11b8.png"></a>

<a href="https://user-images.githubusercontent.com/3975986/209730869-5f667f65-4da7-401e-b39c-fa91d23d2682.png"><img width="400"  alt="image" src="https://user-images.githubusercontent.com/3975986/209730869-5f667f65-4da7-401e-b39c-fa91d23d2682.png"></a>

- Utiliteler bölməsindən Qeydiyyatçı TLD Sync bölməsinə keçin. Açılan ekranda "DomainNameApi"-ni seçin, bir az gözləyin.
- Növbəti ekranlarda sistemimizdəki bütün tld-lər WHMCS-dəki bütün tld-lərlə müqayisə olunur, mənfəət marji və zərər hesablanır və toplu olaraq göstərilir, importa imkan verir.
Daha ətraflı məlumat üçün : <a href="https://docs.whmcs.com/Registrar_TLD_Sync">Whmcs TLD Sync</a>


<hr>

## İnzibatçının Bakışı 
<a href="https://user-images.githubusercontent.com/3975986/209735794-6f2d6dbe-c4e2-463c-b768-1d79fe3b6d81.png"><img width="400"  alt="image" src="https://user-images.githubusercontent.com/3975986/209735794-6f2d6dbe-c4e2-463c-b768-1d79fe3b6d81.png"></a>

- Domen adı üçün "Silmə tələbi" göndərə bilərsiniz.
- Domen adı üçün "Transfer İmtinası" göndərə bilərsiniz.
- Domen adının canlı statusunu, anlık başlama və bitməsini görmək olar.
- Sublarınızı siyahıya ala bilərsiniz.
- Əlavə sahə məlumatlarını görə bilərsiniz.
<hr>

## Ümumi Tənzimləmələr 
<a href="https://user-images.githubusercontent.com/3975986/209731622-51b3cd62-1c23-4257-a30c-ce3a00d10bf3.png"><img width="400"  alt="image" src="https://user-images.githubusercontent.com/3975986/209731622-51b3cd62-1c23-4257-a30c-ce3a00d10bf3.png"></a>
<a href="https://user-images.githubusercontent.com/3975986/209732098-7dba4e20-220d-4450-be3b-0ad1f9b8083d.png"><img width="400"  alt="image" src="https://user-images.githubusercontent.com/3975986/209732098-7dba4e20-220d-4450-be3b-0ad1f9b8083d.png"></a>

- Sistem Ayarlarından Genel Tənzimləmələrə keçin, domen sekmesini seçin.
- Müştərilərinizin öz domen adlarını qeydiyyat etmələrinə imkan vermək istəyirsinizsə, 'Müştərilərə öz domenlərini qeydiyyat etməyə imkan ver' seçimini aktivləşdirin.
- Müştərilərinizin domen adını öz başına transfer etmək imkanına sahib olmalarını istəyirsinizsə, 'Müştərilərin domen adını öz başına transfer etməyə imkan ver' seçimini aktivləşdirin.
- Müştərilərinizin dolayı tarixdən əvvəl domen adlarını yeniləməyə imkan vermək istəyirsinizsə, 'Yeniləmə Sifarişləri Aktivdir' seçimini aktivləşdirin.
- Müştərilərinizin ödənişlərlə birlikdə yeniləmələrdən avtomatik olaraq istifadə etmək istəyirsinizsə, 'Ödənişlərlə Avtomatik Yenilə' seçimini aktivləşdirin.
- Cari domenin düzgün aralıqlarda yoxlanılıb sinxronizasiya edilməsini istəyirsinizsə, 'Domen Sinxronizasiyası Aktivdir' seçimini aktivləşdirin. Bu seçimi aktivləşdirməyi tövsiyə edirik.
- Türk, İvrit, Ərəb və Rus dillərindəki domen adlarını idarə etmək istəyirsinizsə, 'IDN Domenlərə İcazə Ver' seçimini aktivləşdirin.
- 'Default Nameserver' məlumatında nameserver məlumatınızı daxil edin.

<hr>

## Sinxronizasiya Ayarları

<a href="https://user-images.githubusercontent.com/3975986/209734789-de8a1692-281f-452d-900a-ab662f2aa4f6.png"><img width="400"  alt="image" src="https://user-images.githubusercontent.com/3975986/209734789-de8a1692-281f-452d-900a-ab662f2aa4f6.png"></a>
<a href="https://user-images.githubusercontent.com/3975986/209734883-a96c13d8-6275-4fb3-b500-fc3a05b6c11f.png"><img width="400"  alt="image" src="https://user-images.githubusercontent.com/3975986/209734883-a96c13d8-6275-4fb3-b500-fc3a05b6c11f.png"></a>

- Sistem Ayarlarından Avtomatlaşdırma tənzimləmələrinə keçin. Domain Sinxronizasiya Ayarları bölməsinə keçin.
- Domen sinxronizasiyasını aktivləşdirin,
- Yeni Son Bitmə Tarixinin dəyişdirilməsini istəyirsinizsə, 'Son Bitmə Tarixini Sinxronla' seçimini aktivləşdirin.
- Digər tənzimləmələri sisteminizin intensivliyinə uyğun şəkildə tənzimləyin.

<hr>

## Səhv - Detal Görünüşü

<a href="https://user-images.githubusercontent.com/3975986/209735161-1455e50b-e25c-4cab-9069-b1eb746b3a65.png"><img width="400"  alt="image" src="https://user-images.githubusercontent.com/3975986/209735161-1455e50b-e25c-4cab-9069-b1eb746b3a65.png"></a>
<a href="https://user-images.githubusercontent.com/3975986/209735249-54826bd6-7f03-4827-94e1-110e6929da97.png"><img width="400"  alt="image" src="https://user-images.githubusercontent.com/3975986/209735249-54826bd6-7f03-4827-94e1-110e6929da97.png"></a>

- Sistem Günlükləri seçimindən Modul Günlüyünə keçin.
- Əlaqəli günlüyü tapın və tarixə basın
- Tələbə, cavab və filtrlənmiş cavabı ətraflı şəkildə görə bilərsiniz.

!! Sistem günlüyünün günlük istifadəsi üçün sistem performansı üçün bağlı olanın bağlı olması tövsiyə olunur. Ətraflı məlumat üçün : <a href="https://docs.whmcs.com/System_Logs">Whmcs Qeydlər</a> 



## Testlər 



| Test Adı       | GTLD | TRTLD |
|----------------|------|-------|
| Qeydiyyat      | ✓    | ✓     |
| Transfer       | ✓    | ✓     | 
| Yeniləmə       | ✓    | ✓     | 
| Nameserver     | ✓    | ✓     | 
| Qeydiyyatçı Blok | ✓    | ✓     | 
| Əlaqə           | ✓    | ✓     | 
| EPP            | ✓    | ✓     | 
| Silmə          | ✓    | ✓     | 
| AltNameserver  | ✓    | ✓     | 
| Əlçatanlıq     | ✓    | ✓     | 
| Qiymət Sinxronizasiyası  | ✓    | ✓     | 
| Transfer İmtina | ✓    | ✓     | 
| Sinxronizasiya | ✓    | ✓     | 
| Transfer Sinxronizasiya | ✓    | ✓     | 


## Problem həllində Yardım 
- Artıq yeni xüsusi sahələr əlavə etdim, ancaq tənzimləmələrdə onları görmürəm.
- Önbellek bitmiş ola bilər. Önbellek qovluğundakı bütün faylları silin.
<hr>

- "Parsing WSDL: Couldn't load from..." səhvi alıram.
- Şəbəkə problemi kimi görünür. Serverinizin İP ünvanı qeydiyyat tərəfindən bloklana bilər. Məsələnin həll edilməsi üçün bizimlə əlaqə saxlayın.
