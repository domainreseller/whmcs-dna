<div align="center">  
  <a href="README.md"   >   TR <img style="padding-top: 8px" src="https://raw.githubusercontent.com/yammadev/flag-icons/master/png/TR.png" alt="TR" height="20" /></a>  
  <a href="README-EN.md"> | EN <img style="padding-top: 8px" src="https://raw.githubusercontent.com/yammadev/flag-icons/master/png/US.png" alt="EN" height="20" /></a>  
  <a href="README-CN.md"> | CN <img style="padding-top: 8px" src="https://raw.githubusercontent.com/yammadev/flag-icons/master/png/CN.png" alt="CN" height="20" /></a>  
  <a href="README-AZ.md"> | AZ <img style="padding-top: 8px" src="https://raw.githubusercontent.com/yammadev/flag-icons/master/png/AZ.png" alt="AZ" height="20" /></a>  
  <a href="README-DE.md"> | DE <img style="padding-top: 8px" src="https://raw.githubusercontent.com/yammadev/flag-icons/master/png/DE.png" alt="DE" height="20" /></a>  
  <a href="README-FR.md"> | FR <img style="padding-top: 8px" src="https://raw.githubusercontent.com/yammadev/flag-icons/master/png/FR.png" alt="FR" height="20" /></a>  
  <a href="README-AR.md"> | SA <img style="padding-top: 8px" src="https://raw.githubusercontent.com/yammadev/flag-icons/master/png/SA.png" alt="AR" height="20" /></a>  
  <a href="README-NL.md"> | NL <img style="padding-top: 8px" src="https://raw.githubusercontent.com/yammadev/flag-icons/master/png/NL.png" alt="NL" height="20" /></a>  
  <a href="README-FA.md"> | FA <img style="padding-top: 8px" src="https://raw.githubusercontent.com/yammadev/flag-icons/master/png/IR.png" alt="FA" height="20" /></a>  
</div>

---

## 📦 دانلود — همیشه از بخش Releases استفاده کنید!

⬇️ **جدیدترین نسخهٔ تست‌شده را از اینجا دریافت کنید: https://github.com/domainreseller/whmcs-dna/releases/latest**

> ⚠️ از دکمهٔ سبز رنگ **Code → Download ZIP** استفاده **نکنید** — آن نسخهٔ خام شاخهٔ توسعه را دانلود می‌کند. بسته‌های انتشار، نسخه‌بندی، تست و آمادهٔ استفاده در محیط تولید هستند.

---

## راهنمای نصب و یکپارچه‌سازی

### حداقل نیازمندی‌ها

- WHMCS 7.8 یا بالاتر
- PHP 7.4 یا بالاتر (نسخهٔ ۸.۱ توصیه می‌شود)
- افزونهٔ PHP SOAPClient باید فعال باشد.
- فیلدهای سفارشی مشتری شامل اطلاعات هویتی / شماره مالیاتی / اطلاعات ادارهٔ مالیات (اختیاری)

---

## نصب

!!!! توجه !!!!

_**اگر در حال ارتقا هستید، قبل از نصب از فایل‌های قدیمی خود پشتیبان بگیرید.**_

پوشهٔ «modules» را از فایل دانلود شده، در پوشه‌ای که WHMCS نصب شده قرار دهید. (مثال: /home/whmcs/public_html) فایل‌های .gitinore، README.md، LICENSE را کنار بگذارید.

<a href="https://user-images.githubusercontent.com/3975986/209725636-b6b41019-3810-412c-8c52-616aab3760ad.png"><img width="400" alt="image" src="https://user-images.githubusercontent.com/3975986/209725636-b6b41019-3810-412c-8c52-616aab3760ad.png"></a>

- به بخش تنظیمات سیستم بروید.

<hr>

<a href="https://user-images.githubusercontent.com/3975986/209725739-96ab634d-9cc4-486d-a258-88065ab55c0b.png"><img width="400" alt="image" src="https://user-images.githubusercontent.com/3975986/209725739-96ab634d-9cc4-486d-a258-88065ab55c0b.png"></a>

- به بخش ثبت‌کنندهٔ دامنه (Domain Registrar) بروید.

<hr>

<a href="https://user-images.githubusercontent.com/3975986/209726687-fbf56bd3-e78a-457c-a118-86f87b9db6f0.png"><img width="400" alt="image" src="https://user-images.githubusercontent.com/3975986/209726687-fbf56bd3-e78a-457c-a118-86f87b9db6f0.png"></a>

- در صفحه‌ای که وارد شدید، اگر فایل‌های افزونه را در پوشهٔ صحیح قرار داده باشید، گزینهٔ "DomainNameAPI" ظاهر خواهد شد.
- پس از فعال‌سازی، نام کاربری و رمز عبور دریافت‌شده از ما را وارد کنید.
- پس از ذخیره‌سازی، نام کاربری و موجودی فعلی شما قابل مشاهده خواهد بود.
- اگر فیلدهای شمارهٔ شناسایی ملی و شمارهٔ مالیاتی را برای دریافت دامنهٔ .tr کاربران خود تنظیم کرده‌اید، آن‌ها را در تنظیمات مشاهده شده، تطبیق دهید.
- اگر از ارز اصلی غیر از USD استفاده می‌کنید، می‌توانید تنظیمات «Exchange Convertion For TLD Sync» را تعیین کنید. (این تنظیم فقط برای همگام‌سازی قیمت‌ها برای واردات TLDهای منطقه‌ای استفاده می‌شود. در غیر این صورت نیازی به تغییر ندارید.)

---

## 🔑 اعتبارنامهٔ API — نام کاربری/رمز عبور یا شناسهٔ نماینده/کلید API؟

هر دو روش پشتیبانی می‌شوند — آن‌ها را در همان دو فیلد افزونه وارد کنید؛ افزونه به‌طور خودکار تشخیص می‌دهد از کدام API استفاده کند:

| وضعیت شما | فیلد "Username" | فیلد "Password" | API مورد استفاده |
|---|---|---|---|
| **اعتبارنامهٔ پنل جدید** (توصیه‌شده) | شناسهٔ نماینده — UUID مانند `xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx` | کلید API | REST |
| **اعتبارنامهٔ قدیمی** | نام کاربری API | رمز عبور API | SOAP |

> 💡 **شناسهٔ نماینده** و **کلید API** خود را در پنل DomainNameAPI و در بخش **تنظیمات API** پیدا کنید.
> ⚠️ این‌ها **اعتبارنامهٔ API** هستند — ایمیل و رمز عبور ورود به پنل شما در اینجا کار نخواهد کرد.

هیچ تنظیم اضافی لازم نیست — اگر فیلد نام کاربری شامل یک UUID باشد، افزونه از API مدرن REST استفاده می‌کند، در غیر این صورت از SOAP کلاسیک.

<a href="https://youtu.be/LEw_iMnquSo">+ لینک یوتیوب </a>

<hr>

## تنظیمات قیمت‌گذاری، نسبت‌دهی TLD و جستجو

<a href="https://user-images.githubusercontent.com/3975986/209727461-dd79f4a8-ed49-45cd-b305-26a5d37c6fd9.png"><img width="400" alt="image" src="https://user-images.githubusercontent.com/3975986/209727461-dd79f4a8-ed49-45cd-b305-26a5d37c6fd9.png"></a>

- از تنظیمات سیستم به بخش قیمت‌گذاری دامنه (Domain Pricing) بروید.

<hr>

<a href="https://user-images.githubusercontent.com/3975986/209728124-fe1aabdc-b0b0-4b7c-be2a-ff3b572a56a4.png"><img width="400" alt="image" src="https://user-images.githubusercontent.com/3975986/209728124-fe1aabdc-b0b0-4b7c-be2a-ff3b572a56a4.png"></a>

- TLD مورد نظر برای فروش را تعیین کنید. (مثال: .com.tr)
- برای ثبت‌نام خودکار، "Domain Name API" را انتخاب کنید.
- گزینهٔ EPP code را انتخاب کنید.
- برای قیمت‌گذاری، می‌توانید به‌صورت دستی وارد کنید. همچنین می‌توانید قیمت عمده تعیین کنید. (در بخش بعدی توضیح داده خواهد شد)

<a href="https://user-images.githubusercontent.com/3975986/209728748-51ae6bbe-018c-42a2-b85d-ab5f37cd6559.png"><img width="400" alt="image" src="https://user-images.githubusercontent.com/3975986/209728748-51ae6bbe-018c-42a2-b85d-ab5f37cd6559.png"></a>

- به جای استفاده از سرورهای عمومی Whois به عنوان منبع جستجوی دامنه، می‌توانید از API دامنه استفاده کنید. برای این کار، در بخش "Lookup provider" دکمهٔ "Change" را فشار دهید، گزینهٔ "DomainNameApi" را که بعد از گزینهٔ ثبت‌کنندهٔ دامنه در پایین ظاهر می‌شود، انتخاب کنید، سپس انتخاب کنید از کدام TLDها استفاده شود.

برای اطلاعات بیشتر: <a href="https://docs.whmcs.com/Domain_Pricing">مستندات قیمت‌گذاری دامنه WHMCS</a>

<hr>

## قیمت‌گذاری عمده و قیمت‌گذاری خودکار

<a href="https://user-images.githubusercontent.com/3975986/209730191-0b796b2f-7f90-4dba-9a17-8ed2e11e11b8.png"><img width="400" alt="image" src="https://user-images.githubusercontent.com/3975986/209730191-0b796b2f-7f90-4dba-9a17-8ed2e11e11b8.png"></a>

<a href="https://user-images.githubusercontent.com/3975986/209730869-5f667f65-4da7-401e-b39c-fa91d23d2682.png"><img width="400" alt="image" src="https://user-images.githubusercontent.com/3975986/209730869-5f667f65-4da7-401e-b39c-fa91d23d2682.png"></a>

- از بخش Utilities به Registrar TLD Sync بروید. از صفحهٔ نمایش داده شده، "DomainNameApi" را انتخاب کنید، کمی منتظر بمانید.
- در صفحهٔ بعد، تمام TLDهای موجود در سیستم ما با تمام TLDهای موجود در WHMCS مقایسه می‌شوند، حاشیهٔ سود و زیان محاسبه و به‌صورت عمده نمایش داده می‌شوند و امکان وارد کردن فراهم می‌شود.

برای اطلاعات بیشتر: <a href="https://docs.whmcs.com/Registrar_TLD_Sync">مستندات همگام‌سازی TLD WHMCS</a>

<hr>

## دیدگاه مدیر

<a href="https://user-images.githubusercontent.com/3975986/209735794-6f2d6dbe-c4e2-463c-b768-1d79fe3b6d81.png"><img width="400" alt="image" src="https://user-images.githubusercontent.com/3975986/209735794-6f2d6dbe-c4e2-463c-b768-1d79fe3b6d81.png"></a>

- می‌توانید درخواست "حذف" برای نام دامنه ارسال کنید.
- می‌توانید "لغو انتقال" برای نام دامنه ارسال کنید.
- می‌توانید وضعیت زنده، زمان شروع و پایان لحظه‌ای نام دامنه را مشاهده کنید.
- می‌توانید زیردامنه‌های خود را لیست کنید.
- می‌توانید اطلاعات فیلدهای اضافی را مشاهده کنید.

<hr>

## تنظیمات عمومی

<a href="https://user-images.githubusercontent.com/3975986/209731622-51b3cd62-1c23-4257-a30c-ce3a00d10bf3.png"><img width="400" alt="image" src="https://user-images.githubusercontent.com/3975986/209731622-51b3cd62-1c23-4257-a30c-ce3a00d10bf3.png"></a>
<a href="https://user-images.githubusercontent.com/3975986/209732098-7dba4e20-220d-4450-be3b-0ad1f9b8083d.png"><img width="400" alt="image" src="https://user-images.githubusercontent.com/3975986/209732098-7dba4e20-220d-4450-be3b-0ad1f9b8083d.png"></a>

- از تنظیمات سیستم به تنظیمات عمومی بروید، برگهٔ دامنه را انتخاب کنید.
- اگر می‌خواهید مشتریان شما بتوانند خودشان نام دامنه ثبت کنند، گزینهٔ 'Allow clients to register domains with you' را فعال کنید.
- اگر می‌خواهید مشتریان شما بتوانند خودشان دامنه را انتقال دهند، گزینهٔ 'Allow clients to transfer a domain to you' را فعال کنید.
- اگر می‌خواهید مشتریان شما بتوانند قبل از تاریخ سررسید، دامنه خود را تمدید کنند، گزینهٔ 'Enable Renewal Orders' را فعال کنید.
- اگر می‌خواهید تمدید همزمان با پرداخت در صورت‌حساب منعکس شود، گزینهٔ 'Auto Renew on Payment' را فعال کنید.
- اگر می‌خواهید دامنهٔ فعلی در فواصل زمانی معین بررسی و همگام‌سازی شود، گزینهٔ 'Domain Sync Enabled' را فعال کنید. توصیه می‌کنیم این گزینه را فعال کنید.
- اگر می‌خواهید نام‌های دامنهٔ ترکی، عبری، عربی، روسی و غیره را مدیریت کنید، گزینهٔ 'Allow IDN Domains' را فعال کنید.
- در اطلاعات 'Default Nameserver'، اطلاعات نام‌سرور خود را وارد کنید.

<hr>

## تنظیمات همگام‌سازی

<a href="https://user-images.githubusercontent.com/3975986/209734789-de8a1692-281f-452d-900a-ab662f2aa4f6.png"><img width="400" alt="image" src="https://user-images.githubusercontent.com/3975986/209734789-de8a1692-281f-452d-900a-ab662f2aa4f6.png"></a>
<a href="https://user-images.githubusercontent.com/3975986/209734883-a96c13d8-6275-4fb3-b500-fc3a05b6c11f.png"><img width="400" alt="image" src="https://user-images.githubusercontent.com/3975986/209734883-a96c13d8-6275-4fb3-b500-fc3a05b6c11f.png"></a>

- از تنظیمات سیستم به تنظیمات اتوماسیون بروید. به بخش تنظیمات همگام‌سازی دامنه بروید.
- همگام‌سازی دامنه را روشن کنید،
- اگر می‌خواهید تاریخ پایان در به‌روزرسانی تغییر کند، گزینهٔ "Sync Next Due Date" را فعال کنید.
- سایر تنظیمات را با توجه به ترافیک سیستم خود تنظیم کنید.

<hr>

## مشاهدهٔ خطا - جزئیات

<a href="https://user-images.githubusercontent.com/3975986/209735161-1455e50b-e25c-4cab-9069-b1eb746b3a65.png"><img width="400" alt="image" src="https://user-images.githubusercontent.com/3975986/209735161-1455e50b-e25c-4cab-9069-b1eb746b3a65.png"></a>
<a href="https://user-images.githubusercontent.com/3975986/209735249-54826bd6-7f03-4827-94e1-110e6929da97.png"><img width="400" alt="image" src="https://user-images.githubusercontent.com/3975986/209735249-54826bd6-7f03-4827-94e1-110e6929da97.png"></a>

- از گزینهٔ System Logs به بخش Module Log در سمت راست بروید.
- لاگ مربوطه را پیدا کنید و روی تاریخ کلیک کنید.
- می‌توانید درخواست، پاسخ و پاسخ فیلترشدهٔ دقیق را مشاهده کنید.

!! توصیه می‌کنیم برای استفادهٔ روزانه، به دلیل عملکرد سیستم، لاگ سیستم بسته باشد. برای اطلاعات دقیق: <a href="https://docs.whmcs.com/System_Logs">مستندات لاگ‌گیری WHMCS</a>

---

## تست‌ها

| نام تست | GTLD | TRTLD |
|---|---|---|
| ثبت‌نام | ✓ | ✓ |
| انتقال | ✓ | ✓ |
| تمدید | ✓ | ✓ |
| نام‌سرور | ✓ | ✓ |
| قفل ثبت‌کننده | ✓ | ✓ |
| مخاطب | ✓ | ✓ |
| EPP | ✓ | ✓ |
| حذف | ✓ | ✓ |
| زیرنام‌سرور | ✓ | ✓ |
| در دسترس بودن | ✓ | ✓ |
| همگام‌سازی قیمت | ✓ | ✓ |
| لغو انتقال | ✓ | ✓ |
| همگام‌سازی | ✓ | ✓ |
| همگام‌سازی انتقال | ✓ | ✓ |

---

## عیب‌یابی

- من فیلدهای سفارشی جدید اضافه کردم اما در تنظیمات نمی‌توانم آن‌ها را ببینم.
- ممکن است کش منقضی شده باشد. تمام فایل‌های موجود در پوشهٔ cache را حذف کنید.

<hr>

- خطای "Parsing WSDL: Couldn't load from..." را دریافت کردم.
- به نظر مشکل شبکه است. ممکن است آدرس IP سرور شما توسط رجیستری مسدود شده باشد. برای حل مشکل با ما تماس بگیرید.

---

## کدهای بازگشت و خطا به همراه توضیحات

| کد | توضیحات | جزئیات |
|---|---|---|
| ۱۰۰۰ | دستور با موفقیت انجام شد | دستور با موفقیت انجام شد |
| ۱۰۰۱ | دستور با موفقیت انجام شد؛ اقدام در انتظار | دستور با موفقیت انجام شد؛ اقدام در انتظار |
| ۲۰۰۳ | پارامتر ضروری وجود ندارد | پارامتر ضروری وجود ندارد. به عنوان مثال: شماره تلفن در اطلاعات تماس وجود ندارد |
| ۲۱۰۵ | شیء واجد شرایط تمدید نیست | شیء واجد شرایط تمدید نیست، اقدامات به‌روزرسانی قفل شده‌اند. وضعیت نباید "clientupdateprohibited" باشد. ممکن است به دلیل سایر شرایط وضعیت باشد. |
| ۲۲۰۰ | خطای احراز هویت | خطای احراز هویت، کد مجوز نادرست است، یا دامنه با ثبت‌کنندهٔ دیگری ثبت شده است. |
| ۲۳۰۲ | شیء وجود دارد | نام دامنه یا اطلاعات نام‌سرور قبلاً در پایگاه داده وجود دارد. قابل ثبت نیست. |
| ۲۳۰۳ | شیء وجود ندارد | نام دامنه یا اطلاعات نام‌سرور در پایگاه داده وجود ندارد. ثبت جدید لازم است. |
| ۲۳۰۴ | وضعیت شیء عملیات را ممنوع می‌کند | وضعیت شیء اقدام را ممنوع می‌کند، به‌روزرسانی‌ها قفل شده‌اند. وضعیت نباید "clientupdateprohibited" باشد. ممکن است به دلیل سایر شرایط وضعیت باشد. |

---

## 🌟 دربارهٔ این نسخه

**مترجم و توسعه‌دهندهٔ نسخهٔ اختصاصی فارسی:**  
**رامین تجلائی (Ramin Dibā)**  
🌐 وب‌سایت: [www.dibaserver.com](https://www.dibaserver.com)

این ترجمهٔ فارسی به منظور استفادهٔ آسان‌تر کاربران فارسی‌زبان از افزونهٔ DomainNameAPI برای WHMCS تهیه شده است.  
برای پشتیبانی، همکاری یا سفارش نسخه‌های اختصاصی، می‌توانید از طریق وب‌سایت فوق با ما در ارتباط باشید.

---