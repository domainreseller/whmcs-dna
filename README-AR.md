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


## دليل التثبيت والتكامل

### المتطلبات الدنيا

- WHMCS 7.8 أو الأحدث
- PHP 7.4 أو الأحدث (الموصى به 8.1)
- يجب تفعيل مكون PHP SOAPClient.
- حقول مخصصة لبيانات الهوية / الرقم الضريبي / معلومات مكتب الضرائب في حقول العملاء. (اختياري)

## إعداد

!!!! تنبيه !!!!

_**إذا كنت تقوم بالترقية، قم بنسخ الملفات القديمة قبل التثبيت.**_

ضع مجلد "modules" في المجلد الذي قمت بتنزيله في المجلد الذي تم تثبيت Whmcs فيه. (مثال: /home/whmcs/public_html) تجاهل ملفات .gitinore و README.md و LICENSE.

<a href="https://user-images.githubusercontent.com/3975986/209725636-b6b41019-3810-412c-8c52-616aab3760ad.png"><img width="400" alt="صورة" src="https://user-images.githubusercontent.com/3975986/209725636-b6b41019-3810-412c-8c52-616aab3760ad.png"></a>

- انتقل إلى قسم إعدادات النظام

<hr>

<a href="https://user-images.githubusercontent.com/3975986/209725739-96ab634d-9cc4-486d-a258-88065ab55c0b.png"><img width="400" alt="صورة" src="https://user-images.githubusercontent.com/3975986/209725739-96ab634d-9cc4-486d-a258-88065ab55c0b.png"></a>

- انتقل إلى قسم مسجّل النطاقات

<hr>

<a href="https://user-images.githubusercontent.com/3975986/209726687-fbf56bd3-e78a-457c-a118-86f87b9db6f0.png"><img width="400" alt="صورة" src="https://user-images.githubusercontent.com/3975986/209726687-fbf56bd3-e78a-457c-a118-86f87b9db6f0.png"></a>

- في الصفحة التي تم إدخالها، إذا كنت تركت ملفات الوحدة في المجلد الصحيح، ستظهر "DomainNameAPI".
- بعد التفعيل، قم بإدخال اسم المستخدم وكلمة المرور التي تم الحصول عليها منا.
- بعد الحفظ، سيكون اسم المستخدم الخاص بك والرصيد الحالي مرئيين.
- قم بتطابق رقم الهوية TR ومعلومات الرقم الضريبي المطلوبة للحصول على اسم المجال .tr للمستخدمين الخاصة بك، إن وجدت، من الإعدادات التي رأيتها.
- إذا كنت تستخدم عملة واحدة رئيسية ما عدا الدولار الأمريكي، يمكنك تعيين إعداد "تحويل الصرف لمزامنة TLD" (هذا الإعداد يستخدم فقط لمزامنة التسعير لاستيرادات TLD إقليمية. وإلا فلن تحتاج إلى تغييره)

<a href="https://youtu.be/LEw_iMnquSo">+ رابط يوتيوب</a>

<hr>

## إعدادات التسعير وتعيين TLD والبحث

<a href="https://user-images.githubusercontent.com/3975986/209727461-dd79f4a8-ed49-45cd-b305-26a5d37c6fd9.png"><img width="400" alt="صورة" src="https://user-images.githubusercontent.com/3975986/209727461-dd79f4a8-ed49-45cd-b305-26a5d37c6fd9.png"></a>

- انتقل إلى تسعير النطاقات من إعدادات النظام.
<hr>

<a href="https://user-images.githubusercontent.com/3975986/209728124-fe1aabdc-b0b0-4b7c-be2a-ff3b572a56a4.png"><img width="400" alt="صورة" src="https://user-images.githubusercontent.com/3975986/209728124-fe1aabdc-b0b0-4b7c-be2a-ff3b572a56a4.png"></a>

- حدد TLD الذي تريد بيعه. (مثال: .com.tr)
- حدد "Domain Name API" للتسجيل التلقائي.
- حدد خيار رمز EPP.
- بالنسبة للتسعير، يمكنك إدخاله يدويًا. يمكنك أيضًا تعيين سعر جماعي. (سيتم شرحه في القسم التالي).

<a href="https://user-images.githubusercontent.com/3975986/209728748-51ae6bbe-018c-42a2-b85d-ab5f37cd6559.png"><img width="400" alt="صورة" src="https://user-images.githubusercontent.com/3975986/209728748-51ae6bbe-018c-42a2-b85d-ab5f37cd6559.png"></a>

- بدلاً من استخدام خوادم Whois العامة كمصدر استعلام نطاق، يمكنك استخدام واجهة برمجة التطبيقات لاسم المجال. للقيام بذلك، اضغط على الزر "تغيير" في قسم "مزود البحث"، حدد الخيار "DomainNameApi" الظاهر في الجزء السفلي بعد خيار سجل المجال، ثم اختر أي TLD لاستخدامه.

لمزيد من المعلومات: <a href="https://docs.whmcs.com/Domain_Pricing">تسعير نطاقات Whmcs</a>
<hr>

## التسعير الجماعي والتسعير التلقائي

<a href="https://user-images.githubusercontent.com/3975986/209730191-0b796b2f-7f90-4dba-9a17-8ed2e11e11b8.png"><img width="400" alt="صورة" src="https://user-images.githubusercontent.com/3975986/209730191-0b796b2f-7f90-4dba-9a17-8ed2e11e11b8.png"></a>

<a href="https://user-images.githubusercontent.com/3975986/209730869-5f667f65-4da7-401e-b39c-fa91d23d2682.png"><img width="400" alt="صورة" src="https://user-images.githubusercontent.com/3975986/209730869-5f667f65-4da7-401e-b39c-fa91d23d2682.png"></a>

- انتقل إلى مزامنة مسجّل TLD من قسم الأدوات. حدد "DomainNameApi" من الشاشة التي تظهر، انتظر قليلاً.
- في الشاشة التالية، يتم مقارنة جميع TLD في نظامنا مع جميع TLD في whmcs، ويتم حساب الهامش الربحي والخسارة وعرضه جماعيًا، مما يسمح بالاستيراد.

لمزيد من المعلومات: <a href="https://docs.whmcs.com/Registrar_TLD_Sync">مزامنة TLD في Whmcs</a>
<hr>

## منظور المدير

<a href="https://user-images.githubusercontent.com/3975986/209735794-6f2d6dbe-c4e2-463c-b768-1d79fe3b6d81.png"><img width="400" alt="صورة" src="https://user-images.githubusercontent.com/3975986/209735794-6f2d6dbe-c4e2-463c-b768-1d79fe3b6d81.png"></a>

- يمكنك إرسال "طلب حذف" لاسم المجال.
- يمكنك إرسال "إلغاء النقل" لاسم المجال.
- يمكنك رؤية الحالة المباشرة لبدء وانتهاء فوري لاسم المجال.
- يمكنك إدراج مشتركيك.
- يمكنك عرض معلومات الحقل الإضافي.

<hr>

## الإعدادات العامة

<a href="https://user-images.githubusercontent.com/3975986/209731622-51b3cd62-1c23-4257-a30c-ce3a00d10bf3.png"><img width="400" alt="صورة" src="https://user-images.githubusercontent.com/3975986/209731622-51b3cd62-1c23-4257-a30c-ce3a00d10bf3.png"></a>
<a href="https://user-images.githubusercontent.com/3975986/209732098-7dba4e20-220d-4450-be3b-0ad1f9b8083d.png"><img width="400" alt="صورة" src="https://user-images.githubusercontent.com/3975986/209732098-7dba4e20-220d-4450-be3b-0ad1f9b8083d.png"></a>

- انتقل إلى الإعدادات العامة من إعدادات النظام، حدد علامة التبويب المتعلقة بالنطاق.
- قم بتفعيل الخيار 'السماح للعملاء بتسجيل النطاقات معك' إذا كنت ترغب في أن يكون للعملاء القدرة على تسجيل أسماء النطاق بأنفسهم.
- قم بتفعيل الخيار 'السماح للعملاء بنقل النطاق إليك' إذا كنت ترغب في أن يكون للعملاء القدرة على نقل اسم المجال بأنفسهم.
- قم بتفعيل الخيار 'تمكين أوامر التجديد' إذا كنت ترغب في أن يتمكن العملاء من تجديد اسم المجال قبل تاريخ انتهاء الصلاحية.
- قم بتفعيل الخيار 'التجديد التلقائي عند الدفع' إذا كنت ترغب في أن يتم تجديد اسم المجال للعملاء في نفس وقت الدفع.
- قم بتفعيل الخيار 'تمكين مزامنة النطاق' إذا كنت ترغب في التحقق والمزامنة الدورية للنطاق الحالي. نوصي بتفعيل هذا الخيار.
- إذا كنت ترغب في إدارة أسماء النطاق باللغة التركية أو العبرية أو العربية أو الروسية، قم بتفعيل الخيار 'السماح بالنطاقات IDN'.
- في معلومات 'مخدم الأسماء الافتراضي'، أدخل معلومات مخدم الأسماء الخاصة بك.

<hr>

## إعدادات المزامنة

<a href="https://user-images.githubusercontent.com/3975986/209734789-de8a1692-281f-452d-900a-ab662f2aa4f6.png"><img width="400" alt="صورة" src="https://user-images.githubusercontent.com/3975986/209734789-de8a1692-281f-452d-900a-ab662f2aa4f6.png"></a>
<a href="https://user-images.githubusercontent.com/3975986/209734883-a96c13d8-6275-4fb3-b500-fc3a05b6c11f.png"><img width="400" alt="صورة" src="https://user-images.githubusercontent.com/3975986/209734883-a96c13d8-6275-4fb3-b500-fc3a05b6c11f.png"></a>

- انتقل إلى إعدادات الأتمتة من إعدادات النظام. انتقل إلى قسم إعدادات مزامنة النطاقات.
- قم بتشغيل مزامنة النطاقات.
- قم بتفعيل الخيار "مزامنة تاريخ الاستحقاق التالي" إذا كنت ترغب في تغيير تاريخ الانتهاء في التحديث.
- ضبط الإعدادات الأخرى وفقًا لكثافة نظامك.

<hr>

## عرض الأخطاء والتفاصيل

<a href="https://user-images.githubusercontent.com/3975986/209735161-1455e50b-e25c-4cab-9069-b1eb746b3a65.png"><img width="400" alt="صورة" src="https://user-images.githubusercontent.com/3975986/209735161-1455e50b-e25c-4cab-9069-b1eb746b3a65.png"></a>
<a href="https://user-images.githubusercontent.com/3975986/209735249-54826bd6-7f03-4827-94e1-110e6929da97.png"><img width="400" alt="صورة" src="https://user-images.githubusercontent.com/3975986/209735249-54826bd6-7f03-4827-94e1-110e6929da97.png"></a>

- انتقل إلى قسم سجل الوحدة من خيارات سجلات النظام.
- ابحث عن السجل ذي الصلة وانقر على التاريخ.
- يمكنك عرض طلب التفاصيل والرد والرد المُصفى بشكل مفصل.

!! نوصي بإغلاق سجل النظام للاستخدام اليومي من حيث أداء النظام. لمزيد من المعلومات: <a href="https://docs.whmcs.com/System_Logs">تسجيلات Whmcs</a>

## الاختبارات

| اسم الاختبار   | GTLD | TRTLD |
|----------------|------|-------|
| تسجيل        | ✓    | ✓     |
| نقل           | ✓    | ✓     |
| تجديد          | ✓    | ✓     |
| خادم الأسماء     | ✓    | ✓     |
| قفل المسجّل  | ✓    | ✓     |
| جهة الاتصال     | ✓    | ✓     |
| EPP            | ✓    | ✓     |
| حذف         | ✓    | ✓     |
| خادم أسماء فرعي  | ✓    | ✓     |
| توافر        | ✓    | ✓     |
| تزامن التسعير   | ✓    | ✓     |
| إلغاء النقل | ✓    | ✓     |
| مزامنة    | ✓    | ✓     |
| مزامنة النقل   | ✓    | ✓     |

## استكشاف الأخطاء وإصلاحها
- لقد أضفت بالفعل حقولًا مخصصة جديدة ولكن لا يمكنني رؤيتها في الإعدادات.
- قد يكون التخزين المؤقت منتهي الصلاحية. احذف جميع الملفات في مجلد التخزين المؤقت.
<hr>

- حصلت على خطأ "Parsing WSDL: Couldn't load from..."
- يبدو أن هناك مشكلة في الشبكة. قد يتم حظر عنوان IP لخادمك من قِبَل سجل المجال. اتصل بنا لحل المشكلة.
