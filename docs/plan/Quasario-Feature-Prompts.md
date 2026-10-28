# Quasario: الميزات خطوة بخطوة مع برومبت مترابط لكل ميزة

مبنية على [خطة التنفيذ](../Quasario-Development-Plan.docx) والتصاميم المعتمدة. الإصدار 2: الترتيب مبني على الاعتماديات، ولكل ميزة شرح لفائدتها، وكل برومبت يعرف ما بُني قبله وما سيُبنى عليه بعده.

## كيف رتّبتُ الخطوات

- **لا خطوة قبل ما تعتمد عليه.** الملف يُبنى بسكربت يرفض أي خطوة تسبق اعتمادياتها، فالترتيب مضمون.
- **كل خلفية تتبعها شاشتها مباشرة** (شريحة عمودية). هكذا تنتهي كل ميزة كاملة وتُعرض يوم الخميس، ولا تتراكم endpoints بلا شاشات.
- **الأساس المشترك أولاً:** قاعدة البيانات، شكل الأخطاء، مكوّنات الواجهة، خدمة الإشعارات، مكوّنات الخريطة، تخزين الصور. كل خطوة لاحقة تعيد استخدامها بالاسم.
- **الخطر مبكراً:** النشر في الخطوة 9، وتجربتا الخريطة والذكاء الاصطناعي في Sprint 1، ونسخة المساعد الأولى في Sprint 2.
- **ما يُبنى عليه يُبنى قبله:** خدمة حذف الحساب قبل معالجة البلاغات (حذف صفحة = حذف حساب)، وبلاغات المستخدمين قبل لوحة البلاغات، ومنتقي الأشخاص في الأقسام قبل طلبات التقييم.
- الثنائيان يعملان بالتوازي: الثنائي أ (Nazeer + Batol) والثنائي ب (Hossin + Abdulhadi). ترتيب الجدول هو ترتيب الاعتماد، وليس ترتيب العمل الإجباري لكل شخص.

## كيف تستخدم البرومبتات

1. **مرة واحدة:** ضع [CLAUDE.md](CLAUDE.md) في جذر المستودع. فيه سياق المشروع كاملاً.
2. **Claude Code داخل المستودع:** يقرأ CLAUDE.md تلقائياً. انسخ البرومبت كما هو؛ سيقرأ الملفات المذكورة في «Already in the codebase» قبل أن يكتب.
3. **claude.ai:** أنشئ Project وضع CLAUDE.md في تعليماته، وارفع معه الملفات التي تذكرها الخطوة من الكود الحالي (مثلاً routes/api.php والـModels المعنية) حتى يرى ما بُني قبله.
4. **الواجهة:** أرفق صور الشاشات المذكورة (Sxx من ui-missing/png، والباقي من UI Image).
5. **بعد كل خطوة:** الاختبارات تنجح، تراجع الكود وتفهمه، Pull Request برقم الحالة، مراجعة زميل، دمج. إذا غيّر Claude اسم شيء مذكور في «keep these stable» فارفض التغيير.
6. إذا تغيّر التصميم: عدّل CLAUDE.md أولاً.

**التصنيف:** Backend = Laravel · Frontend = شاشات المستخدم · Admin (Frontend) = لوحة الإدارة · DevOps = إعداد ونشر · Test = اختبارات.

## القائمة المرتبة

| # | الميزة | النوع | المسؤول | الحالات | تعتمد على |
|---|---|---|---|---|---|
| | **Sprint 1: الأساس والمصادقة (25/10 – 5/11)** | | | | |
| 1 | [المستودع والهيكل وCI](#step-1) | DevOps | Nazeer | — | — |
| 2 | [قاعدة البيانات: Migrations والـModels والـEnums](#step-2) | Backend | Nazeer | كل الحالات | 1 |
| 3 | [بيانات أولية: Seeders وFactories](#step-3) | Backend | Nazeer | — | 2 |
| 4 | [أساس الـAPI: الأخطاء والصلاحيات](#step-4) | Backend | Hossin | — | 1 |
| 5 | [هيكل الواجهة: التوجيه والتخطيط وعميل الـAPI](#step-5) | Frontend | Batol | — | 1 |
| 6 | [مكوّنات الواجهة المشتركة](#step-6) | Frontend | Batol | — | 5 |
| 7 | [المصادقة: التسجيل بمرحلتين والدخول والخروج](#step-7) | Backend | Hossin | UC-U01 U02 U03 A01 A02 | 4، 3 |
| 8 | [شاشات الدخول والتسجيل بمرحلتين](#step-8) | Frontend | Abdulhadi | UC-U01 U02 U03 A01 A02 | 7، 6 |
| 9 | [النشر على الخادم التجريبي](#step-9) | DevOps | Nazeer + Syrien | — | 8 |
| 10 | [استعادة كلمة المرور](#step-10) | Backend | Hossin | UC-U02 (بديل) | 7 |
| 11 | [شاشات نسيت كلمة المرور وكلمة المرور الجديدة](#step-11) | Frontend | Abdulhadi | UC-U02 (بديل) | 10، 8 |
| 12 | [تجربة الخريطة ومكوّناتها المشتركة](#step-12) | Frontend | Batol | UC-V06 V07 U25 U26 VU02 V10 | 5 |
| 13 | [تجربة الذكاء الاصطناعي](#step-13) | Backend | Hossin | UC-V08 | 3 |
| | **Sprint 2: قلب الدليل (8/11 – 26/11)** | | | | |
| 14 | [عرض الصفحة والقوائم الثابتة](#step-14) | Backend | Nazeer | UC-V03 V04 | 7 |
| 15 | [الصفحة الرئيسية والبحث والتصفية](#step-15) | Backend | Nazeer | UC-V01 V02 | 14 |
| 16 | [الصفحة الرئيسية والبحث والتصفية](#step-16) | Frontend | Batol | UC-V01 V02 | 15، 6 |
| 17 | [صفحة العرض وعارض صور المعرض](#step-17) | Frontend | Batol | UC-V03 V04 | 14، 16 |
| 18 | [تعديل الملف الشخصي](#step-18) | Backend | Nazeer | UC-U04 | 14 |
| 19 | [معرض الأعمال](#step-19) | Backend | Nazeer | UC-U21 U22 | 18 |
| 20 | [روابط التواصل الاجتماعي](#step-20) | Backend | Nazeer | UC-U23 U24 | 14 |
| 21 | [تعديل الملف والمعرض والروابط](#step-21) | Frontend | Batol | UC-U04 U21–U24 | 18، 19، 20، 17 |
| 22 | [المفضلة](#step-22) | Backend | Nazeer | UC-U06 U07 U08 | 14 |
| 23 | [شاشة المحفوظات وزر الحفظ](#step-23) | Frontend | Batol | UC-U06 U07 U08 | 22، 17 |
| 24 | [بنية الإشعارات والبحث عن أشخاص](#step-24) | Backend | Hossin | UC-U11 | 7 |
| 25 | [شاشة الإشعارات](#step-25) | Frontend | Abdulhadi | UC-U11 | 24، 6 |
| 26 | [الأقسام: إنشاء وتعديل وحذف](#step-26) | Backend | Hossin | UC-U27 U28 U29 U30 | 24، 14 |
| 27 | [دعوات الأقسام والعضوية](#step-27) | Backend | Hossin | UC-U31–U36 | 26 |
| 28 | [شاشات الأقسام](#step-28) | Frontend | Abdulhadi | UC-U27–U30 U32 U36 | 26، 27، 21 |
| 29 | [شاشة دعوات الأقسام](#step-29) | Frontend | Abdulhadi | UC-U33 U34 U35 | 27، 25 |
| 30 | [المساعد الذكي نسخة أولى (بالبديل الاحتياطي)](#step-30) | Backend | Hossin | UC-V08 | 13، 15 |
| | **Sprint 3: الثقة والفعاليات (29/11 – 17/12)** | | | | |
| 31 | [التقييمات الموثّقة](#step-31) | Backend | Nazeer | UC-U16–U20 V05 | 19، 24 |
| 32 | [شاشات التقييم الموثّق](#step-32) | Frontend | Batol | UC-U16–U20 V05 | 31، 28 |
| 33 | [طلب شارة التوثيق](#step-33) | Backend | Nazeer | UC-U15 | 7 |
| 34 | [شاشة شارة التوثيق](#step-34) | Frontend | Batol | UC-U15 | 33، 6 |
| 35 | [موقع الصفحة وخريطة الصفحات](#step-35) | Backend | Nazeer | UC-U25 U26 V06 V07 | 18 |
| 36 | [موقع الصفحة والخريطة التفاعلية](#step-36) | Frontend | Batol | UC-U25 U26 V06 V07 | 35، 12، 21 |
| 37 | [الفعاليات: جهة المستخدم الموثّق](#step-37) | Backend | Hossin | UC-VU01–VU05 | 33، 18، 24 |
| 38 | [الفعاليات العامة](#step-38) | Backend | Hossin | UC-V09 V10 | 37 |
| 39 | [مراجعة الفعاليات (المسؤول)](#step-39) | Backend | Hossin | UC-A12–A16 A20 | 37 |
| 40 | [شاشات الفعاليات العامة](#step-40) | Frontend | Abdulhadi | UC-V09 V10 | 38، 12 |
| 41 | [إدارة فعالياتي](#step-41) | Frontend | Abdulhadi | UC-VU01–VU05 | 37، 40، 34 |
| 42 | [لوحة الإدارة: الهيكل ومراجعة الفعاليات](#step-42) | Admin (Frontend) | Abdulhadi | UC-A12–A16 A20 | 39، 40 |
| | **Sprint 4: الإدارة والتواصل والمساعد (20/12 – 7/1)** | | | | |
| 43 | [البلاغات (جهة المستخدم)](#step-43) | Backend | Nazeer | UC-U12 U13 U14 | 14، 38 |
| 44 | [زر ونموذج التبليغ](#step-44) | Frontend | Batol | UC-U12 U13 U14 | 43، 17، 40 |
| 45 | [المحادثة](#step-45) | Backend | Nazeer | UC-U09 U10 | 24، 14 |
| 46 | [شاشات المحادثة](#step-46) | Frontend | Batol | UC-U09 U10 | 45، 17 |
| 47 | [حذف الحساب](#step-47) | Backend | Nazeer | UC-U05 | 2، 45 |
| 48 | [حذف الحساب](#step-48) | Frontend | Batol | UC-U05 | 47، 8 |
| 49 | [إدارة الحسابات والحظر وسحب الشارة](#step-49) | Backend | Hossin | UC-A03–A08 | 39، 7 |
| 50 | [لوحة الإدارة: الحسابات](#step-50) | Admin (Frontend) | Abdulhadi | UC-A03–A08 | 49، 42 |
| 51 | [مراجعة طلبات التوثيق](#step-51) | Backend | Hossin | UC-A09 A10 A11 | 33، 39 |
| 52 | [لوحة الإدارة: طلبات التوثيق](#step-52) | Admin (Frontend) | Abdulhadi | UC-A09 A10 A11 | 51، 42 |
| 53 | [معالجة البلاغات](#step-53) | Backend | Hossin | UC-A17 A18 A19 | 43، 47، 49 |
| 54 | [لوحة الإدارة: البلاغات](#step-54) | Admin (Frontend) | Abdulhadi | UC-A17 A18 A19 | 53، 42 |
| 55 | [المساعد الذكي الكامل](#step-55) | Backend | Hossin | UC-V08 | 30، 35 |
| 56 | [شاشة المساعد الذكي](#step-56) | Frontend | Batol | UC-V08 | 55، 16 |
| | **التثبيت والتسليم (8/1 – 21/1)** | | | | |
| 57 | [مراجعة التجاوب والتناسق](#step-57) | Frontend | Batol | كل الشاشات | 56، 48، 46، 44 |
| 58 | [اختبارات طرف إلى طرف (Playwright)](#step-58) | Test | Abdulhadi | المسارات الحرجة | 50، 52، 54، 32، 41 |
| 59 | [مراجعة الأمان](#step-59) | Test | الجميع | — | 58 |
| 60 | [الإنتاج والنسخ الاحتياطي](#step-60) | DevOps | Syrien | — | 9، 59 |
| 61 | [بيانات وسيناريو العرض](#step-61) | DevOps | Syrien | — | 60 |

## Sprint 1: الأساس والمصادقة (25/10 – 5/11)

<a id="step-1"></a>
### 1. المستودع والهيكل وCI

**النوع:** DevOps · **المسؤول:** Nazeer · **الحالات:** —

**ماذا تضيف للبرنامج:** هذه هي الأرض التي يقف عليها كل شيء: مستودع واحد يعمل فيه الأربعة، وفحص تلقائي (CI) يمنع دمج كود مكسور. بدونها يضيع الوقت في دمج ملفات من أجهزة مختلفة.

**تعتمد على:** لا شيء  
**تعتمد عليها لاحقاً:** 2 (قاعدة البيانات: Migrations والـModels والـEnums)، 4 (أساس الـAPI: الأخطاء والصلاحيات)، 5 (هيكل الواجهة: التوجيه والتخطيط وعميل الـAPI)

```text
[Quasario · Step 1/61 · DevOps · —]
Project: Quasario, a local services directory (Laravel REST API + React SPA + admin panel). The full
context is in CLAUDE.md: read it before anything else.
Feature: Repository skeleton and CI.
Done before this step: none.
Already in the codebase (reuse it, do not recreate it): Nothing yet. This is the first step.
Later steps build on this: step 2 'Database, models and enums'; step 4 'API foundation: errors and middleware'; step 5 'Frontend architecture'.
So keep these stable and named exactly as written: Folder layout backend/ + frontend/, CLAUDE.md at the root, CI workflow that every later PR must pass, README setup that every teammate follows.

Task: Create the Quasario monorepo skeleton.
- Root: README.md (prerequisites with exact versions: PHP 8.3+, Composer, MySQL 8, Node 22 LTS; setup for backend
  and frontend; how to run tests), .gitignore, .editorconfig, docs/api.md, docs/decisions.md,
  .github/pull_request_template.md (What / UC ids / How to test / Screenshots / Definition of Done checklist).
- Copy CLAUDE.md (attached) to the root.
- backend/: new Laravel project, `php artisan install:api` (Sanctum + routes/api.php), Pest, Laravel Pint,
  knuckleswtf/scribe. .env.example: MySQL, QUEUE_CONNECTION=database, CACHE_STORE=database, MAIL_MAILER=log,
  FRONTEND_URL, ADMIN_EMAIL, ADMIN_PASSWORD. API prefix /api/v1.
- frontend/: Vite React TypeScript with ESLint + Prettier; scripts dev, build, lint, typecheck, test.
- .github/workflows/ci.yml on pull_request and push to main. backend job: PHP 8.3, MySQL 8 service, composer install,
  pint --test, migrate, pest. frontend job: Node 22, npm ci, lint, tsc --noEmit, vitest run, build.
Give every file and the exact commands in order.
```

<a id="step-2"></a>
### 2. قاعدة البيانات: Migrations والـModels والـEnums

**النوع:** Backend · **المسؤول:** Nazeer · **الحالات:** كل الحالات

**ماذا تضيف للبرنامج:** يحوّل ERD المعتمد إلى جداول حقيقية، ومخطط Class إلى Models. كل ميزة لاحقة تقرأ وتكتب في هذه الجداول، فأي خطأ هنا يتكرر في كل مكان. سلوك الحذف المتتالي هنا هو ما يجعل حذف الحساب والبلاغ يعمل بسطر واحد.

**تعتمد على:** 1 (المستودع والهيكل وCI)  
**تعتمد عليها لاحقاً:** 3 (بيانات أولية: Seeders وFactories)، 47 (حذف الحساب)

```text
[Quasario · Step 2/61 · Backend · كل الحالات]
Project: Quasario, a local services directory (Laravel REST API + React SPA + admin panel). The full
context is in CLAUDE.md: read it before anything else.
Feature: Database, models and enums.
Done before this step: step 1 'Repository skeleton and CI'.
Already in the codebase (reuse it, do not recreate it): The empty Laravel app from step 'Repository skeleton and CI'.
Later steps build on this: step 3 'Seeders and factories'; step 47 'Account deletion API'.
So keep these stable and named exactly as written: All 23 tables, PHP enums in app/Enums, models with relationships, User::isAdmin()/isBanned(), scope User::active(), scope Page::visible() (owner not banned), Page average_rating accessor + a reusable query scope withAverageRating(). Every later backend step uses these names; do not rename them later.

Task: Implement the database from CLAUDE.md sections 4 and 5.
- One migration per table in dependency order (lookups, users, password_reset_tokens, pages, page_locations,
  social_links, gallery_images, saved_pages, sections, section_members, section_invitations, notifications,
  conversations, messages, verification_requests, endorsement_requests, endorsement_request_images, endorsements,
  events, event_posts, reports). Exact columns, nullability, unique keys and on-delete rules. Enum columns store
  lowercase values. CHECK constraints: events end_date >= start_date; reports exactly one target FK set;
  endorsements rating 1..5.
- PHP backed enums (app/Enums) with lowercase values and label().
- Models with $fillable, $casts and all relationships: User hasOne page, hasMany events, notifications,
  verificationRequests, sentEndorsementRequests (requester), receivedEndorsementRequests (endorser);
  Page belongsTo user/jobTitle/city, hasOne location, hasMany galleryImages/socialLinks/sections;
  Section hasMany members (section_members) and invitations; EndorsementRequest belongsToMany GalleryImage
  (endorsement_request_images), hasOne endorsement; Event hasMany posts (ordered oldest first);
  Report belongsTo page/event/eventPost/reporter/handler; Conversation hasMany messages.
- User: HasApiTokens + RoutesNotifications (NOT Notifiable); isAdmin(), isBanned(), scope active().
- Page: scope visible() (owner active), scope withAverageRating() adding `average_rating` and `endorsements_count`
  via subqueries over accepted endorsements of the owner (requester), accessor job_category through job title.
- Minimal factories for every model (completed in the next step).
- Pest test: create a row in every table, then delete a user and assert the cascade (page, gallery, sections,
  memberships, requests, endorsements, conversations, messages, events, posts, reports, notifications) and that
  admin FKs reviewed_by / handled_by become NULL when an admin is deleted.

How to work:
- Read CLAUDE.md first; use its table names, enums, error codes and API contract exactly.
- Before coding, read the files listed under "Already in the codebase" and reuse them. Then list the files you
  will create or change, and write them in full.
- Thin controllers; rules in Services; validation in FormRequests; ownership in Policies; output via API Resources.
- Pest feature tests in tests/Feature/<Module>/: success, every error listed above (assert the `code`), and
  rejection of a visitor, another user and a banned user where relevant.
- Do not change migrations or other modules unless this task says so. If you must touch a shared file, say why.
- Update docs/api.md with the endpoints (request, response example, error codes).
- Finish with: routes added, how to run the tests, example curl requests, and anything the frontend must know.
```

<a id="step-3"></a>
### 3. بيانات أولية: Seeders وFactories

**النوع:** Backend · **المسؤول:** Nazeer · **الحالات:** —

**ماذا تضيف للبرنامج:** يملأ القاعدة بمدن ومهن حقيقية وحساب مسؤول وصفحات تجريبية، فيستطيع فريق الواجهة رؤية شاشات ممتلئة من اليوم الأول، وتصبح الاختبارات أسهل. القوائم هنا هي نفسها التي يختار منها المستخدم عند التسجيل والتصفية.

**تعتمد على:** 2 (قاعدة البيانات: Migrations والـModels والـEnums)  
**تعتمد عليها لاحقاً:** 7 (المصادقة: التسجيل بمرحلتين والدخول والخروج)، 13 (تجربة الذكاء الاصطناعي)

```text
[Quasario · Step 3/61 · Backend · —]
Project: Quasario, a local services directory (Laravel REST API + React SPA + admin panel). The full
context is in CLAUDE.md: read it before anything else.
Feature: Seeders and factories.
Done before this step: step 2 'Database, models and enums'.
Already in the codebase (reuse it, do not recreate it): Models, enums and minimal factories from step 'Database, models and enums'.
Later steps build on this: step 7 'Authentication API'; step 13 'AI assistant spike'.
So keep these stable and named exactly as written: Realistic factories (states: banned(), verified(), admin(), withPage()), CitySeeder, JobSeeder, AdminSeeder, DemoSeeder. Every later test uses these factory states; keep their names.

Task: Complete factories and seeders.
- Factory states: User::factory()->admin(), ->banned(), ->verified(), ->withPage() (creates the page with a
  random city and job title). Factories for gallery images use a small placeholder image.
- CitySeeder: 14 Syrian governorate centers (Damascus, Aleppo, Homs, Hama, Latakia, Tartus, Idlib, Daraa,
  As-Suwayda, Quneitra, Deir ez-Zor, Raqqa, Al-Hasakah, Rif Dimashq).
- JobSeeder: ~10 job categories (Medical, Engineering, Education, Crafts & Repair, Design & Media, Technology,
  Beauty, Legal & Finance, Food, Transport), each with 4–8 English job titles.
- AdminSeeder from .env ADMIN_EMAIL / ADMIN_PASSWORD.
- DemoSeeder (only local/staging): 30 users with pages, gallery images, social links, some locations around
  Damascus, accepted endorsements, 3 verified users with accepted and pending events and posts, 1 banned user.
- `php artisan migrate:fresh --seed` must work on an empty database.

How to work:
- Read CLAUDE.md first; use its table names, enums, error codes and API contract exactly.
- Before coding, read the files listed under "Already in the codebase" and reuse them. Then list the files you
  will create or change, and write them in full.
- Thin controllers; rules in Services; validation in FormRequests; ownership in Policies; output via API Resources.
- Pest feature tests in tests/Feature/<Module>/: success, every error listed above (assert the `code`), and
  rejection of a visitor, another user and a banned user where relevant.
- Do not change migrations or other modules unless this task says so. If you must touch a shared file, say why.
- Update docs/api.md with the endpoints (request, response example, error codes).
- Finish with: routes added, how to run the tests, example curl requests, and anything the frontend must know.
```

<a id="step-4"></a>
### 4. أساس الـAPI: الأخطاء والصلاحيات

**النوع:** Backend · **المسؤول:** Hossin · **الحالات:** —

**ماذا تضيف للبرنامج:** يضمن أن كل endpoint في المشروع يرد بنفس الشكل، وأن رموز الأخطاء من SSD تصل للواجهة كما هي. الواجهة تبني رسائلها على هذه الرموز، والـMiddleware هنا هو ما يمنع الزائر والمحظور وغير الموثّق من الوصول لما لا يحق له.

**تعتمد على:** 1 (المستودع والهيكل وCI)  
**تعتمد عليها لاحقاً:** 7 (المصادقة: التسجيل بمرحلتين والدخول والخروج)

```text
[Quasario · Step 4/61 · Backend · —]
Project: Quasario, a local services directory (Laravel REST API + React SPA + admin panel). The full
context is in CLAUDE.md: read it before anything else.
Feature: API foundation: errors and middleware.
Done before this step: step 1 'Repository skeleton and CI'.
Already in the codebase (reuse it, do not recreate it): The Laravel app with Sanctum from step 'Repository skeleton and CI'.
Later steps build on this: step 7 'Authentication API'.
So keep these stable and named exactly as written: DomainError(code, status), the JSON error format, middleware aliases not_banned / admin / verified, route groups in routes/api.php, rate limiters `auth` and `actions`. Every later endpoint is added inside these groups and throws DomainError for business rules.

Task: Shared API foundation in backend/ (no features).
1. app/Exceptions/DomainError.php: exception with string `code`, HTTP status, message.
2. bootstrap/app.php exception rendering for /api/*, always {"message","code","errors"}:
   ValidationException → 422; per field keep Laravel's messages and set top-level `code` from the first failure
   mapped to CLAUDE.md codes (required→requiredField, email/regex/url/in/exists→invalidField, unique→alreadyUsed,
   min on password→weakPassword, mimes/image/max on files→unsupportedFile); AuthenticationException → 401
   "unauthenticated"; AuthorizationException → 403 "forbidden"; ModelNotFound/NotFoundHttp → 404 notFound;
   DomainError → its status/code; other → 500 "serverError" without trace when APP_DEBUG=false.
3. Middleware aliases: not_banned (403 accountBanned and deletes the current token), admin (403 notAdmin),
   verified (403 notVerified).
4. routes/api.php, prefix v1, groups: public; auth:sanctum + not_banned; + verified; + admin. Add GET /health.
5. CORS only FRONTEND_URL. Rate limiters `auth` (5/min per IP) and `actions` (30/min per user).
6. Tests for each error shape and each middleware.

How to work:
- Read CLAUDE.md first; use its table names, enums, error codes and API contract exactly.
- Before coding, read the files listed under "Already in the codebase" and reuse them. Then list the files you
  will create or change, and write them in full.
- Thin controllers; rules in Services; validation in FormRequests; ownership in Policies; output via API Resources.
- Pest feature tests in tests/Feature/<Module>/: success, every error listed above (assert the `code`), and
  rejection of a visitor, another user and a banned user where relevant.
- Do not change migrations or other modules unless this task says so. If you must touch a shared file, say why.
- Update docs/api.md with the endpoints (request, response example, error codes).
- Finish with: routes added, how to run the tests, example curl requests, and anything the frontend must know.
```

<a id="step-5"></a>
### 5. هيكل الواجهة: التوجيه والتخطيط وعميل الـAPI

**النوع:** Frontend · **المسؤول:** Batol · **الحالات:** —

**ماذا تضيف للبرنامج:** يبني هيكل التطبيق الذي تدخل فيه كل الشاشات: شريط التنقل للهاتف والحاسوب، ومسارات الزائر والمستخدم والموثّق والمسؤول، وعميل API واحد يضيف التوكن ويفهم الأخطاء. كل شاشة لاحقة تُضاف إلى هذا الهيكل فقط.

**تعتمد على:** 1 (المستودع والهيكل وCI)  
**تعتمد عليها لاحقاً:** 6 (مكوّنات الواجهة المشتركة)، 12 (تجربة الخريطة ومكوّناتها المشتركة)

```text
[Quasario · Step 5/61 · Frontend · —]
Project: Quasario, a local services directory (Laravel REST API + React SPA + admin panel). The full
context is in CLAUDE.md: read it before anything else.
Feature: Frontend architecture.
Done before this step: step 1 'Repository skeleton and CI'.
Already in the codebase (reuse it, do not recreate it): The Vite React TS project from step 'Repository skeleton and CI'.
Later steps build on this: step 6 'Shared UI components'; step 12 'Map components spike'.
So keep these stable and named exactly as written: router.tsx with all routes and guards (GuestOnly, RequireUser, RequireVerified, RequireAdmin), AuthProvider/useAuth (token + user), api/client.ts with normalized ApiError {status, code, message, errors}, lib/errors.ts mapping every CLAUDE.md error code to an English message, api/types.ts with all enums, MainLayout / AuthLayout / AdminLayout. Later screens only add pages to these routes.

Task: Frontend architecture in frontend/src.
- Tailwind v4 (@tailwindcss/vite) theme: bg #2b2b2b, primary #fdd54f, card #116754, neutrals.
- app/router.tsx: public (/, /search, /pages/:id, /events, /events/:id, /map, /assistant); guest (/sign-in,
  /sign-up, /forgot-password, /reset-password); user (/profile, /profile/edit, /saved, /chats, /chats/:id,
  /notifications, /settings, /sections/requests, /endorsements, /verification); verified (/my-events,
  /my-events/new, /my-events/:id/edit); admin (/admin, /admin/accounts, /admin/accounts/:id, /admin/banned,
  /admin/verification, /admin/verification/:id, /admin/events, /admin/events/:id, /admin/reports,
  /admin/reports/:id). Guards as listed; placeholder pages for now.
- app/providers.tsx: QueryClientProvider, AuthProvider (token in localStorage "quasario.token", current user from
  GET /me), Toast provider.
- api/client.ts: axios, baseURL VITE_API_URL, Bearer interceptor, 401 → clear auth + redirect /sign-in, errors
  normalized to ApiError. lib/errors.ts: messageFor(code).
- api/types.ts: enums from CLAUDE.md section 5 and shared types (Paginated<T>, ApiError, User).
- layouts: MainLayout (mobile bottom nav Events / Map / Home / Chats / Profile + round AI button; desktop top nav),
  AuthLayout, AdminLayout (sidebar: Accounts, Banned accounts, Verification requests, Manage events, Reports, Log out).

How to work:
- Read CLAUDE.md first; use its conventions, colors, navigation and API contract exactly.
- Before coding, read the files listed under "Already in the codebase" and reuse them (shared components, api
  client, hooks, types). Then list the files you will create or change, and write them in full (TypeScript, Tailwind).
- API calls in src/api/endpoints/<module>.ts with types in src/api/types.ts (snake_case fields as the API returns
  them); TanStack Query hooks inside the feature folder; invalidate the related queries after each mutation.
- Forms: react-hook-form + zod with the same rules as the backend; show server field errors; map `code` to an
  English message through the shared error helper.
- Every screen has loading, empty and error states and works at 360px and 1280px.
- If the endpoint is not merged yet, mock it in the endpoint file behind `import.meta.env.VITE_USE_MOCKS` with the
  exact response shape from docs/api.md.
- Vitest tests for the zod schemas and one component test for the main screen.
- I attach the UI mockups named above; match their layout and texts.
```

<a id="step-6"></a>
### 6. مكوّنات الواجهة المشتركة

**النوع:** Frontend · **المسؤول:** Batol · **الحالات:** —

**ماذا تضيف للبرنامج:** مكوّنات تُكتب مرة واحدة وتستخدمها كل الشاشات: الأزرار والحقول ونافذة تأكيد الحذف الموحّدة ونافذة «سجّل الدخول» للزائر. هذا يجعل التطبيق متناسقاً ويوفر أياماً من العمل المكرر، وهو تطبيق مباشر لقرارات UI المعتمدة.

**تعتمد على:** 5 (هيكل الواجهة: التوجيه والتخطيط وعميل الـAPI)  
**تعتمد عليها لاحقاً:** 8 (شاشات الدخول والتسجيل بمرحلتين)، 16 (الصفحة الرئيسية والبحث والتصفية)، 25 (شاشة الإشعارات)، 34 (شاشة شارة التوثيق)

```text
[Quasario · Step 6/61 · Frontend · —]
Project: Quasario, a local services directory (Laravel REST API + React SPA + admin panel). The full
context is in CLAUDE.md: read it before anything else.
Feature: Shared UI components.
Done before this step: step 5 'Frontend architecture'.
Already in the codebase (reuse it, do not recreate it): Theme, layouts and providers from step 'Frontend architecture'.
Later steps build on this: step 8 'Sign up and sign in screens'; step 16 'Home, search and filter screens'; step 25 'Notifications screen'; step 34 'Trust mark screen'.
So keep these stable and named exactly as written: components/ui: Button, Input, Textarea, Select, MultiSelect, ImageUpload, Modal, ConfirmDialog + useConfirm(), SignInRequiredDialog + useRequireAuth(), Toast, Spinner, EmptyState, ErrorState, Avatar, Badge (status), Card, Tabs, LoadMore, StarRating. Every later screen must use these instead of new ones.

Task: Shared UI kit in src/components/ui (Tailwind, accessible, responsive):
Button (primary yellow / secondary / danger / ghost, loading), Input, Textarea (with counter), Select, MultiSelect,
ImageUpload (preview; JPG/PNG ≤5MB check; `accept` and optional `capture`), Modal, ConfirmDialog with a
`useConfirm()` hook returning a promise (the single delete confirmation of the design), SignInRequiredDialog with
`useRequireAuth()` that runs an action if signed in or opens the dialog for visitors, Toast, Spinner, EmptyState,
ErrorState (retry), Avatar, Badge (PENDING / ACCEPTED / REJECTED colors), Card, Tabs, LoadMore (infinite queries),
StarRating (display and input 1–5).
Dev-only /ui page showing all of them. Vitest tests for ConfirmDialog, useRequireAuth and ImageUpload validation.

How to work:
- Read CLAUDE.md first; use its conventions, colors, navigation and API contract exactly.
- Before coding, read the files listed under "Already in the codebase" and reuse them (shared components, api
  client, hooks, types). Then list the files you will create or change, and write them in full (TypeScript, Tailwind).
- API calls in src/api/endpoints/<module>.ts with types in src/api/types.ts (snake_case fields as the API returns
  them); TanStack Query hooks inside the feature folder; invalidate the related queries after each mutation.
- Forms: react-hook-form + zod with the same rules as the backend; show server field errors; map `code` to an
  English message through the shared error helper.
- Every screen has loading, empty and error states and works at 360px and 1280px.
- If the endpoint is not merged yet, mock it in the endpoint file behind `import.meta.env.VITE_USE_MOCKS` with the
  exact response shape from docs/api.md.
- Vitest tests for the zod schemas and one component test for the main screen.
- I attach the UI mockups named above; match their layout and texts.
```

<a id="step-7"></a>
### 7. المصادقة: التسجيل بمرحلتين والدخول والخروج

**النوع:** Backend · **المسؤول:** Hossin · **الحالات:** UC-U01 U02 U03 A01 A02

**ماذا تضيف للبرنامج:** تجعل المستخدم يملك حساباً وصفحة من لحظة التسجيل، فتظهر صفحته في الدليل مباشرة. الدخول الواحد للمستخدم والمسؤول يوجّه كل واحد إلى مكانه، ومنع المحظور من الدخول هو أساس لوحة الإدارة لاحقاً.

**تعتمد على:** 4 (أساس الـAPI: الأخطاء والصلاحيات)، 3 (بيانات أولية: Seeders وFactories)  
**تعتمد عليها لاحقاً:** 8 (شاشات الدخول والتسجيل بمرحلتين)، 10 (استعادة كلمة المرور)، 14 (عرض الصفحة والقوائم الثابتة)، 24 (بنية الإشعارات والبحث عن أشخاص)، 33 (طلب شارة التوثيق)، 49 (إدارة الحسابات والحظر وسحب الشارة)

```text
[Quasario · Step 7/61 · Backend · UC-U01 U02 U03 A01 A02]
Project: Quasario, a local services directory (Laravel REST API + React SPA + admin panel). The full
context is in CLAUDE.md: read it before anything else.
Feature: Authentication API.
Done before this step: step 4 'API foundation: errors and middleware'; step 3 'Seeders and factories'.
Already in the codebase (reuse it, do not recreate it): DomainError, error format, middleware and route groups (step 'API foundation'); User/Page models, factories and seeded cities/jobs (steps 'Database' and 'Seeders').
Later steps build on this: step 8 'Sign up and sign in screens'; step 10 'Password reset API'; step 14 'Page read API and filter options'; step 24 'Notifications service and people search'; step 33 'Verification request API'; step 49 'Admin accounts API'.
So keep these stable and named exactly as written: POST /auth/register/check, POST /auth/register, POST /auth/login, POST /auth/logout, GET /me with UserResource {id, username, email, role, is_verified, status, page_id}. The frontend auth screens and every later `me` check rely on this exact UserResource.

Task: Authentication endpoints (Sanctum tokens).
- POST /auth/register/check {email, username, phone, password}: validate only, save nothing. email valid + unique
  (alreadyUsed); username 3–50 [A-Za-z0-9._] unique; phone [+]digits 7–20; password ≥ 8 (weakPassword).
- POST /auth/register {step-1 fields + job_category_id, job_title_id, city_id, experience?}: re-validate step 1;
  job title must belong to the category (invalidField). One transaction: users (role user, status active) +
  pages (display_name = username, job_title_id, city_id, experience). 201, no token, no email confirmation.
- POST /auth/login {login, password}: email or username. Wrong → 401 invalidCredentials; banned → 403
  accountBanned. Success → {token, user: UserResource}.
- POST /auth/logout deletes the current token. GET /me returns UserResource.
- Rate limiter `auth` on register/check, register, login.
Tests: full sign-up, each validation code, login by email and by username, banned user, admin role, logout.

How to work:
- Read CLAUDE.md first; use its table names, enums, error codes and API contract exactly.
- Before coding, read the files listed under "Already in the codebase" and reuse them. Then list the files you
  will create or change, and write them in full.
- Thin controllers; rules in Services; validation in FormRequests; ownership in Policies; output via API Resources.
- Pest feature tests in tests/Feature/<Module>/: success, every error listed above (assert the `code`), and
  rejection of a visitor, another user and a banned user where relevant.
- Do not change migrations or other modules unless this task says so. If you must touch a shared file, say why.
- Update docs/api.md with the endpoints (request, response example, error codes).
- Finish with: routes added, how to run the tests, example curl requests, and anything the frontend must know.
```

<a id="step-8"></a>
### 8. شاشات الدخول والتسجيل بمرحلتين

**النوع:** Frontend · **المسؤول:** Abdulhadi · **الحالات:** UC-U01 U02 U03 A01 A02

**ماذا تضيف للبرنامج:** أول ما يراه المستخدم. بعدها يستطيع أي شخص إنشاء حساب ودخوله من الهاتف، ويُوجَّه المسؤول إلى لوحة الإدارة. كل الشاشات المحمية لاحقاً تعتمد على حالة الدخول التي تُحفظ هنا.

**تعتمد على:** 7 (المصادقة: التسجيل بمرحلتين والدخول والخروج)، 6 (مكوّنات الواجهة المشتركة)  
**تعتمد عليها لاحقاً:** 9 (النشر على الخادم التجريبي)، 11 (شاشات نسيت كلمة المرور وكلمة المرور الجديدة)، 48 (حذف الحساب)

```text
[Quasario · Step 8/61 · Frontend · UC-U01 U02 U03 A01 A02]
Project: Quasario, a local services directory (Laravel REST API + React SPA + admin panel). The full
context is in CLAUDE.md: read it before anything else.
Feature: Sign up and sign in screens.
Done before this step: step 7 'Authentication API'; step 6 'Shared UI components'.
Already in the codebase (reuse it, do not recreate it): AuthProvider/useAuth, router guards, api client and error mapping (step 'Frontend architecture'); shared components (step 'Shared UI components'); auth endpoints (step 'Authentication API'); GET /filters is built later, so mock it until then.
Later steps build on this: step 9 'Staging deployment'; step 11 'Password reset screens'; step 48 'Delete account screen'.
So keep these stable and named exactly as written: api/endpoints/auth.ts (registerCheck, register, login, logout, me), the sign-in flow that stores token + user, and Settings screen with Log out. Later steps add more rows to Settings (Sections requests, Trust mark, Delete account).

Task: Auth screens. Attach the original Sign up / Sign in screens, S01, and screens 34/35 (sign-up step 2).
- /sign-up step 1: Email, Username, Phone, Password → POST /auth/register/check; field errors shown.
- Step 2 (same route, state in memory): Job category → Job title (filtered), City (from GET /filters; mocked until
  that endpoint exists), Experience (optional) → POST /auth/register → message "Account created, please sign in"
  → /sign-in. Back keeps step-1 values.
- /sign-in: "Email or username" + password; links "Forgot password?" and "Create account". Store token + user;
  admins → /admin, users → /.
- /settings (S06) with "Log out" (no confirmation). Signed-in users visiting guest pages go home.

How to work:
- Read CLAUDE.md first; use its conventions, colors, navigation and API contract exactly.
- Before coding, read the files listed under "Already in the codebase" and reuse them (shared components, api
  client, hooks, types). Then list the files you will create or change, and write them in full (TypeScript, Tailwind).
- API calls in src/api/endpoints/<module>.ts with types in src/api/types.ts (snake_case fields as the API returns
  them); TanStack Query hooks inside the feature folder; invalidate the related queries after each mutation.
- Forms: react-hook-form + zod with the same rules as the backend; show server field errors; map `code` to an
  English message through the shared error helper.
- Every screen has loading, empty and error states and works at 360px and 1280px.
- If the endpoint is not merged yet, mock it in the endpoint file behind `import.meta.env.VITE_USE_MOCKS` with the
  exact response shape from docs/api.md.
- Vitest tests for the zod schemas and one component test for the main screen.
- I attach the UI mockups named above; match their layout and texts.
```

<a id="step-9"></a>
### 9. النشر على الخادم التجريبي

**النوع:** DevOps · **المسؤول:** Nazeer + Syrien · **الحالات:** —

**ماذا تضيف للبرنامج:** يضع نسخة تعمل على الإنترنت من الأسبوع الثاني، فيجرب الفريق من الهاتف الحقيقي، وتظهر مشاكل الخادم مبكراً بدل ليلة التسليم. من هنا فصاعداً كل دمج في main يُنشر تلقائياً.

**تعتمد على:** 8 (شاشات الدخول والتسجيل بمرحلتين)  
**تعتمد عليها لاحقاً:** 60 (الإنتاج والنسخ الاحتياطي)

```text
[Quasario · Step 9/61 · DevOps · —]
Project: Quasario, a local services directory (Laravel REST API + React SPA + admin panel). The full
context is in CLAUDE.md: read it before anything else.
Feature: Staging deployment.
Done before this step: step 8 'Sign up and sign in screens'.
Already in the codebase (reuse it, do not recreate it): Working backend + frontend with authentication (steps up to 'Sign up and sign in screens').
Later steps build on this: step 60 'Production and backups'.
So keep these stable and named exactly as written: A staging URL, scripts/deploy.sh and the deploy-staging GitHub Actions job. Every Thursday demo runs on this server.

Task: Deploy to a staging Ubuntu server (I will give the server details).
- Nginx, PHP 8.3-FPM + extensions, MySQL 8, Composer, Node 22, Supervisor, certbot.
- One domain: Nginx serves frontend/dist with SPA fallback; /api and /storage go to Laravel public/. HTTPS.
- Laravel: staging .env, key:generate, migrate --seed, storage:link, permissions, config/route cache,
  Supervisor `queue:work`, cron `schedule:run`.
- scripts/deploy.sh (idempotent): git pull, composer install --no-dev, migrate --force, caches, npm ci, build.
- GitHub Actions deploy-staging on push to main using SSH secrets.
Give step-by-step commands and every config file.
```

<a id="step-10"></a>
### 10. استعادة كلمة المرور

**النوع:** Backend · **المسؤول:** Hossin · **الحالات:** UC-U02 (بديل)

**ماذا تضيف للبرنامج:** يحل مشكلة من نسي كلمة مروره دون تدخل المسؤول. نفس الرسالة دائماً حتى لا يعرف أحد أي بريد مسجّل (حماية للخصوصية كما في Narrative).

**تعتمد على:** 7 (المصادقة: التسجيل بمرحلتين والدخول والخروج)  
**تعتمد عليها لاحقاً:** 11 (شاشات نسيت كلمة المرور وكلمة المرور الجديدة)

```text
[Quasario · Step 10/61 · Backend · UC-U02 (بديل)]
Project: Quasario, a local services directory (Laravel REST API + React SPA + admin panel). The full
context is in CLAUDE.md: read it before anything else.
Feature: Password reset API.
Done before this step: step 7 'Authentication API'.
Already in the codebase (reuse it, do not recreate it): Users, auth endpoints and rate limiter `auth` (step 'Authentication API').
Later steps build on this: step 11 'Password reset screens'.
So keep these stable and named exactly as written: POST /auth/forgot-password and POST /auth/reset-password; reset link format {FRONTEND_URL}/reset-password?token=&email= used by the reset screen.

Task: Password reset with Laravel's password broker.
- POST /auth/forgot-password {email}: always 200 with "If this email is registered, a reset link has been sent.";
  email link {FRONTEND_URL}/reset-password?token=...&email=...
- POST /auth/reset-password {token, email, password}: password ≥ 8 (weakPassword); invalid/expired → 422
  invalidField. On success delete all the user's tokens. Works for users and admins.
Tests: identical responses for known/unknown email, valid reset, reused token rejected, expired token rejected.

How to work:
- Read CLAUDE.md first; use its table names, enums, error codes and API contract exactly.
- Before coding, read the files listed under "Already in the codebase" and reuse them. Then list the files you
  will create or change, and write them in full.
- Thin controllers; rules in Services; validation in FormRequests; ownership in Policies; output via API Resources.
- Pest feature tests in tests/Feature/<Module>/: success, every error listed above (assert the `code`), and
  rejection of a visitor, another user and a banned user where relevant.
- Do not change migrations or other modules unless this task says so. If you must touch a shared file, say why.
- Update docs/api.md with the endpoints (request, response example, error codes).
- Finish with: routes added, how to run the tests, example curl requests, and anything the frontend must know.
```

<a id="step-11"></a>
### 11. شاشات نسيت كلمة المرور وكلمة المرور الجديدة

**النوع:** Frontend · **المسؤول:** Abdulhadi · **الحالات:** UC-U02 (بديل)

**ماذا تضيف للبرنامج:** يكمل دورة الدخول، ويضيف الشاشة التي كانت ناقصة في التصميم (إدخال كلمة المرور الجديدة بعد فتح الرابط).

**تعتمد على:** 10 (استعادة كلمة المرور)، 8 (شاشات الدخول والتسجيل بمرحلتين)  
**تعتمد عليها لاحقاً:** لا شيء

```text
[Quasario · Step 11/61 · Frontend · UC-U02 (بديل)]
Project: Quasario, a local services directory (Laravel REST API + React SPA + admin panel). The full
context is in CLAUDE.md: read it before anything else.
Feature: Password reset screens.
Done before this step: step 10 'Password reset API'; step 8 'Sign up and sign in screens'.
Already in the codebase (reuse it, do not recreate it): Sign-in screen and auth endpoints file (step 'Sign up and sign in screens'); reset endpoints (step 'Password reset API').
Later steps build on this: none (end of a chain).
So keep these stable and named exactly as written: forgotPassword/resetPassword in api/endpoints/auth.ts.

Task: Password reset screens. Attach S01–S03.
- /forgot-password: email → POST /auth/forgot-password → always the confirmation screen (S03).
- /reset-password?token&email: new password + confirm (≥ 8, must match) → POST /auth/reset-password → success +
  link to Sign in. No mockup exists: same style as S02. Invalid/expired token → message + link to request again.

How to work:
- Read CLAUDE.md first; use its conventions, colors, navigation and API contract exactly.
- Before coding, read the files listed under "Already in the codebase" and reuse them (shared components, api
  client, hooks, types). Then list the files you will create or change, and write them in full (TypeScript, Tailwind).
- API calls in src/api/endpoints/<module>.ts with types in src/api/types.ts (snake_case fields as the API returns
  them); TanStack Query hooks inside the feature folder; invalidate the related queries after each mutation.
- Forms: react-hook-form + zod with the same rules as the backend; show server field errors; map `code` to an
  English message through the shared error helper.
- Every screen has loading, empty and error states and works at 360px and 1280px.
- If the endpoint is not merged yet, mock it in the endpoint file behind `import.meta.env.VITE_USE_MOCKS` with the
  exact response shape from docs/api.md.
- Vitest tests for the zod schemas and one component test for the main screen.
- I attach the UI mockups named above; match their layout and texts.
```

<a id="step-12"></a>
### 12. تجربة الخريطة ومكوّناتها المشتركة

**النوع:** Frontend · **المسؤول:** Batol · **الحالات:** UC-V06 V07 U25 U26 VU02 V10

**ماذا تضيف للبرنامج:** الخريطة أخطر جزء في الواجهة لأنها خدمة خارجية. نجربها مبكراً ونصنع منها ثلاثة مكوّنات تعيد استخدامها ست حالات لاحقاً: موقع الصفحة والخريطة التفاعلية وموقع الفعالية.

**تعتمد على:** 5 (هيكل الواجهة: التوجيه والتخطيط وعميل الـAPI)  
**تعتمد عليها لاحقاً:** 36 (موقع الصفحة والخريطة التفاعلية)، 40 (شاشات الفعاليات العامة)

```text
[Quasario · Step 12/61 · Frontend · UC-V06 V07 U25 U26 VU02 V10]
Project: Quasario, a local services directory (Laravel REST API + React SPA + admin panel). The full
context is in CLAUDE.md: read it before anything else.
Feature: Map components spike.
Done before this step: step 5 'Frontend architecture'.
Already in the codebase (reuse it, do not recreate it): Layouts and useGeolocation location (step 'Frontend architecture').
Later steps build on this: step 36 'Page location and interactive map screens'; step 40 'Public events screens'.
So keep these stable and named exactly as written: components/map/MapView, MapPicker, LocationCard and hooks/useGeolocation, plus docs/decisions.md notes. Used later by page location, interactive map, event creation/editing and event details.

Task: Spike + reusable map components with react-leaflet and OpenStreetMap tiles (no API key).
- MapView: props center, zoom, markers [{id, lat, lng, title, onClick}], fitBounds option.
- MapPicker: controlled {latitude, longitude}; click places/moves one pin; "Use my location" via
  hooks/useGeolocation (handles denied permission); default center Damascus (33.5138, 36.2765).
- LocationCard (S14): name, job, floor label, directions, place photo, "View page".
- Fix Leaflet's default marker icon with Vite; OSM attribution. Dev-only /dev/map page. Findings in docs/decisions.md.

How to work:
- Read CLAUDE.md first; use its conventions, colors, navigation and API contract exactly.
- Before coding, read the files listed under "Already in the codebase" and reuse them (shared components, api
  client, hooks, types). Then list the files you will create or change, and write them in full (TypeScript, Tailwind).
- API calls in src/api/endpoints/<module>.ts with types in src/api/types.ts (snake_case fields as the API returns
  them); TanStack Query hooks inside the feature folder; invalidate the related queries after each mutation.
- Forms: react-hook-form + zod with the same rules as the backend; show server field errors; map `code` to an
  English message through the shared error helper.
- Every screen has loading, empty and error states and works at 360px and 1280px.
- If the endpoint is not merged yet, mock it in the endpoint file behind `import.meta.env.VITE_USE_MOCKS` with the
  exact response shape from docs/api.md.
- Vitest tests for the zod schemas and one component test for the main screen.
- I attach the UI mockups named above; match their layout and texts.
```

<a id="step-13"></a>
### 13. تجربة الذكاء الاصطناعي

**النوع:** Backend · **المسؤول:** Hossin · **الحالات:** UC-V08

**ماذا تضيف للبرنامج:** الذكاء الاصطناعي أكثر ميزة قد تفشل لأسباب خارجة عنا (توفر الخدمة والدفع). نعرف في الأسبوع الأول هل تعمل، ونبني بديلاً احتياطياً يضمن أن المساعد يعمل في كل الأحوال.

**تعتمد على:** 3 (بيانات أولية: Seeders وFactories)  
**تعتمد عليها لاحقاً:** 30 (المساعد الذكي نسخة أولى (بالبديل الاحتياطي))

```text
[Quasario · Step 13/61 · Backend · UC-V08]
Project: Quasario, a local services directory (Laravel REST API + React SPA + admin panel). The full
context is in CLAUDE.md: read it before anything else.
Feature: AI assistant spike.
Done before this step: step 3 'Seeders and factories'.
Already in the codebase (reuse it, do not recreate it): Seeded job titles (step 'Seeders and factories').
Later steps build on this: step 30 'Assistant endpoint v0'.
So keep these stable and named exactly as written: Services/Assistant: AiProvider interface, KeywordAiProvider, HttpAiProvider, AssistantService::suggest(problem): {reply, job_title_ids, provider}. The assistant endpoint later calls only AssistantService.

Task: Spike for the AI assistant in backend/app/Services/Assistant.
- Interface AiProvider { suggest(string $problem): AiSuggestion } where AiSuggestion = {reply: ?string,
  job_title_ids: int[]} using ids from job_titles.
- KeywordAiProvider: always works; config/assistant.php maps job titles to keywords in English and Arabic
  (e.g. Plumber: leak, pipe, sink, تسريب, مواسير).
- HttpAiProvider: external LLM HTTP API from .env (AI_PROVIDER_URL, AI_API_KEY, AI_MODEL). Prompt with the list of
  job titles (id + name) and the problem; require strict JSON {"job_title_ids":[...],"reply":"..."}; validate ids
  exist; timeout 15s.
- AssistantService: Http when configured, Keyword on any failure; returns which provider answered.
- Command `assistant:try "my sink is leaking"`. Write findings (provider, cost, reachable from our server) in
  docs/decisions.md.

How to work:
- Read CLAUDE.md first; use its table names, enums, error codes and API contract exactly.
- Before coding, read the files listed under "Already in the codebase" and reuse them. Then list the files you
  will create or change, and write them in full.
- Thin controllers; rules in Services; validation in FormRequests; ownership in Policies; output via API Resources.
- Pest feature tests in tests/Feature/<Module>/: success, every error listed above (assert the `code`), and
  rejection of a visitor, another user and a banned user where relevant.
- Do not change migrations or other modules unless this task says so. If you must touch a shared file, say why.
- Update docs/api.md with the endpoints (request, response example, error codes).
- Finish with: routes added, how to run the tests, example curl requests, and anything the frontend must know.
```

## Sprint 2: قلب الدليل (8/11 – 26/11)

<a id="step-14"></a>
### 14. عرض الصفحة والقوائم الثابتة

**النوع:** Backend · **المسؤول:** Nazeer · **الحالات:** UC-V03 V04

**ماذا تضيف للبرنامج:** قلب الدليل: عرض صفحة مقدم الخدمة بكل محتواها. نفس الـResource سيعرض لاحقاً الأقسام والتقييمات والموقع، فبناؤه بشكل صحيح من البداية يوفر تعديلات كثيرة.

**تعتمد على:** 7 (المصادقة: التسجيل بمرحلتين والدخول والخروج)  
**تعتمد عليها لاحقاً:** 15 (الصفحة الرئيسية والبحث والتصفية)، 17 (صفحة العرض وعارض صور المعرض)، 18 (تعديل الملف الشخصي)، 20 (روابط التواصل الاجتماعي)، 22 (المفضلة)، 26 (الأقسام: إنشاء وتعديل وحذف)، 43 (البلاغات (جهة المستخدم))، 45 (المحادثة)

```text
[Quasario · Step 14/61 · Backend · UC-V03 V04]
Project: Quasario, a local services directory (Laravel REST API + React SPA + admin panel). The full
context is in CLAUDE.md: read it before anything else.
Feature: Page read API and filter options.
Done before this step: step 7 'Authentication API'.
Already in the codebase (reuse it, do not recreate it): Page model with withAverageRating() and visible() (step 'Database'), seeded data, route groups.
Later steps build on this: step 15 'Home, search and filter API'; step 17 'Page view screen'; step 18 'Profile edit API'; step 20 'Social links API'; step 22 'Saved pages API'; step 26 'Sections API'; step 43 'Reports API (users)'; step 45 'Chat API'.
So keep these stable and named exactly as written: GET /filters (cities, job categories with titles, social platforms, floors), GET /pages/{page}, GET /me/page, PageResource and PageCardResource. Gallery, links, sections, endorsements and location steps only ADD data into PageResource; the cards are reused by home, search, saved, map and assistant.

Task: Page read endpoints.
- GET /filters: cities, job categories with titles, social platforms, floors (cache 1 hour).
- PageCardResource: id, display_name, job_title {id,name}, city {id,name}, profile_image_url, image_url (cover or
  first gallery image), is_verified, average_rating (null if none), endorsements_count.
- PageResource: card fields + job_category, cover_image_url, bio, study, experience, owner_user_id,
  gallery [], social_links [], location (null), sections [], endorsements [] (empty arrays now, filled by later
  steps), and for a signed-in viewer is_saved and is_owner.
- GET /pages/{page}: public; 404 notFound if owner banned (admins can see). GET /me/page: my page.
- Eager-load; a test asserts query count ≤ 12.

How to work:
- Read CLAUDE.md first; use its table names, enums, error codes and API contract exactly.
- Before coding, read the files listed under "Already in the codebase" and reuse them. Then list the files you
  will create or change, and write them in full.
- Thin controllers; rules in Services; validation in FormRequests; ownership in Policies; output via API Resources.
- Pest feature tests in tests/Feature/<Module>/: success, every error listed above (assert the `code`), and
  rejection of a visitor, another user and a banned user where relevant.
- Do not change migrations or other modules unless this task says so. If you must touch a shared file, say why.
- Update docs/api.md with the endpoints (request, response example, error codes).
- Finish with: routes added, how to run the tests, example curl requests, and anything the frontend must know.
```

<a id="step-15"></a>
### 15. الصفحة الرئيسية والبحث والتصفية

**النوع:** Backend · **المسؤول:** Nazeer · **الحالات:** UC-V01 V02

**ماذا تضيف للبرنامج:** هذا ما يجعل Quasario دليلاً: الزائر يجد مقدم الخدمة بالاسم أو المهنة أو المدينة. ترتيب الصفحات حسب التقييم الموثّق يكافئ من يقدّم خدمة جيدة.

**تعتمد على:** 14 (عرض الصفحة والقوائم الثابتة)  
**تعتمد عليها لاحقاً:** 16 (الصفحة الرئيسية والبحث والتصفية)، 30 (المساعد الذكي نسخة أولى (بالبديل الاحتياطي))

```text
[Quasario · Step 15/61 · Backend · UC-V01 V02]
Project: Quasario, a local services directory (Laravel REST API + React SPA + admin panel). The full
context is in CLAUDE.md: read it before anything else.
Feature: Home, search and filter API.
Done before this step: step 14 'Page read API and filter options'.
Already in the codebase (reuse it, do not recreate it): PageCardResource, Page::visible() and withAverageRating() (steps 'Database' and 'Page read API').
Later steps build on this: step 16 'Home, search and filter screens'; step 30 'Assistant endpoint v0'.
So keep these stable and named exactly as written: GET /home and GET /pages?q=&cities[]=&jobs[]= returning paginated PageCardResource. The home/search screens and the assistant reuse the same sorting.

Task: Home and search.
- GET /home and GET /pages?q=&cities[]=&jobs[]=&page=: visible pages only, paginated 20, PageCardResource.
- Sort: average verified rating desc (nulls last), then newest page. Use withAverageRating() (SQL, not PHP).
- q: display_name or job title name (LIKE). cities[] and jobs[] (job title ids) accept many values.
- Add indexes in a new migration if needed.
Tests: ordering, search by name and job, multi-city/multi-job filter, banned owner hidden.

How to work:
- Read CLAUDE.md first; use its table names, enums, error codes and API contract exactly.
- Before coding, read the files listed under "Already in the codebase" and reuse them. Then list the files you
  will create or change, and write them in full.
- Thin controllers; rules in Services; validation in FormRequests; ownership in Policies; output via API Resources.
- Pest feature tests in tests/Feature/<Module>/: success, every error listed above (assert the `code`), and
  rejection of a visitor, another user and a banned user where relevant.
- Do not change migrations or other modules unless this task says so. If you must touch a shared file, say why.
- Update docs/api.md with the endpoints (request, response example, error codes).
- Finish with: routes added, how to run the tests, example curl requests, and anything the frontend must know.
```

<a id="step-16"></a>
### 16. الصفحة الرئيسية والبحث والتصفية

**النوع:** Frontend · **المسؤول:** Batol · **الحالات:** UC-V01 V02

**ماذا تضيف للبرنامج:** أول شاشة يراها الزائر. من هنا يبدأ استخدام الدليل فعلاً، وهي محور العرض النصفي.

**تعتمد على:** 15 (الصفحة الرئيسية والبحث والتصفية)، 6 (مكوّنات الواجهة المشتركة)  
**تعتمد عليها لاحقاً:** 17 (صفحة العرض وعارض صور المعرض)، 56 (شاشة المساعد الذكي)

```text
[Quasario · Step 16/61 · Frontend · UC-V01 V02]
Project: Quasario, a local services directory (Laravel REST API + React SPA + admin panel). The full
context is in CLAUDE.md: read it before anything else.
Feature: Home, search and filter screens.
Done before this step: step 15 'Home, search and filter API'; step 6 'Shared UI components'.
Already in the codebase (reuse it, do not recreate it): Layouts, shared components, LoadMore (steps 'Frontend architecture' and 'Shared UI components'); /home, /pages and /filters endpoints.
Later steps build on this: step 17 'Page view screen'; step 56 'Assistant screen'.
So keep these stable and named exactly as written: components/PageCard (reused by saved, map card list, assistant results), api/endpoints/pages.ts, useFilters() hook (reused by sign-up step 2 and events filter).

Task: Home + search + filter. Attach S04 and the original home, search and filter (21) screens.
- Home: search bar, filter button, PageCard grid/list (photo, name, job, city, verified badge, rating or "-"),
  LoadMore.
- Filter sheet: multi-select cities and job titles grouped by category (useFilters from GET /filters); Apply/Reset.
- Results in /search with state in the URL (?q=&cities=&jobs=) so refresh/back work. Card → /pages/:id.
- Replace the sign-up mock of GET /filters with useFilters().

How to work:
- Read CLAUDE.md first; use its conventions, colors, navigation and API contract exactly.
- Before coding, read the files listed under "Already in the codebase" and reuse them (shared components, api
  client, hooks, types). Then list the files you will create or change, and write them in full (TypeScript, Tailwind).
- API calls in src/api/endpoints/<module>.ts with types in src/api/types.ts (snake_case fields as the API returns
  them); TanStack Query hooks inside the feature folder; invalidate the related queries after each mutation.
- Forms: react-hook-form + zod with the same rules as the backend; show server field errors; map `code` to an
  English message through the shared error helper.
- Every screen has loading, empty and error states and works at 360px and 1280px.
- If the endpoint is not merged yet, mock it in the endpoint file behind `import.meta.env.VITE_USE_MOCKS` with the
  exact response shape from docs/api.md.
- Vitest tests for the zod schemas and one component test for the main screen.
- I attach the UI mockups named above; match their layout and texts.
```

<a id="step-17"></a>
### 17. صفحة العرض وعارض صور المعرض

**النوع:** Frontend · **المسؤول:** Batol · **الحالات:** UC-V03 V04

**ماذا تضيف للبرنامج:** الشاشة التي يحكم منها الزائر على مقدم الخدمة. صُممت كوعاء تُضاف إليه لاحقاً الأقسام والتقييمات والموقع والتبليغ والرسائل دون إعادة كتابة.

**تعتمد على:** 14 (عرض الصفحة والقوائم الثابتة)، 16 (الصفحة الرئيسية والبحث والتصفية)  
**تعتمد عليها لاحقاً:** 21 (تعديل الملف والمعرض والروابط)، 23 (شاشة المحفوظات وزر الحفظ)، 44 (زر ونموذج التبليغ)، 46 (شاشات المحادثة)

```text
[Quasario · Step 17/61 · Frontend · UC-V03 V04]
Project: Quasario, a local services directory (Laravel REST API + React SPA + admin panel). The full
context is in CLAUDE.md: read it before anything else.
Feature: Page view screen.
Done before this step: step 14 'Page read API and filter options'; step 16 'Home, search and filter screens'.
Already in the codebase (reuse it, do not recreate it): PageCard, shared components, useRequireAuth (SignInRequiredDialog), pages endpoints.
Later steps build on this: step 21 'Profile, gallery and links editing screens'; step 23 'Saved screen and Save button'; step 44 'Report dialog'; step 46 'Chat screens'.
So keep these stable and named exactly as written: pages/PageView.tsx with named slots/components: PageHeader, AboutSection, GalleryGrid + GalleryViewer, SocialLinks, LocationRow, SectionsList, EndorsementsList, PageActions (Save / Message / Report). Later steps fill these components; keep their names.

Task: Page view. Attach the original person/organization page screens and S20.
- PageHeader: cover, profile image, name, verified badge, job, city, rating.
- PageActions: Save (toggle), Message, Report; visitors get SignInRequiredDialog via useRequireAuth; the owner sees
  "Edit" instead. Message/Report are wired in later steps (leave handlers as TODO with the dialog for visitors).
- AboutSection (bio, study, experience), GalleryGrid → full-screen GalleryViewer with description and arrows,
  SocialLinks (icon per platform), LocationRow, SectionsList, EndorsementsList: render data when present, hide
  when empty.
- 404 → "Page not found".

How to work:
- Read CLAUDE.md first; use its conventions, colors, navigation and API contract exactly.
- Before coding, read the files listed under "Already in the codebase" and reuse them (shared components, api
  client, hooks, types). Then list the files you will create or change, and write them in full (TypeScript, Tailwind).
- API calls in src/api/endpoints/<module>.ts with types in src/api/types.ts (snake_case fields as the API returns
  them); TanStack Query hooks inside the feature folder; invalidate the related queries after each mutation.
- Forms: react-hook-form + zod with the same rules as the backend; show server field errors; map `code` to an
  English message through the shared error helper.
- Every screen has loading, empty and error states and works at 360px and 1280px.
- If the endpoint is not merged yet, mock it in the endpoint file behind `import.meta.env.VITE_USE_MOCKS` with the
  exact response shape from docs/api.md.
- Vitest tests for the zod schemas and one component test for the main screen.
- I attach the UI mockups named above; match their layout and texts.
```

<a id="step-18"></a>
### 18. تعديل الملف الشخصي

**النوع:** Backend · **المسؤول:** Nazeer · **الحالات:** UC-U04

**ماذا تضيف للبرنامج:** يجعل صاحب الصفحة يقدّم نفسه بشكل كامل (صور ونبذة ودراسة وخبرة ومدينة). جودة الصفحات هي جودة الدليل.

**تعتمد على:** 14 (عرض الصفحة والقوائم الثابتة)  
**تعتمد عليها لاحقاً:** 19 (معرض الأعمال)، 21 (تعديل الملف والمعرض والروابط)، 35 (موقع الصفحة وخريطة الصفحات)، 37 (الفعاليات: جهة المستخدم الموثّق)

```text
[Quasario · Step 18/61 · Backend · UC-U04]
Project: Quasario, a local services directory (Laravel REST API + React SPA + admin panel). The full
context is in CLAUDE.md: read it before anything else.
Feature: Profile edit API.
Done before this step: step 14 'Page read API and filter options'.
Already in the codebase (reuse it, do not recreate it): Page model, file storage config, PageResource (step 'Page read API').
Later steps build on this: step 19 'Gallery API'; step 21 'Profile, gallery and links editing screens'; step 35 'Page location and map API'; step 37 'Verified user events API'.
So keep these stable and named exactly as written: GET /me/profile and POST /me/profile (partial multipart update), and an ImageStorage service (store/replace/delete public images) reused by gallery, location, events.

Task: Profile edit.
- app/Services/ImageStorage.php: storePublic(UploadedFile, dir), replace(oldPath, file, dir), delete(path);
  used by every later upload.
- GET /me/profile: display_name, username, email, phone, job_category_id, job_title_id, city_id, bio, study,
  experience, profile_image_url, cover_image_url.
- POST /me/profile (multipart, `sometimes` rules: only sent fields change): display_name 1–100; username/email
  unique except self (alreadyUsed); phone format; job title in category; profile_image/cover_image JPG/PNG ≤5MB
  (unsupportedFile), replacing deletes the old file; password change needs current_password (invalidPassword) and
  password ≥ 8 (weakPassword). "Location" in the UI = city_id.
Tests for each rule.

How to work:
- Read CLAUDE.md first; use its table names, enums, error codes and API contract exactly.
- Before coding, read the files listed under "Already in the codebase" and reuse them. Then list the files you
  will create or change, and write them in full.
- Thin controllers; rules in Services; validation in FormRequests; ownership in Policies; output via API Resources.
- Pest feature tests in tests/Feature/<Module>/: success, every error listed above (assert the `code`), and
  rejection of a visitor, another user and a banned user where relevant.
- Do not change migrations or other modules unless this task says so. If you must touch a shared file, say why.
- Update docs/api.md with the endpoints (request, response example, error codes).
- Finish with: routes added, how to run the tests, example curl requests, and anything the frontend must know.
```

<a id="step-19"></a>
### 19. معرض الأعمال

**النوع:** Backend · **المسؤول:** Nazeer · **الحالات:** UC-U21 U22

**ماذا تضيف للبرنامج:** المعرض دليل عملي على جودة العمل، وهو أيضاً مصدر الصور التي تُرفق بطلبات التقييم الموثّق لاحقاً.

**تعتمد على:** 18 (تعديل الملف الشخصي)  
**تعتمد عليها لاحقاً:** 21 (تعديل الملف والمعرض والروابط)، 31 (التقييمات الموثّقة)

```text
[Quasario · Step 19/61 · Backend · UC-U21 U22]
Project: Quasario, a local services directory (Laravel REST API + React SPA + admin panel). The full
context is in CLAUDE.md: read it before anything else.
Feature: Gallery API.
Done before this step: step 18 'Profile edit API'.
Already in the codebase (reuse it, do not recreate it): ImageStorage (step 'Profile edit API'), GalleryImage model, PageResource gallery field.
Later steps build on this: step 21 'Profile, gallery and links editing screens'; step 31 'Endorsements API'.
So keep these stable and named exactly as written: POST /me/gallery, DELETE /me/gallery/{image}, GalleryImageResource {id, image_url, description}, GalleryImagePolicy. Endorsement requests later attach these ids.

Task: Gallery.
- POST /me/gallery (multipart image, description?): JPG/PNG ≤5MB (unsupportedFile), description ≤1000; stored under
  gallery/{page_id} through ImageStorage.
- DELETE /me/gallery/{image}: GalleryImagePolicy owner only (403); delete file; pivot rows cascade.
- Fill PageResource.gallery.
Tests: upload, wrong type, too big, owner delete, other user 403, file removed.

How to work:
- Read CLAUDE.md first; use its table names, enums, error codes and API contract exactly.
- Before coding, read the files listed under "Already in the codebase" and reuse them. Then list the files you
  will create or change, and write them in full.
- Thin controllers; rules in Services; validation in FormRequests; ownership in Policies; output via API Resources.
- Pest feature tests in tests/Feature/<Module>/: success, every error listed above (assert the `code`), and
  rejection of a visitor, another user and a banned user where relevant.
- Do not change migrations or other modules unless this task says so. If you must touch a shared file, say why.
- Update docs/api.md with the endpoints (request, response example, error codes).
- Finish with: routes added, how to run the tests, example curl requests, and anything the frontend must know.
```

<a id="step-20"></a>
### 20. روابط التواصل الاجتماعي

**النوع:** Backend · **المسؤول:** Nazeer · **الحالات:** UC-U23 U24

**ماذا تضيف للبرنامج:** يصل الزائر لمقدم الخدمة على المنصة التي يفضلها (واتساب وفيسبوك ولينكدإن...).

**تعتمد على:** 14 (عرض الصفحة والقوائم الثابتة)  
**تعتمد عليها لاحقاً:** 21 (تعديل الملف والمعرض والروابط)

```text
[Quasario · Step 20/61 · Backend · UC-U23 U24]
Project: Quasario, a local services directory (Laravel REST API + React SPA + admin panel). The full
context is in CLAUDE.md: read it before anything else.
Feature: Social links API.
Done before this step: step 14 'Page read API and filter options'.
Already in the codebase (reuse it, do not recreate it): SocialPlatform enum, PageResource social_links field.
Later steps build on this: step 21 'Profile, gallery and links editing screens'.
So keep these stable and named exactly as written: POST /me/social-links, DELETE /me/social-links/{link}, SocialLinkResource.

Task: Social links.
- POST /me/social-links {platform, url}: platform in SocialPlatform (invalidField); url http(s) ≤2048 (invalidField),
  WHATSAPP accepts https://wa.me/...; one per platform per page → 422 alreadyUsed.
- DELETE /me/social-links/{link}: owner only. Fill PageResource.social_links.
Tests for each rule.

How to work:
- Read CLAUDE.md first; use its table names, enums, error codes and API contract exactly.
- Before coding, read the files listed under "Already in the codebase" and reuse them. Then list the files you
  will create or change, and write them in full.
- Thin controllers; rules in Services; validation in FormRequests; ownership in Policies; output via API Resources.
- Pest feature tests in tests/Feature/<Module>/: success, every error listed above (assert the `code`), and
  rejection of a visitor, another user and a banned user where relevant.
- Do not change migrations or other modules unless this task says so. If you must touch a shared file, say why.
- Update docs/api.md with the endpoints (request, response example, error codes).
- Finish with: routes added, how to run the tests, example curl requests, and anything the frontend must know.
```

<a id="step-21"></a>
### 21. تعديل الملف والمعرض والروابط

**النوع:** Frontend · **المسؤول:** Batol · **الحالات:** UC-U04 U21–U24

**ماذا تضيف للبرنامج:** يعطي صاحب الصفحة أدوات بناء صفحته من الهاتف. بعد هذه الخطوة يصبح الدليل قابلاً للتعبئة من المستخدمين أنفسهم.

**تعتمد على:** 18 (تعديل الملف الشخصي)، 19 (معرض الأعمال)، 20 (روابط التواصل الاجتماعي)، 17 (صفحة العرض وعارض صور المعرض)  
**تعتمد عليها لاحقاً:** 28 (شاشات الأقسام)، 36 (موقع الصفحة والخريطة التفاعلية)

```text
[Quasario · Step 21/61 · Frontend · UC-U04 U21–U24]
Project: Quasario, a local services directory (Laravel REST API + React SPA + admin panel). The full
context is in CLAUDE.md: read it before anything else.
Feature: Profile, gallery and links editing screens.
Done before this step: step 18 'Profile edit API'; step 19 'Gallery API'; step 20 'Social links API'; step 17 'Page view screen'.
Already in the codebase (reuse it, do not recreate it): PageView components (step 'Page view screen'), ImageUpload, useConfirm, profile/gallery/links endpoints.
Later steps build on this: step 28 'Sections screens'; step 36 'Page location and interactive map screens'.
So keep these stable and named exactly as written: /profile (my page = PageView in owner mode with edit controls) and /profile/edit. Later owner tools (location, sections, endorsements) are added to owner mode the same way.

Task: Owner editing. Attach S08, S09, S10, S11.
- /profile/edit (S08): one form, one Save that sends only changed fields (multipart when an image changed); password
  part (current/new); city labeled "Location"; category → title.
- /profile: PageView with isOwner controls: "+" on GalleryGrid (image + description), delete per image
  (useConfirm), SocialLinks "Add link" (fixed platform list from useFilters, URL) and delete.
- Invalidate ['page', id] and ['me','page'] after changes.

How to work:
- Read CLAUDE.md first; use its conventions, colors, navigation and API contract exactly.
- Before coding, read the files listed under "Already in the codebase" and reuse them (shared components, api
  client, hooks, types). Then list the files you will create or change, and write them in full (TypeScript, Tailwind).
- API calls in src/api/endpoints/<module>.ts with types in src/api/types.ts (snake_case fields as the API returns
  them); TanStack Query hooks inside the feature folder; invalidate the related queries after each mutation.
- Forms: react-hook-form + zod with the same rules as the backend; show server field errors; map `code` to an
  English message through the shared error helper.
- Every screen has loading, empty and error states and works at 360px and 1280px.
- If the endpoint is not merged yet, mock it in the endpoint file behind `import.meta.env.VITE_USE_MOCKS` with the
  exact response shape from docs/api.md.
- Vitest tests for the zod schemas and one component test for the main screen.
- I attach the UI mockups named above; match their layout and texts.
```

<a id="step-22"></a>
### 22. المفضلة

**النوع:** Backend · **المسؤول:** Nazeer · **الحالات:** UC-U06 U07 U08

**ماذا تضيف للبرنامج:** يرجع المستخدم بسهولة لمقدمي الخدمة الذين أعجبوه، وهو سبب إضافي لإنشاء حساب.

**تعتمد على:** 14 (عرض الصفحة والقوائم الثابتة)  
**تعتمد عليها لاحقاً:** 23 (شاشة المحفوظات وزر الحفظ)

```text
[Quasario · Step 22/61 · Backend · UC-U06 U07 U08]
Project: Quasario, a local services directory (Laravel REST API + React SPA + admin panel). The full
context is in CLAUDE.md: read it before anything else.
Feature: Saved pages API.
Done before this step: step 14 'Page read API and filter options'.
Already in the codebase (reuse it, do not recreate it): PageCardResource and is_saved in PageResource.
Later steps build on this: step 23 'Saved screen and Save button'.
So keep these stable and named exactly as written: POST/DELETE /pages/{page}/save and GET /me/saved-pages.

Task: Saved pages.
- POST /pages/{page}/save: idempotent (second press does nothing, 200). Saving your own page is allowed.
- DELETE /pages/{page}/save; GET /me/saved-pages (PageCardResource, newest saved first, visible pages only).
Tests: idempotency, removal, visitor 401, banned owner's page not listed.

How to work:
- Read CLAUDE.md first; use its table names, enums, error codes and API contract exactly.
- Before coding, read the files listed under "Already in the codebase" and reuse them. Then list the files you
  will create or change, and write them in full.
- Thin controllers; rules in Services; validation in FormRequests; ownership in Policies; output via API Resources.
- Pest feature tests in tests/Feature/<Module>/: success, every error listed above (assert the `code`), and
  rejection of a visitor, another user and a banned user where relevant.
- Do not change migrations or other modules unless this task says so. If you must touch a shared file, say why.
- Update docs/api.md with the endpoints (request, response example, error codes).
- Finish with: routes added, how to run the tests, example curl requests, and anything the frontend must know.
```

<a id="step-23"></a>
### 23. شاشة المحفوظات وزر الحفظ

**النوع:** Frontend · **المسؤول:** Batol · **الحالات:** UC-U06 U07 U08

**ماذا تضيف للبرنامج:** يُكمل تجربة المستخدم المسجّل الأساسية: يبحث ويفتح ويحفظ ويعود.

**تعتمد على:** 22 (المفضلة)، 17 (صفحة العرض وعارض صور المعرض)  
**تعتمد عليها لاحقاً:** لا شيء

```text
[Quasario · Step 23/61 · Frontend · UC-U06 U07 U08]
Project: Quasario, a local services directory (Laravel REST API + React SPA + admin panel). The full
context is in CLAUDE.md: read it before anything else.
Feature: Saved screen and Save button.
Done before this step: step 22 'Saved pages API'; step 17 'Page view screen'.
Already in the codebase (reuse it, do not recreate it): PageActions Save button (step 'Page view screen'), PageCard, saved endpoints.
Later steps build on this: none (end of a chain).
So keep these stable and named exactly as written: useSavePage() hook.

Task: Wire PageActions Save to POST/DELETE using is_saved; /saved lists saved PageCards with "Remove" (direct,
no confirm as in the design); empty state "No saved pages".

How to work:
- Read CLAUDE.md first; use its conventions, colors, navigation and API contract exactly.
- Before coding, read the files listed under "Already in the codebase" and reuse them (shared components, api
  client, hooks, types). Then list the files you will create or change, and write them in full (TypeScript, Tailwind).
- API calls in src/api/endpoints/<module>.ts with types in src/api/types.ts (snake_case fields as the API returns
  them); TanStack Query hooks inside the feature folder; invalidate the related queries after each mutation.
- Forms: react-hook-form + zod with the same rules as the backend; show server field errors; map `code` to an
  English message through the shared error helper.
- Every screen has loading, empty and error states and works at 360px and 1280px.
- If the endpoint is not merged yet, mock it in the endpoint file behind `import.meta.env.VITE_USE_MOCKS` with the
  exact response shape from docs/api.md.
- Vitest tests for the zod schemas and one component test for the main screen.
- I attach the UI mockups named above; match their layout and texts.
```

<a id="step-24"></a>
### 24. بنية الإشعارات والبحث عن أشخاص

**النوع:** Backend · **المسؤول:** Hossin · **الحالات:** UC-U11

**ماذا تضيف للبرنامج:** 21 حالة في التصميم ترسل إشعاراً. نبني الخدمة مرة واحدة الآن فتستخدمها الأقسام والتقييمات والتوثيق والفعاليات والبلاغات والمحادثة. والبحث عن أشخاص تستخدمه الأقسام والتقييمات.

**تعتمد على:** 7 (المصادقة: التسجيل بمرحلتين والدخول والخروج)  
**تعتمد عليها لاحقاً:** 25 (شاشة الإشعارات)، 26 (الأقسام: إنشاء وتعديل وحذف)، 31 (التقييمات الموثّقة)، 37 (الفعاليات: جهة المستخدم الموثّق)، 45 (المحادثة)

```text
[Quasario · Step 24/61 · Backend · UC-U11]
Project: Quasario, a local services directory (Laravel REST API + React SPA + admin panel). The full
context is in CLAUDE.md: read it before anything else.
Feature: Notifications service and people search.
Done before this step: step 7 'Authentication API'.
Already in the codebase (reuse it, do not recreate it): Notification model and NotificationType enum (step 'Database'), route groups.
Later steps build on this: step 25 'Notifications screen'; step 26 'Sections API'; step 31 'Endorsements API'; step 37 'Verified user events API'; step 45 'Chat API'.
So keep these stable and named exactly as written: NotificationService::send(User, NotificationType, array data), GET /notifications, POST /notifications/read, GET /people?name= (PersonResource {user_id, page_id, display_name, username, job_title, profile_image_url}). Every later step that notifies calls only NotificationService; the data keys documented in docs/api.md must be used as written.

Task: Notifications infrastructure + people search.
- NotificationService::send(User $to, NotificationType $type, array $data): row in our custom table. Define and
  document in docs/api.md the data keys per type (e.g. actor_name, actor_page_id, section_id, section_name,
  request_id, event_id, event_name, conversation_id). Never throws for a missing receiver.
- GET /notifications: mine, newest first, paginated, meta.unread_count. POST /notifications/read {ids?} (none = all).
- GET /people?name=: active users by display name or username, excluding self, limit 20, PersonResource.
Tests: send/list/read, isolation, people search excludes self and banned.

How to work:
- Read CLAUDE.md first; use its table names, enums, error codes and API contract exactly.
- Before coding, read the files listed under "Already in the codebase" and reuse them. Then list the files you
  will create or change, and write them in full.
- Thin controllers; rules in Services; validation in FormRequests; ownership in Policies; output via API Resources.
- Pest feature tests in tests/Feature/<Module>/: success, every error listed above (assert the `code`), and
  rejection of a visitor, another user and a banned user where relevant.
- Do not change migrations or other modules unless this task says so. If you must touch a shared file, say why.
- Update docs/api.md with the endpoints (request, response example, error codes).
- Finish with: routes added, how to run the tests, example curl requests, and anything the frontend must know.
```

<a id="step-25"></a>
### 25. شاشة الإشعارات

**النوع:** Frontend · **المسؤول:** Abdulhadi · **الحالات:** UC-U11

**ماذا تضيف للبرنامج:** يعرف المستخدم ما يحدث على حسابه (دعوة، تقييم، قبول فعالية...) دون أن يبحث بنفسه. هنا تُعرّف جملة كل نوع إشعار والشاشة التي يفتحها.

**تعتمد على:** 24 (بنية الإشعارات والبحث عن أشخاص)، 6 (مكوّنات الواجهة المشتركة)  
**تعتمد عليها لاحقاً:** 29 (شاشة دعوات الأقسام)

```text
[Quasario · Step 25/61 · Frontend · UC-U11]
Project: Quasario, a local services directory (Laravel REST API + React SPA + admin panel). The full
context is in CLAUDE.md: read it before anything else.
Feature: Notifications screen.
Done before this step: step 24 'Notifications service and people search'; step 6 'Shared UI components'.
Already in the codebase (reuse it, do not recreate it): Header in MainLayout, shared components, notifications endpoints.
Later steps build on this: step 29 'Section requests screen'.
So keep these stable and named exactly as written: NotificationBell (unread badge) and lib/notifications.ts: per NotificationType {text(data), link(data)}. Later steps only add new link targets if routes change.

Task: Notifications. Bell with unread count (refetch 60s) in the header; /notifications list with icon and an
English sentence per type from lib/notifications.ts (e.g. "Ahmad invited you to the section Team"), unread
highlighted, click → related screen (page, /sections/requests, /endorsements, event, chat); "Mark all as read".

How to work:
- Read CLAUDE.md first; use its conventions, colors, navigation and API contract exactly.
- Before coding, read the files listed under "Already in the codebase" and reuse them (shared components, api
  client, hooks, types). Then list the files you will create or change, and write them in full (TypeScript, Tailwind).
- API calls in src/api/endpoints/<module>.ts with types in src/api/types.ts (snake_case fields as the API returns
  them); TanStack Query hooks inside the feature folder; invalidate the related queries after each mutation.
- Forms: react-hook-form + zod with the same rules as the backend; show server field errors; map `code` to an
  English message through the shared error helper.
- Every screen has loading, empty and error states and works at 360px and 1280px.
- If the endpoint is not merged yet, mock it in the endpoint file behind `import.meta.env.VITE_USE_MOCKS` with the
  exact response shape from docs/api.md.
- Vitest tests for the zod schemas and one component test for the main screen.
- I attach the UI mockups named above; match their layout and texts.
```

<a id="step-26"></a>
### 26. الأقسام: إنشاء وتعديل وحذف

**النوع:** Backend · **المسؤول:** Hossin · **الحالات:** UC-U27 U28 U29 U30

**ماذا تضيف للبرنامج:** تسمح للمؤسسات (مثل المستشفى) بعرض فريقها ومعلومات تواصل الأقسام، فتصبح الصفحة صالحة للأفراد والمؤسسات معاً.

**تعتمد على:** 24 (بنية الإشعارات والبحث عن أشخاص)، 14 (عرض الصفحة والقوائم الثابتة)  
**تعتمد عليها لاحقاً:** 27 (دعوات الأقسام والعضوية)، 28 (شاشات الأقسام)

```text
[Quasario · Step 26/61 · Backend · UC-U27 U28 U29 U30]
Project: Quasario, a local services directory (Laravel REST API + React SPA + admin panel). The full
context is in CLAUDE.md: read it before anything else.
Feature: Sections API.
Done before this step: step 24 'Notifications service and people search'; step 14 'Page read API and filter options'.
Already in the codebase (reuse it, do not recreate it): NotificationService and people search (step 'Notifications service'), Section models, PageResource sections field.
Later steps build on this: step 27 'Section invitations API'; step 28 'Sections screens'.
So keep these stable and named exactly as written: POST /me/sections/members, POST /me/sections/info, PATCH/DELETE /me/sections/{section}, SectionPolicy, SectionResource {id, name, type, phone, email, members[PersonResource]}. Invitations reuse SectionPolicy and the invitation creation logic (SectionService::invite).

Task: Sections.
- SectionService::invite(Section, User) (shared with the next step): members sections only; not self
  (selfSelection); not already member and no pending invitation (409 alreadyRequested); creates pending invitation
  and notifies SECTION_INVITATION.
- POST /me/sections/members {name, member_ids[]}: name ≤100 required; calls invite() for each id (members are
  invited, never added directly).
- POST /me/sections/info {name, phone, email}: all required, valid formats.
- PATCH /me/sections/{section} {name, phone?, email?}: type immutable; phone/email only for info.
- DELETE /me/sections/{section}: notify members SECTION_DELETED, then delete.
- Fill PageResource.sections.
Tests for each rule and notification.

How to work:
- Read CLAUDE.md first; use its table names, enums, error codes and API contract exactly.
- Before coding, read the files listed under "Already in the codebase" and reuse them. Then list the files you
  will create or change, and write them in full.
- Thin controllers; rules in Services; validation in FormRequests; ownership in Policies; output via API Resources.
- Pest feature tests in tests/Feature/<Module>/: success, every error listed above (assert the `code`), and
  rejection of a visitor, another user and a banned user where relevant.
- Do not change migrations or other modules unless this task says so. If you must touch a shared file, say why.
- Update docs/api.md with the endpoints (request, response example, error codes).
- Finish with: routes added, how to run the tests, example curl requests, and anything the frontend must know.
```

<a id="step-27"></a>
### 27. دعوات الأقسام والعضوية

**النوع:** Backend · **المسؤول:** Hossin · **الحالات:** UC-U31–U36

**ماذا تضيف للبرنامج:** يجعل العضوية في القسم بموافقة الطرفين، فلا يُضاف أحد إلى صفحة مؤسسة دون رضاه.

**تعتمد على:** 26 (الأقسام: إنشاء وتعديل وحذف)  
**تعتمد عليها لاحقاً:** 28 (شاشات الأقسام)، 29 (شاشة دعوات الأقسام)

```text
[Quasario · Step 27/61 · Backend · UC-U31–U36]
Project: Quasario, a local services directory (Laravel REST API + React SPA + admin panel). The full
context is in CLAUDE.md: read it before anything else.
Feature: Section invitations API.
Done before this step: step 26 'Sections API'.
Already in the codebase (reuse it, do not recreate it): SectionService::invite, SectionPolicy, NotificationService.
Later steps build on this: step 28 'Sections screens'; step 29 'Section requests screen'.
So keep these stable and named exactly as written: Invitation and membership endpoints; GET /me/section-invitations used by the requests screen.

Task: Invitations and membership.
- POST /me/sections/{section}/invitations {user_id} → SectionService::invite.
- DELETE /me/sections/{section}/members/{user}: owner removes; notify REMOVED_FROM_SECTION.
- GET /me/section-invitations: my pending ones with section name and owner page (display_name, image, page_id).
- POST /section-invitations/{id}/accept → membership row, accepted, responded_at, notify owner INVITATION_ACCEPTED;
  /reject → rejected, notify INVITATION_REJECTED. Invitee only; pending only.
- POST /sections/{section}/leave: members only; notify owner MEMBER_LEFT.
Tests for every rule and notification.

How to work:
- Read CLAUDE.md first; use its table names, enums, error codes and API contract exactly.
- Before coding, read the files listed under "Already in the codebase" and reuse them. Then list the files you
  will create or change, and write them in full.
- Thin controllers; rules in Services; validation in FormRequests; ownership in Policies; output via API Resources.
- Pest feature tests in tests/Feature/<Module>/: success, every error listed above (assert the `code`), and
  rejection of a visitor, another user and a banned user where relevant.
- Do not change migrations or other modules unless this task says so. If you must touch a shared file, say why.
- Update docs/api.md with the endpoints (request, response example, error codes).
- Finish with: routes added, how to run the tests, example curl requests, and anything the frontend must know.
```

<a id="step-28"></a>
### 28. شاشات الأقسام

**النوع:** Frontend · **المسؤول:** Abdulhadi · **الحالات:** UC-U27–U30 U32 U36

**ماذا تضيف للبرنامج:** يعطي صاحب الصفحة أداة بناء الأقسام، ويعرضها للزوار داخل الصفحة.

**تعتمد على:** 26 (الأقسام: إنشاء وتعديل وحذف)، 27 (دعوات الأقسام والعضوية)، 21 (تعديل الملف والمعرض والروابط)  
**تعتمد عليها لاحقاً:** 32 (شاشات التقييم الموثّق)

```text
[Quasario · Step 28/61 · Frontend · UC-U27–U30 U32 U36]
Project: Quasario, a local services directory (Laravel REST API + React SPA + admin panel). The full
context is in CLAUDE.md: read it before anything else.
Feature: Sections screens.
Done before this step: step 26 'Sections API'; step 27 'Section invitations API'; step 21 'Profile, gallery and links editing screens'.
Already in the codebase (reuse it, do not recreate it): Owner mode of PageView and SectionsList (steps 'Page view' and 'Profile editing'), people search, useConfirm.
Later steps build on this: step 32 'Endorsement screens'.
So keep these stable and named exactly as written: components/PeoplePicker (search + multi-select) reused by endorsement requests.

Task: Sections. Attach the original "Adding a section" + member selection screens, S15, S16, S17.
- Add section (owner mode): type Members / Info, name; Info → phone + email; Members → PeoplePicker → "Send
  invitations".
- Edit section: name, (info) phone/email, (members) remove (useConfirm) and "+" invite; "Delete section" (useConfirm).
- SectionsList on the page shows members (link to their pages) and info; members see "Leave section" (useConfirm).

How to work:
- Read CLAUDE.md first; use its conventions, colors, navigation and API contract exactly.
- Before coding, read the files listed under "Already in the codebase" and reuse them (shared components, api
  client, hooks, types). Then list the files you will create or change, and write them in full (TypeScript, Tailwind).
- API calls in src/api/endpoints/<module>.ts with types in src/api/types.ts (snake_case fields as the API returns
  them); TanStack Query hooks inside the feature folder; invalidate the related queries after each mutation.
- Forms: react-hook-form + zod with the same rules as the backend; show server field errors; map `code` to an
  English message through the shared error helper.
- Every screen has loading, empty and error states and works at 360px and 1280px.
- If the endpoint is not merged yet, mock it in the endpoint file behind `import.meta.env.VITE_USE_MOCKS` with the
  exact response shape from docs/api.md.
- Vitest tests for the zod schemas and one component test for the main screen.
- I attach the UI mockups named above; match their layout and texts.
```

<a id="step-29"></a>
### 29. شاشة دعوات الأقسام

**النوع:** Frontend · **المسؤول:** Abdulhadi · **الحالات:** UC-U33 U34 U35

**ماذا تضيف للبرنامج:** يرى المستخدم الدعوات ويقبلها أو يرفضها، فيكتمل مسار الأقسام من الطرفين.

**تعتمد على:** 27 (دعوات الأقسام والعضوية)، 25 (شاشة الإشعارات)  
**تعتمد عليها لاحقاً:** لا شيء

```text
[Quasario · Step 29/61 · Frontend · UC-U33 U34 U35]
Project: Quasario, a local services directory (Laravel REST API + React SPA + admin panel). The full
context is in CLAUDE.md: read it before anything else.
Feature: Section requests screen.
Done before this step: step 27 'Section invitations API'; step 25 'Notifications screen'.
Already in the codebase (reuse it, do not recreate it): Settings screen, notifications links to /sections/requests.
Later steps build on this: none (end of a chain).
So keep these stable and named exactly as written: Settings row 'Sections Request'.

Task: Sections Request (S18) at /sections/requests, linked from Settings and notifications: pending invitations
(section, owner) with Accept / Reject; empty state "No invitations".

How to work:
- Read CLAUDE.md first; use its conventions, colors, navigation and API contract exactly.
- Before coding, read the files listed under "Already in the codebase" and reuse them (shared components, api
  client, hooks, types). Then list the files you will create or change, and write them in full (TypeScript, Tailwind).
- API calls in src/api/endpoints/<module>.ts with types in src/api/types.ts (snake_case fields as the API returns
  them); TanStack Query hooks inside the feature folder; invalidate the related queries after each mutation.
- Forms: react-hook-form + zod with the same rules as the backend; show server field errors; map `code` to an
  English message through the shared error helper.
- Every screen has loading, empty and error states and works at 360px and 1280px.
- If the endpoint is not merged yet, mock it in the endpoint file behind `import.meta.env.VITE_USE_MOCKS` with the
  exact response shape from docs/api.md.
- Vitest tests for the zod schemas and one component test for the main screen.
- I attach the UI mockups named above; match their layout and texts.
```

<a id="step-30"></a>
### 30. المساعد الذكي نسخة أولى (بالبديل الاحتياطي)

**النوع:** Backend · **المسؤول:** Hossin · **الحالات:** UC-V08

**ماذا تضيف للبرنامج:** يجعل endpoint المساعد جاهزاً مبكراً بالبديل الذي لا يفشل، فلا تبقى الميزة الأخطر معلقة حتى النهاية.

**تعتمد على:** 13 (تجربة الذكاء الاصطناعي)، 15 (الصفحة الرئيسية والبحث والتصفية)  
**تعتمد عليها لاحقاً:** 55 (المساعد الذكي الكامل)

```text
[Quasario · Step 30/61 · Backend · UC-V08]
Project: Quasario, a local services directory (Laravel REST API + React SPA + admin panel). The full
context is in CLAUDE.md: read it before anything else.
Feature: Assistant endpoint v0.
Done before this step: step 13 'AI assistant spike'; step 15 'Home, search and filter API'.
Already in the codebase (reuse it, do not recreate it): AssistantService (step 'AI assistant spike'), PageCardResource and withAverageRating (steps 'Page read' and 'Home, search').
Later steps build on this: step 55 'AI assistant final'.
So keep these stable and named exactly as written: POST /assistant {problem, latitude?, longitude?} → {reply, providers: PageCardResource[], provider}. The final AI step only changes the provider, not the contract.

Task: POST /assistant (public, rate limit 10/min per IP).
- problem required (emptyProblem), ≤1000 chars.
- AssistantService (Keyword only for now) → up to 2 visible pages with a suggested job title, best rated first; if
  latitude/longitude given and pages have locations, the nearest first (Haversine in SQL).
- Response {reply, providers, provider}. Tests with seeded keywords.

How to work:
- Read CLAUDE.md first; use its table names, enums, error codes and API contract exactly.
- Before coding, read the files listed under "Already in the codebase" and reuse them. Then list the files you
  will create or change, and write them in full.
- Thin controllers; rules in Services; validation in FormRequests; ownership in Policies; output via API Resources.
- Pest feature tests in tests/Feature/<Module>/: success, every error listed above (assert the `code`), and
  rejection of a visitor, another user and a banned user where relevant.
- Do not change migrations or other modules unless this task says so. If you must touch a shared file, say why.
- Update docs/api.md with the endpoints (request, response example, error codes).
- Finish with: routes added, how to run the tests, example curl requests, and anything the frontend must know.
```

## Sprint 3: الثقة والفعاليات (29/11 – 17/12)

<a id="step-31"></a>
### 31. التقييمات الموثّقة

**النوع:** Backend · **المسؤول:** Nazeer · **الحالات:** UC-U16–U20 V05

**ماذا تضيف للبرنامج:** الميزة التي تميز Quasario: تقييم لا يكتبه أي أحد، بل شخص تعامل فعلاً مع مقدم الخدمة وعلى أعمال محددة من معرضه. منه يأتي متوسط التقييم الذي يرتب الدليل.

**تعتمد على:** 19 (معرض الأعمال)، 24 (بنية الإشعارات والبحث عن أشخاص)  
**تعتمد عليها لاحقاً:** 32 (شاشات التقييم الموثّق)

```text
[Quasario · Step 31/61 · Backend · UC-U16–U20 V05]
Project: Quasario, a local services directory (Laravel REST API + React SPA + admin panel). The full
context is in CLAUDE.md: read it before anything else.
Feature: Endorsements API.
Done before this step: step 19 'Gallery API'; step 24 'Notifications service and people search'.
Already in the codebase (reuse it, do not recreate it): Gallery images and policy (step 'Gallery API'), NotificationService, people search, withAverageRating.
Later steps build on this: step 32 'Endorsement screens'.
So keep these stable and named exactly as written: Endorsement request endpoints, GET /endorsements/{id}, DELETE /endorsements/{id}, EndorsementResource; PageResource.endorsements filled; average rating now changes home ordering.

Task: Endorsements.
- POST /endorsement-requests {endorser_id, image_ids[]}: ≥1 image (requiredField), images from my gallery
  (invalidField), endorser ≠ me (selfSelection), endorser active, no pending request to the same person (409
  alreadyRequested). Notify ENDORSEMENT_REQUEST.
- GET /endorsement-requests (incoming pending), GET /endorsement-requests/{id} (requester or endorser only, with images).
- POST /endorsement-requests/{id}/accept {comment, rating}: endorser only, pending only; comment required, rating
  1–5; create endorsement; notify ENDORSEMENT_ACCEPTED. /reject: notify ENDORSEMENT_REJECTED.
- GET /endorsements/{id} public (endorser, rating, comment, images). DELETE /endorsements/{id}: endorsed page owner
  only; notify endorser ENDORSEMENT_DELETED.
- Fill PageResource.endorsements; test that home ordering follows the new average.

How to work:
- Read CLAUDE.md first; use its table names, enums, error codes and API contract exactly.
- Before coding, read the files listed under "Already in the codebase" and reuse them. Then list the files you
  will create or change, and write them in full.
- Thin controllers; rules in Services; validation in FormRequests; ownership in Policies; output via API Resources.
- Pest feature tests in tests/Feature/<Module>/: success, every error listed above (assert the `code`), and
  rejection of a visitor, another user and a banned user where relevant.
- Do not change migrations or other modules unless this task says so. If you must touch a shared file, say why.
- Update docs/api.md with the endpoints (request, response example, error codes).
- Finish with: routes added, how to run the tests, example curl requests, and anything the frontend must know.
```

<a id="step-32"></a>
### 32. شاشات التقييم الموثّق

**النوع:** Frontend · **المسؤول:** Batol · **الحالات:** UC-U16–U20 V05

**ماذا تضيف للبرنامج:** يحوّل الثقة إلى شيء مرئي في الصفحة، ويعطي المستخدم طريقاً لطلب التقييم والرد عليه.

**تعتمد على:** 31 (التقييمات الموثّقة)، 28 (شاشات الأقسام)  
**تعتمد عليها لاحقاً:** 58 (اختبارات طرف إلى طرف (Playwright))

```text
[Quasario · Step 32/61 · Frontend · UC-U16–U20 V05]
Project: Quasario, a local services directory (Laravel REST API + React SPA + admin panel). The full
context is in CLAUDE.md: read it before anything else.
Feature: Endorsement screens.
Done before this step: step 31 'Endorsements API'; step 28 'Sections screens'.
Already in the codebase (reuse it, do not recreate it): PeoplePicker (step 'Sections screens'), GalleryGrid selection, StarRating, EndorsementsList slot in PageView.
Later steps build on this: step 58 'End-to-end tests'.
So keep these stable and named exactly as written: Endorsements screen (/endorsements) and EndorsementDetail modal.

Task: Endorsements. Attach the original endorsement screens and S19.
- /endorsements: "Incoming" list → detail with images, comment, StarRating input, Accept / Reject; "New request" →
  PeoplePicker (single) → choose ≥1 images from my gallery → Send.
- EndorsementsList on PageView: "Endorsement from …", stars, comment; click → detail with work images; owner can
  delete (useConfirm).

How to work:
- Read CLAUDE.md first; use its conventions, colors, navigation and API contract exactly.
- Before coding, read the files listed under "Already in the codebase" and reuse them (shared components, api
  client, hooks, types). Then list the files you will create or change, and write them in full (TypeScript, Tailwind).
- API calls in src/api/endpoints/<module>.ts with types in src/api/types.ts (snake_case fields as the API returns
  them); TanStack Query hooks inside the feature folder; invalidate the related queries after each mutation.
- Forms: react-hook-form + zod with the same rules as the backend; show server field errors; map `code` to an
  English message through the shared error helper.
- Every screen has loading, empty and error states and works at 360px and 1280px.
- If the endpoint is not merged yet, mock it in the endpoint file behind `import.meta.env.VITE_USE_MOCKS` with the
  exact response shape from docs/api.md.
- Vitest tests for the zod schemas and one component test for the main screen.
- I attach the UI mockups named above; match their layout and texts.
```

<a id="step-33"></a>
### 33. طلب شارة التوثيق

**النوع:** Backend · **المسؤول:** Nazeer · **الحالات:** UC-U15

**ماذا تضيف للبرنامج:** يمكّن مقدم الخدمة من إثبات هويته للحصول على الشارة. الشارة تزيد ثقة الزوار وهي شرط نشر الفعاليات.

**تعتمد على:** 7 (المصادقة: التسجيل بمرحلتين والدخول والخروج)  
**تعتمد عليها لاحقاً:** 34 (شاشة شارة التوثيق)، 37 (الفعاليات: جهة المستخدم الموثّق)، 51 (مراجعة طلبات التوثيق)

```text
[Quasario · Step 33/61 · Backend · UC-U15]
Project: Quasario, a local services directory (Laravel REST API + React SPA + admin panel). The full
context is in CLAUDE.md: read it before anything else.
Feature: Verification request API.
Done before this step: step 7 'Authentication API'.
Already in the codebase (reuse it, do not recreate it): VerificationRequest model, private disk, route groups.
Later steps build on this: step 34 'Trust mark screen'; step 37 'Verified user events API'; step 51 'Admin verification review API'.
So keep these stable and named exactly as written: GET/POST /me/verification and the private storage path convention ids/{user_id}/... used by the admin review.

Task: Verification request.
- GET /me/verification: {is_verified, latest_request: {status, created_at} | null}.
- POST /me/verification (multipart id_image JPG/PNG ≤5MB): already verified or pending → 409 alreadyRequested;
  rejected allows a new one. Store on the PRIVATE disk ids/{user_id}/; never return a public URL.
Tests.

How to work:
- Read CLAUDE.md first; use its table names, enums, error codes and API contract exactly.
- Before coding, read the files listed under "Already in the codebase" and reuse them. Then list the files you
  will create or change, and write them in full.
- Thin controllers; rules in Services; validation in FormRequests; ownership in Policies; output via API Resources.
- Pest feature tests in tests/Feature/<Module>/: success, every error listed above (assert the `code`), and
  rejection of a visitor, another user and a banned user where relevant.
- Do not change migrations or other modules unless this task says so. If you must touch a shared file, say why.
- Update docs/api.md with the endpoints (request, response example, error codes).
- Finish with: routes added, how to run the tests, example curl requests, and anything the frontend must know.
```

<a id="step-34"></a>
### 34. شاشة شارة التوثيق

**النوع:** Frontend · **المسؤول:** Batol · **الحالات:** UC-U15

**ماذا تضيف للبرنامج:** يعرض للمستخدم حالة توثيقه ويتيح طلب الشارة، ويُظهر علامة التوثيق في كل مكان يظهر فيه اسمه.

**تعتمد على:** 33 (طلب شارة التوثيق)، 6 (مكوّنات الواجهة المشتركة)  
**تعتمد عليها لاحقاً:** 41 (إدارة فعالياتي)

```text
[Quasario · Step 34/61 · Frontend · UC-U15]
Project: Quasario, a local services directory (Laravel REST API + React SPA + admin panel). The full
context is in CLAUDE.md: read it before anything else.
Feature: Trust mark screen.
Done before this step: step 33 'Verification request API'; step 6 'Shared UI components'.
Already in the codebase (reuse it, do not recreate it): ImageUpload with capture, Badge, Settings.
Later steps build on this: step 41 'My events screens'.
So keep these stable and named exactly as written: VerifiedBadge component used by PageCard, PageHeader, chats and admin screens.

Task: /verification (Trust Mark): status Verified ✓ / Pending / Rejected / None; for None/Rejected: ID image upload
(device or camera) and Submit. Create VerifiedBadge and use it in PageCard and PageHeader. Add the Settings row.

How to work:
- Read CLAUDE.md first; use its conventions, colors, navigation and API contract exactly.
- Before coding, read the files listed under "Already in the codebase" and reuse them (shared components, api
  client, hooks, types). Then list the files you will create or change, and write them in full (TypeScript, Tailwind).
- API calls in src/api/endpoints/<module>.ts with types in src/api/types.ts (snake_case fields as the API returns
  them); TanStack Query hooks inside the feature folder; invalidate the related queries after each mutation.
- Forms: react-hook-form + zod with the same rules as the backend; show server field errors; map `code` to an
  English message through the shared error helper.
- Every screen has loading, empty and error states and works at 360px and 1280px.
- If the endpoint is not merged yet, mock it in the endpoint file behind `import.meta.env.VITE_USE_MOCKS` with the
  exact response shape from docs/api.md.
- Vitest tests for the zod schemas and one component test for the main screen.
- I attach the UI mockups named above; match their layout and texts.
```

<a id="step-35"></a>
### 35. موقع الصفحة وخريطة الصفحات

**النوع:** Backend · **المسؤول:** Nazeer · **الحالات:** UC-U25 U26 V06 V07

**ماذا تضيف للبرنامج:** يدل الزائر على مكان مقدم الخدمة بدقة (الطابق وصورة المدخل والإرشادات)، ويغذي الخريطة التفاعلية والمساعد الذكي بالأقرب.

**تعتمد على:** 18 (تعديل الملف الشخصي)  
**تعتمد عليها لاحقاً:** 36 (موقع الصفحة والخريطة التفاعلية)، 55 (المساعد الذكي الكامل)

```text
[Quasario · Step 35/61 · Backend · UC-U25 U26 V06 V07]
Project: Quasario, a local services directory (Laravel REST API + React SPA + admin panel). The full
context is in CLAUDE.md: read it before anything else.
Feature: Page location and map API.
Done before this step: step 18 'Profile edit API'.
Already in the codebase (reuse it, do not recreate it): ImageStorage, PageLocation model, Floor enum, PageResource location field.
Later steps build on this: step 36 'Page location and interactive map screens'; step 55 'AI assistant final'.
So keep these stable and named exactly as written: POST/PATCH /me/page/location, GET /pages/{page}/location, GET /map/pages (MapPageResource). The assistant's nearest sorting uses page_locations.

Task: Page location and map.
- POST /me/page/location (multipart): latitude -90..90, longitude -180..180 required; place_image optional JPG/PNG
  ≤5MB; floor optional (Floor); directions optional ≤1000. Existing location → 422 alreadyUsed (use PATCH).
- PATCH /me/page/location {latitude, longitude} only. No delete.
- GET /pages/{page}/location public; fill PageResource.location.
- GET /map/pages: visible pages with a location [{page_id, display_name, job_title, latitude, longitude,
  profile_image_url}], cached 5 minutes, cache cleared on change.
Tests.

How to work:
- Read CLAUDE.md first; use its table names, enums, error codes and API contract exactly.
- Before coding, read the files listed under "Already in the codebase" and reuse them. Then list the files you
  will create or change, and write them in full.
- Thin controllers; rules in Services; validation in FormRequests; ownership in Policies; output via API Resources.
- Pest feature tests in tests/Feature/<Module>/: success, every error listed above (assert the `code`), and
  rejection of a visitor, another user and a banned user where relevant.
- Do not change migrations or other modules unless this task says so. If you must touch a shared file, say why.
- Update docs/api.md with the endpoints (request, response example, error codes).
- Finish with: routes added, how to run the tests, example curl requests, and anything the frontend must know.
```

<a id="step-36"></a>
### 36. موقع الصفحة والخريطة التفاعلية

**النوع:** Frontend · **المسؤول:** Batol · **الحالات:** UC-U25 U26 V06 V07

**ماذا تضيف للبرنامج:** يُظهر مقدمي الخدمات على الخريطة (عنصر Map في شريط التنقل) ويتيح لصاحب الصفحة تحديد مكانه.

**تعتمد على:** 35 (موقع الصفحة وخريطة الصفحات)، 12 (تجربة الخريطة ومكوّناتها المشتركة)، 21 (تعديل الملف والمعرض والروابط)  
**تعتمد عليها لاحقاً:** لا شيء

```text
[Quasario · Step 36/61 · Frontend · UC-U25 U26 V06 V07]
Project: Quasario, a local services directory (Laravel REST API + React SPA + admin panel). The full
context is in CLAUDE.md: read it before anything else.
Feature: Page location and interactive map screens.
Done before this step: step 35 'Page location and map API'; step 12 'Map components spike'; step 21 'Profile, gallery and links editing screens'.
Already in the codebase (reuse it, do not recreate it): MapView, MapPicker, LocationCard (step 'Map components spike'), owner mode of PageView, LocationRow.
Later steps build on this: none (end of a chain).
So keep these stable and named exactly as written: /map screen.

Task: Attach S12, S13, S14 and the original map screen.
- Owner mode → "Page location": none → MapPicker + Place photo + Floor + Directions → POST; exists → "Edit location"
  MapPicker only → PATCH.
- /map: MapView with GET /map/pages; marker → LocationCard (S14) with "View page". LocationRow on PageView opens the
  same card centered on the page.

How to work:
- Read CLAUDE.md first; use its conventions, colors, navigation and API contract exactly.
- Before coding, read the files listed under "Already in the codebase" and reuse them (shared components, api
  client, hooks, types). Then list the files you will create or change, and write them in full (TypeScript, Tailwind).
- API calls in src/api/endpoints/<module>.ts with types in src/api/types.ts (snake_case fields as the API returns
  them); TanStack Query hooks inside the feature folder; invalidate the related queries after each mutation.
- Forms: react-hook-form + zod with the same rules as the backend; show server field errors; map `code` to an
  English message through the shared error helper.
- Every screen has loading, empty and error states and works at 360px and 1280px.
- If the endpoint is not merged yet, mock it in the endpoint file behind `import.meta.env.VITE_USE_MOCKS` with the
  exact response shape from docs/api.md.
- Vitest tests for the zod schemas and one component test for the main screen.
- I attach the UI mockups named above; match their layout and texts.
```

<a id="step-37"></a>
### 37. الفعاليات: جهة المستخدم الموثّق

**النوع:** Backend · **المسؤول:** Hossin · **الحالات:** UC-VU01–VU05

**ماذا تضيف للبرنامج:** يعطي الموثّقين قناة للإعلان عن فعالياتهم وعروضهم، وكل فعالية تمر بمراجعة المسؤول فلا يُنشر محتوى غير لائق.

**تعتمد على:** 33 (طلب شارة التوثيق)، 18 (تعديل الملف الشخصي)، 24 (بنية الإشعارات والبحث عن أشخاص)  
**تعتمد عليها لاحقاً:** 38 (الفعاليات العامة)، 39 (مراجعة الفعاليات (المسؤول))، 41 (إدارة فعالياتي)

```text
[Quasario · Step 37/61 · Backend · UC-VU01–VU05]
Project: Quasario, a local services directory (Laravel REST API + React SPA + admin panel). The full
context is in CLAUDE.md: read it before anything else.
Feature: Verified user events API.
Done before this step: step 33 'Verification request API'; step 18 'Profile edit API'; step 24 'Notifications service and people search'.
Already in the codebase (reuse it, do not recreate it): verified middleware, ImageStorage, Event/EventPost models, owner's page city.
Later steps build on this: step 38 'Public events API'; step 39 'Admin event review API'; step 41 'My events screens'.
So keep these stable and named exactly as written: GET /me/events, POST /me/events, POST /me/events/{event}, DELETE /me/events/{event}, DELETE /me/posts/{post}, EventPolicy, EventResource, EventCardResource, EventService (createWithFirstPost, update → pending, deletePost with lastPost rule). Public events and admin review reuse EventResource.

Task: Event management (auth + verified).
- EventCardResource: id, name, start_date, end_date, city, image_url (first post), status, owner {display_name, page_id}.
  EventResource: card + description, latitude, longitude, posts [{id, image_url, text, created_at}].
- GET /me/events (all statuses).
- POST /me/events (multipart): name ≤150, description, start_date ≥ today, end_date ≥ start_date (invalidDates),
  latitude, longitude, post_image JPG/PNG ≤5MB, post_text. city_id from my page. Event (pending) + first post in a
  transaction.
- POST /me/events/{event} (multipart; any subset + optional new post): owner only; ANY saved change → pending.
- DELETE /me/events/{event} owner only (no notification). DELETE /me/posts/{post}: last post → 409 lastPost.
- Badge revoked → 403 notVerified on all of these.
Tests for every rule.

How to work:
- Read CLAUDE.md first; use its table names, enums, error codes and API contract exactly.
- Before coding, read the files listed under "Already in the codebase" and reuse them. Then list the files you
  will create or change, and write them in full.
- Thin controllers; rules in Services; validation in FormRequests; ownership in Policies; output via API Resources.
- Pest feature tests in tests/Feature/<Module>/: success, every error listed above (assert the `code`), and
  rejection of a visitor, another user and a banned user where relevant.
- Do not change migrations or other modules unless this task says so. If you must touch a shared file, say why.
- Update docs/api.md with the endpoints (request, response example, error codes).
- Finish with: routes added, how to run the tests, example curl requests, and anything the frontend must know.
```

<a id="step-38"></a>
### 38. الفعاليات العامة

**النوع:** Backend · **المسؤول:** Hossin · **الحالات:** UC-V09 V10

**ماذا تضيف للبرنامج:** يعرض للزوار الفعاليات المقبولة فقط، مرتبة من الأحدث، مع التصفية بالمدينة.

**تعتمد على:** 37 (الفعاليات: جهة المستخدم الموثّق)  
**تعتمد عليها لاحقاً:** 40 (شاشات الفعاليات العامة)، 43 (البلاغات (جهة المستخدم))

```text
[Quasario · Step 38/61 · Backend · UC-V09 V10]
Project: Quasario, a local services directory (Laravel REST API + React SPA + admin panel). The full
context is in CLAUDE.md: read it before anything else.
Feature: Public events API.
Done before this step: step 37 'Verified user events API'.
Already in the codebase (reuse it, do not recreate it): EventCardResource, EventResource, Event model.
Later steps build on this: step 40 'Public events screens'; step 43 'Reports API (users)'.
So keep these stable and named exactly as written: GET /events and GET /events/{event}.

Task: Public events.
- GET /events?name=&city_id=: accepted events of active owners, start_date desc (ended ones stay), paginated.
- GET /events/{event}: accepted only, otherwise 404 unless owner or admin.
Tests.

How to work:
- Read CLAUDE.md first; use its table names, enums, error codes and API contract exactly.
- Before coding, read the files listed under "Already in the codebase" and reuse them. Then list the files you
  will create or change, and write them in full.
- Thin controllers; rules in Services; validation in FormRequests; ownership in Policies; output via API Resources.
- Pest feature tests in tests/Feature/<Module>/: success, every error listed above (assert the `code`), and
  rejection of a visitor, another user and a banned user where relevant.
- Do not change migrations or other modules unless this task says so. If you must touch a shared file, say why.
- Update docs/api.md with the endpoints (request, response example, error codes).
- Finish with: routes added, how to run the tests, example curl requests, and anything the frontend must know.
```

<a id="step-39"></a>
### 39. مراجعة الفعاليات (المسؤول)

**النوع:** Backend · **المسؤول:** Hossin · **الحالات:** UC-A12–A16 A20

**ماذا تضيف للبرنامج:** بدونها لا تظهر أي فعالية للزوار. المسؤول يقبل أو يرفض أو يحذف ما نُشر، ويصل الإشعار لصاحب الفعالية.

**تعتمد على:** 37 (الفعاليات: جهة المستخدم الموثّق)  
**تعتمد عليها لاحقاً:** 42 (لوحة الإدارة: الهيكل ومراجعة الفعاليات)، 49 (إدارة الحسابات والحظر وسحب الشارة)، 51 (مراجعة طلبات التوثيق)

```text
[Quasario · Step 39/61 · Backend · UC-A12–A16 A20]
Project: Quasario, a local services directory (Laravel REST API + React SPA + admin panel). The full
context is in CLAUDE.md: read it before anything else.
Feature: Admin event review API.
Done before this step: step 37 'Verified user events API'.
Already in the codebase (reuse it, do not recreate it): EventResource, NotificationService, admin middleware.
Later steps build on this: step 42 'Admin shell and event review screens'; step 49 'Admin accounts API'; step 51 'Admin verification review API'.
So keep these stable and named exactly as written: GET /admin/events, GET /admin/events/{event}, accept, reject, DELETE /admin/events/{event}. First endpoints of the admin panel; later admin steps follow the same controller style in Controllers/Api/V1/Admin.

Task: Admin event review (auth + admin).
- GET /admin/events?status=pending|accepted&name=: Submitted = pending, Published = accepted, name search.
- GET /admin/events/{event}: full details with owner and posts.
- POST /admin/events/{event}/accept → accepted, notify EVENT_ACCEPTED; /reject → rejected, notify EVENT_REJECTED;
  only from pending (409 invalidField otherwise).
- DELETE /admin/events/{event}: notify owner EVENT_DELETED, then delete.
Tests incl. non-admin → 403 notAdmin.

How to work:
- Read CLAUDE.md first; use its table names, enums, error codes and API contract exactly.
- Before coding, read the files listed under "Already in the codebase" and reuse them. Then list the files you
  will create or change, and write them in full.
- Thin controllers; rules in Services; validation in FormRequests; ownership in Policies; output via API Resources.
- Pest feature tests in tests/Feature/<Module>/: success, every error listed above (assert the `code`), and
  rejection of a visitor, another user and a banned user where relevant.
- Do not change migrations or other modules unless this task says so. If you must touch a shared file, say why.
- Update docs/api.md with the endpoints (request, response example, error codes).
- Finish with: routes added, how to run the tests, example curl requests, and anything the frontend must know.
```

<a id="step-40"></a>
### 40. شاشات الفعاليات العامة

**النوع:** Frontend · **المسؤول:** Abdulhadi · **الحالات:** UC-V09 V10

**ماذا تضيف للبرنامج:** عنصر Events في شريط التنقل: الزائر يتصفح الفعاليات ويرى تفاصيلها ومكانها.

**تعتمد على:** 38 (الفعاليات العامة)، 12 (تجربة الخريطة ومكوّناتها المشتركة)  
**تعتمد عليها لاحقاً:** 41 (إدارة فعالياتي)، 42 (لوحة الإدارة: الهيكل ومراجعة الفعاليات)، 44 (زر ونموذج التبليغ)

```text
[Quasario · Step 40/61 · Frontend · UC-V09 V10]
Project: Quasario, a local services directory (Laravel REST API + React SPA + admin panel). The full
context is in CLAUDE.md: read it before anything else.
Feature: Public events screens.
Done before this step: step 38 'Public events API'; step 12 'Map components spike'.
Already in the codebase (reuse it, do not recreate it): MapView, useFilters (cities), LoadMore.
Later steps build on this: step 41 'My events screens'; step 42 'Admin shell and event review screens'; step 44 'Report dialog'.
So keep these stable and named exactly as written: components/EventCard and EventDetail (reused by My events preview and admin event detail), report buttons placeholders.

Task: Events. Attach the original events screens and S22.
- /events: search by name, city filter, EventCards (image, name, dates, city), LoadMore.
- /events/:id: posts feed, description, dates, "Show location" (MapView one marker), Report event / Report post
  buttons (wired in the reports step; visitors get SignInRequiredDialog).

How to work:
- Read CLAUDE.md first; use its conventions, colors, navigation and API contract exactly.
- Before coding, read the files listed under "Already in the codebase" and reuse them (shared components, api
  client, hooks, types). Then list the files you will create or change, and write them in full (TypeScript, Tailwind).
- API calls in src/api/endpoints/<module>.ts with types in src/api/types.ts (snake_case fields as the API returns
  them); TanStack Query hooks inside the feature folder; invalidate the related queries after each mutation.
- Forms: react-hook-form + zod with the same rules as the backend; show server field errors; map `code` to an
  English message through the shared error helper.
- Every screen has loading, empty and error states and works at 360px and 1280px.
- If the endpoint is not merged yet, mock it in the endpoint file behind `import.meta.env.VITE_USE_MOCKS` with the
  exact response shape from docs/api.md.
- Vitest tests for the zod schemas and one component test for the main screen.
- I attach the UI mockups named above; match their layout and texts.
```

<a id="step-41"></a>
### 41. إدارة فعالياتي

**النوع:** Frontend · **المسؤول:** Abdulhadi · **الحالات:** UC-VU01–VU05

**ماذا تضيف للبرنامج:** أدوات المستخدم الموثّق لإنشاء فعالياته ومتابعة حالتها وتعديلها.

**تعتمد على:** 37 (الفعاليات: جهة المستخدم الموثّق)، 40 (شاشات الفعاليات العامة)، 34 (شاشة شارة التوثيق)  
**تعتمد عليها لاحقاً:** 58 (اختبارات طرف إلى طرف (Playwright))

```text
[Quasario · Step 41/61 · Frontend · UC-VU01–VU05]
Project: Quasario, a local services directory (Laravel REST API + React SPA + admin panel). The full
context is in CLAUDE.md: read it before anything else.
Feature: My events screens.
Done before this step: step 37 'Verified user events API'; step 40 'Public events screens'; step 34 'Trust mark screen'.
Already in the codebase (reuse it, do not recreate it): MapPicker, EventCard, Badge, ImageUpload, useConfirm, RequireVerified guard.
Later steps build on this: step 58 'End-to-end tests'.
So keep these stable and named exactly as written: Profile menu item 'Manage events' visible only when user.is_verified.

Task: Manage Events. Attach S23, S24, S25, S26.
- /my-events: EventCards with Pending / Accepted / Rejected badges.
- /my-events/new: name, description, date pickers (end ≥ start, start not past), MapPicker (S25), first post
  (image + text) → "Request sent".
- /my-events/:id/edit (S26): all fields, add post, delete post (lastPost message), warning "Saving will send the
  event for review again"; Delete event (useConfirm).

How to work:
- Read CLAUDE.md first; use its conventions, colors, navigation and API contract exactly.
- Before coding, read the files listed under "Already in the codebase" and reuse them (shared components, api
  client, hooks, types). Then list the files you will create or change, and write them in full (TypeScript, Tailwind).
- API calls in src/api/endpoints/<module>.ts with types in src/api/types.ts (snake_case fields as the API returns
  them); TanStack Query hooks inside the feature folder; invalidate the related queries after each mutation.
- Forms: react-hook-form + zod with the same rules as the backend; show server field errors; map `code` to an
  English message through the shared error helper.
- Every screen has loading, empty and error states and works at 360px and 1280px.
- If the endpoint is not merged yet, mock it in the endpoint file behind `import.meta.env.VITE_USE_MOCKS` with the
  exact response shape from docs/api.md.
- Vitest tests for the zod schemas and one component test for the main screen.
- I attach the UI mockups named above; match their layout and texts.
```

<a id="step-42"></a>
### 42. لوحة الإدارة: الهيكل ومراجعة الفعاليات

**النوع:** Admin (Frontend) · **المسؤول:** Abdulhadi · **الحالات:** UC-A12–A16 A20

**ماذا تضيف للبرنامج:** أول جزء من لوحة الإدارة. تبني الهيكل الذي تُضاف إليه الحسابات والتوثيق والبلاغات في Sprint 4.

**تعتمد على:** 39 (مراجعة الفعاليات (المسؤول))، 40 (شاشات الفعاليات العامة)  
**تعتمد عليها لاحقاً:** 50 (لوحة الإدارة: الحسابات)، 52 (لوحة الإدارة: طلبات التوثيق)، 54 (لوحة الإدارة: البلاغات)

```text
[Quasario · Step 42/61 · Admin (Frontend) · UC-A12–A16 A20]
Project: Quasario, a local services directory (Laravel REST API + React SPA + admin panel). The full
context is in CLAUDE.md: read it before anything else.
Feature: Admin shell and event review screens.
Done before this step: step 39 'Admin event review API'; step 40 'Public events screens'.
Already in the codebase (reuse it, do not recreate it): AdminLayout and RequireAdmin (step 'Frontend architecture'), EventDetail, useConfirm.
Later steps build on this: step 50 'Admin accounts screens'; step 52 'Admin verification screens'; step 54 'Admin reports screens'.
So keep these stable and named exactly as written: Admin table/list pattern (components/admin/AdminTable, AdminDetailHeader) reused by every later admin screen.

Task: Admin. Attach S27, S29, S30. Desktop first, usable on mobile.
- AdminLayout menu (S27); /admin redirects to Manage events.
- Create AdminTable (columns, search box, pagination) and AdminDetailHeader (title + actions).
- /admin/events: tabs Submitted / Published, name search; /admin/events/:id: EventDetail + Accept / Reject
  (Submitted) or Delete with useConfirm (Published).

How to work:
- Read CLAUDE.md first; use its conventions, colors, navigation and API contract exactly.
- Before coding, read the files listed under "Already in the codebase" and reuse them (shared components, api
  client, hooks, types). Then list the files you will create or change, and write them in full (TypeScript, Tailwind).
- API calls in src/api/endpoints/<module>.ts with types in src/api/types.ts (snake_case fields as the API returns
  them); TanStack Query hooks inside the feature folder; invalidate the related queries after each mutation.
- Forms: react-hook-form + zod with the same rules as the backend; show server field errors; map `code` to an
  English message through the shared error helper.
- Every screen has loading, empty and error states and works at 360px and 1280px.
- If the endpoint is not merged yet, mock it in the endpoint file behind `import.meta.env.VITE_USE_MOCKS` with the
  exact response shape from docs/api.md.
- Vitest tests for the zod schemas and one component test for the main screen.
- I attach the UI mockups named above; match their layout and texts.
```

## Sprint 4: الإدارة والتواصل والمساعد (20/12 – 7/1)

<a id="step-43"></a>
### 43. البلاغات (جهة المستخدم)

**النوع:** Backend · **المسؤول:** Nazeer · **الحالات:** UC-U12 U13 U14

**ماذا تضيف للبرنامج:** يجعل المجتمع يحمي الدليل: أي مستخدم يبلّغ عن صفحة أو فعالية أو منشور مخالف. تُبنى أولاً لأن معالجة المسؤول تعتمد عليها.

**تعتمد على:** 14 (عرض الصفحة والقوائم الثابتة)، 38 (الفعاليات العامة)  
**تعتمد عليها لاحقاً:** 44 (زر ونموذج التبليغ)، 53 (معالجة البلاغات)

```text
[Quasario · Step 43/61 · Backend · UC-U12 U13 U14]
Project: Quasario, a local services directory (Laravel REST API + React SPA + admin panel). The full
context is in CLAUDE.md: read it before anything else.
Feature: Reports API (users).
Done before this step: step 14 'Page read API and filter options'; step 38 'Public events API'.
Already in the codebase (reuse it, do not recreate it): Report model and enums, Page/Event/EventPost models, rate limiter `actions`.
Later steps build on this: step 44 'Report dialog'; step 53 'Admin reports API'.
So keep these stable and named exactly as written: Three report endpoints and ReportService::create(). Admin handling reads these rows.

Task: Reports by users.
- POST /pages/{page}/reports, /events/{event}/reports, /posts/{post}/reports {reason}: reason required ≤500
  (emptyField / invalidField); not own content (403 ownContent); no pending report by the same user on the same
  target (409 alreadyReported). One ReportService::create(target, reporter, reason). Rate limit `actions`.
Tests for the three targets and each rule.

How to work:
- Read CLAUDE.md first; use its table names, enums, error codes and API contract exactly.
- Before coding, read the files listed under "Already in the codebase" and reuse them. Then list the files you
  will create or change, and write them in full.
- Thin controllers; rules in Services; validation in FormRequests; ownership in Policies; output via API Resources.
- Pest feature tests in tests/Feature/<Module>/: success, every error listed above (assert the `code`), and
  rejection of a visitor, another user and a banned user where relevant.
- Do not change migrations or other modules unless this task says so. If you must touch a shared file, say why.
- Update docs/api.md with the endpoints (request, response example, error codes).
- Finish with: routes added, how to run the tests, example curl requests, and anything the frontend must know.
```

<a id="step-44"></a>
### 44. زر ونموذج التبليغ

**النوع:** Frontend · **المسؤول:** Batol · **الحالات:** UC-U12 U13 U14

**ماذا تضيف للبرنامج:** نموذج واحد يُستخدم في ثلاثة أماكن، فيبقى التبليغ بسيطاً ومتناسقاً.

**تعتمد على:** 43 (البلاغات (جهة المستخدم))، 17 (صفحة العرض وعارض صور المعرض)، 40 (شاشات الفعاليات العامة)  
**تعتمد عليها لاحقاً:** 57 (مراجعة التجاوب والتناسق)

```text
[Quasario · Step 44/61 · Frontend · UC-U12 U13 U14]
Project: Quasario, a local services directory (Laravel REST API + React SPA + admin panel). The full
context is in CLAUDE.md: read it before anything else.
Feature: Report dialog.
Done before this step: step 43 'Reports API (users)'; step 17 'Page view screen'; step 40 'Public events screens'.
Already in the codebase (reuse it, do not recreate it): PageActions Report button, Report buttons on event detail and posts, useRequireAuth.
Later steps build on this: step 57 'Responsive and consistency pass'.
So keep these stable and named exactly as written: components/ReportDialog.

Task: Attach S21. ReportDialog (reason textarea, 500 counter, required) opened from PageActions, event detail and
each post. Hidden on own content; visitors → SignInRequiredDialog; alreadyReported/ownContent messages; toast
"Report sent".

How to work:
- Read CLAUDE.md first; use its conventions, colors, navigation and API contract exactly.
- Before coding, read the files listed under "Already in the codebase" and reuse them (shared components, api
  client, hooks, types). Then list the files you will create or change, and write them in full (TypeScript, Tailwind).
- API calls in src/api/endpoints/<module>.ts with types in src/api/types.ts (snake_case fields as the API returns
  them); TanStack Query hooks inside the feature folder; invalidate the related queries after each mutation.
- Forms: react-hook-form + zod with the same rules as the backend; show server field errors; map `code` to an
  English message through the shared error helper.
- Every screen has loading, empty and error states and works at 360px and 1280px.
- If the endpoint is not merged yet, mock it in the endpoint file behind `import.meta.env.VITE_USE_MOCKS` with the
  exact response shape from docs/api.md.
- Vitest tests for the zod schemas and one component test for the main screen.
- I attach the UI mockups named above; match their layout and texts.
```

<a id="step-45"></a>
### 45. المحادثة

**النوع:** Backend · **المسؤول:** Nazeer · **الحالات:** UC-U09 U10

**ماذا تضيف للبرنامج:** يتواصل الزائر المسجّل مع مقدم الخدمة داخل التطبيق دون مشاركة رقمه.

**تعتمد على:** 24 (بنية الإشعارات والبحث عن أشخاص)، 14 (عرض الصفحة والقوائم الثابتة)  
**تعتمد عليها لاحقاً:** 46 (شاشات المحادثة)، 47 (حذف الحساب)

```text
[Quasario · Step 45/61 · Backend · UC-U09 U10]
Project: Quasario, a local services directory (Laravel REST API + React SPA + admin panel). The full
context is in CLAUDE.md: read it before anything else.
Feature: Chat API.
Done before this step: step 24 'Notifications service and people search'; step 14 'Page read API and filter options'.
Already in the codebase (reuse it, do not recreate it): Conversation/Message models, NotificationService, private disk.
Later steps build on this: step 46 'Chat screens'; step 47 'Account deletion API'.
So keep these stable and named exactly as written: Conversation and message endpoints with polling (?after=).

Task: Chat with polling.
- POST /conversations {other_user_id}: firstOrCreate with user_one_id < user_two_id; not self; returns conversation +
  last 30 messages.
- POST /messages (multipart) {receiver_id, text?, file?}: text or file (emptyField); JPG/PNG/PDF ≤5MB on PRIVATE disk;
  updates last_message_at; notify NEW_MESSAGE (data conversation_id).
- GET /conversations: mine by last_message_at desc, other user's name/image, last message preview, unread count.
- GET /conversations/{id}/messages?after={id}: participants only; marks received as read.
- GET /messages/{message}/attachment: participants only, streams the file.
Tests incl. third user 403.

How to work:
- Read CLAUDE.md first; use its table names, enums, error codes and API contract exactly.
- Before coding, read the files listed under "Already in the codebase" and reuse them. Then list the files you
  will create or change, and write them in full.
- Thin controllers; rules in Services; validation in FormRequests; ownership in Policies; output via API Resources.
- Pest feature tests in tests/Feature/<Module>/: success, every error listed above (assert the `code`), and
  rejection of a visitor, another user and a banned user where relevant.
- Do not change migrations or other modules unless this task says so. If you must touch a shared file, say why.
- Update docs/api.md with the endpoints (request, response example, error codes).
- Finish with: routes added, how to run the tests, example curl requests, and anything the frontend must know.
```

<a id="step-46"></a>
### 46. شاشات المحادثة

**النوع:** Frontend · **المسؤول:** Batol · **الحالات:** UC-U09 U10

**ماذا تضيف للبرنامج:** عنصر Chats في شريط التنقل، وزر Message في صفحة مقدم الخدمة يفتح المحادثة مباشرة.

**تعتمد على:** 45 (المحادثة)، 17 (صفحة العرض وعارض صور المعرض)  
**تعتمد عليها لاحقاً:** 57 (مراجعة التجاوب والتناسق)

```text
[Quasario · Step 46/61 · Frontend · UC-U09 U10]
Project: Quasario, a local services directory (Laravel REST API + React SPA + admin panel). The full
context is in CLAUDE.md: read it before anything else.
Feature: Chat screens.
Done before this step: step 45 'Chat API'; step 17 'Page view screen'.
Already in the codebase (reuse it, do not recreate it): PageActions Message button, notification link to chats, VerifiedBadge, Avatar.
Later steps build on this: step 57 'Responsive and consistency pass'.
So keep these stable and named exactly as written: /chats and /chats/:id.

Task: Chats. Attach the original chat screens.
- /chats (refetch 30s): avatar, name, last message, time, unread badge.
- /chats/:id: bubbles, polling 5s with ?after=last_id, input + attachment (JPG/PNG/PDF ≤5MB; images preview, PDF as
  link fetched with the token), auto-scroll. Wire PageActions "Message" → POST /conversations → /chats/:id.

How to work:
- Read CLAUDE.md first; use its conventions, colors, navigation and API contract exactly.
- Before coding, read the files listed under "Already in the codebase" and reuse them (shared components, api
  client, hooks, types). Then list the files you will create or change, and write them in full (TypeScript, Tailwind).
- API calls in src/api/endpoints/<module>.ts with types in src/api/types.ts (snake_case fields as the API returns
  them); TanStack Query hooks inside the feature folder; invalidate the related queries after each mutation.
- Forms: react-hook-form + zod with the same rules as the backend; show server field errors; map `code` to an
  English message through the shared error helper.
- Every screen has loading, empty and error states and works at 360px and 1280px.
- If the endpoint is not merged yet, mock it in the endpoint file behind `import.meta.env.VITE_USE_MOCKS` with the
  exact response shape from docs/api.md.
- Vitest tests for the zod schemas and one component test for the main screen.
- I attach the UI mockups named above; match their layout and texts.
```

<a id="step-47"></a>
### 47. حذف الحساب

**النوع:** Backend · **المسؤول:** Nazeer · **الحالات:** UC-U05

**ماذا تضيف للبرنامج:** حق المستخدم في حذف بياناته نهائياً. الخدمة التي تُبنى هنا يعيد المسؤول استخدامها عند حذف صفحة مبلّغ عنها.

**تعتمد على:** 2 (قاعدة البيانات: Migrations والـModels والـEnums)، 45 (المحادثة)  
**تعتمد عليها لاحقاً:** 48 (حذف الحساب)، 53 (معالجة البلاغات)

```text
[Quasario · Step 47/61 · Backend · UC-U05]
Project: Quasario, a local services directory (Laravel REST API + React SPA + admin panel). The full
context is in CLAUDE.md: read it before anything else.
Feature: Account deletion API.
Done before this step: step 2 'Database, models and enums'; step 45 'Chat API'.
Already in the codebase (reuse it, do not recreate it): DB cascades (step 'Database'), ImageStorage, private files of verification and chat.
Later steps build on this: step 48 'Delete account screen'; step 53 'Admin reports API'.
So keep these stable and named exactly as written: AccountDeletionService::delete(User) used by DELETE /me and by the admin report handling.

Task: AccountDeletionService::delete(User): delete all files of the user (profile, cover, gallery, place image,
event posts, ID images, chat attachments they sent), revoke tokens, delete the user row (DB cascades the rest).
DELETE /me uses it (no password; UI confirms). Admins can't delete themselves (403).
Test: user with data in every related table → all gone, other users untouched.

How to work:
- Read CLAUDE.md first; use its table names, enums, error codes and API contract exactly.
- Before coding, read the files listed under "Already in the codebase" and reuse them. Then list the files you
  will create or change, and write them in full.
- Thin controllers; rules in Services; validation in FormRequests; ownership in Policies; output via API Resources.
- Pest feature tests in tests/Feature/<Module>/: success, every error listed above (assert the `code`), and
  rejection of a visitor, another user and a banned user where relevant.
- Do not change migrations or other modules unless this task says so. If you must touch a shared file, say why.
- Update docs/api.md with the endpoints (request, response example, error codes).
- Finish with: routes added, how to run the tests, example curl requests, and anything the frontend must know.
```

<a id="step-48"></a>
### 48. حذف الحساب

**النوع:** Frontend · **المسؤول:** Batol · **الحالات:** UC-U05

**ماذا تضيف للبرنامج:** يكمل إعدادات الحساب بخيار الحذف مع تحذير واضح.

**تعتمد على:** 47 (حذف الحساب)، 8 (شاشات الدخول والتسجيل بمرحلتين)  
**تعتمد عليها لاحقاً:** 57 (مراجعة التجاوب والتناسق)

```text
[Quasario · Step 48/61 · Frontend · UC-U05]
Project: Quasario, a local services directory (Laravel REST API + React SPA + admin panel). The full
context is in CLAUDE.md: read it before anything else.
Feature: Delete account screen.
Done before this step: step 47 'Account deletion API'; step 8 'Sign up and sign in screens'.
Already in the codebase (reuse it, do not recreate it): Settings screen, useConfirm, useAuth.
Later steps build on this: step 57 'Responsive and consistency pass'.
So keep these stable and named exactly as written: Settings row 'Delete account'.

Task: Settings → "Delete account" (S07) with useConfirm warning that everything is deleted permanently → DELETE /me
→ clear auth → home.

How to work:
- Read CLAUDE.md first; use its conventions, colors, navigation and API contract exactly.
- Before coding, read the files listed under "Already in the codebase" and reuse them (shared components, api
  client, hooks, types). Then list the files you will create or change, and write them in full (TypeScript, Tailwind).
- API calls in src/api/endpoints/<module>.ts with types in src/api/types.ts (snake_case fields as the API returns
  them); TanStack Query hooks inside the feature folder; invalidate the related queries after each mutation.
- Forms: react-hook-form + zod with the same rules as the backend; show server field errors; map `code` to an
  English message through the shared error helper.
- Every screen has loading, empty and error states and works at 360px and 1280px.
- If the endpoint is not merged yet, mock it in the endpoint file behind `import.meta.env.VITE_USE_MOCKS` with the
  exact response shape from docs/api.md.
- Vitest tests for the zod schemas and one component test for the main screen.
- I attach the UI mockups named above; match their layout and texts.
```

<a id="step-49"></a>
### 49. إدارة الحسابات والحظر وسحب الشارة

**النوع:** Backend · **المسؤول:** Hossin · **الحالات:** UC-A03–A08

**ماذا تضيف للبرنامج:** يعطي المسؤول أداة إيقاف الحسابات المخالفة وسحب الشارة ممن يسيء استخدامها. الحظر يخفي الصفحة والفعاليات ويمنع الدخول فوراً.

**تعتمد على:** 39 (مراجعة الفعاليات (المسؤول))، 7 (المصادقة: التسجيل بمرحلتين والدخول والخروج)  
**تعتمد عليها لاحقاً:** 50 (لوحة الإدارة: الحسابات)، 53 (معالجة البلاغات)

```text
[Quasario · Step 49/61 · Backend · UC-A03–A08]
Project: Quasario, a local services directory (Laravel REST API + React SPA + admin panel). The full
context is in CLAUDE.md: read it before anything else.
Feature: Admin accounts API.
Done before this step: step 39 'Admin event review API'; step 7 'Authentication API'.
Already in the codebase (reuse it, do not recreate it): not_banned middleware, User::active(), Page::visible(), NotificationService, admin controller style.
Later steps build on this: step 50 'Admin accounts screens'; step 53 'Admin reports API'.
So keep these stable and named exactly as written: Account endpoints and AccountResource for admin screens.

Task: Admin accounts.
- GET /admin/accounts?name=&status=active|banned (users only, not admins).
- GET /admin/accounts/{user}: account, page summary, is_verified, status.
- POST /admin/accounts/{user}/ban: banned, delete tokens, notify ACCOUNT_BANNED; banning a banned user → 409.
  /unban: active, notify ACCOUNT_UNBANNED.
- POST /admin/accounts/{user}/revoke-trust-mark: is_verified false, verified_at null, notify TRUST_MARK_REVOKED.
Tests: banned user can't log in and disappears from search, page view and events; non-admin 403.

How to work:
- Read CLAUDE.md first; use its table names, enums, error codes and API contract exactly.
- Before coding, read the files listed under "Already in the codebase" and reuse them. Then list the files you
  will create or change, and write them in full.
- Thin controllers; rules in Services; validation in FormRequests; ownership in Policies; output via API Resources.
- Pest feature tests in tests/Feature/<Module>/: success, every error listed above (assert the `code`), and
  rejection of a visitor, another user and a banned user where relevant.
- Do not change migrations or other modules unless this task says so. If you must touch a shared file, say why.
- Update docs/api.md with the endpoints (request, response example, error codes).
- Finish with: routes added, how to run the tests, example curl requests, and anything the frontend must know.
```

<a id="step-50"></a>
### 50. لوحة الإدارة: الحسابات

**النوع:** Admin (Frontend) · **المسؤول:** Abdulhadi · **الحالات:** UC-A03–A08

**ماذا تضيف للبرنامج:** شاشات المسؤول لإدارة الحسابات.

**تعتمد على:** 49 (إدارة الحسابات والحظر وسحب الشارة)، 42 (لوحة الإدارة: الهيكل ومراجعة الفعاليات)  
**تعتمد عليها لاحقاً:** 58 (اختبارات طرف إلى طرف (Playwright))

```text
[Quasario · Step 50/61 · Admin (Frontend) · UC-A03–A08]
Project: Quasario, a local services directory (Laravel REST API + React SPA + admin panel). The full
context is in CLAUDE.md: read it before anything else.
Feature: Admin accounts screens.
Done before this step: step 49 'Admin accounts API'; step 42 'Admin shell and event review screens'.
Already in the codebase (reuse it, do not recreate it): AdminTable, AdminDetailHeader, useConfirm, VerifiedBadge.
Later steps build on this: step 58 'End-to-end tests'.
So keep these stable and named exactly as written: —

Task: Attach S28. /admin/accounts with search; /admin/accounts/:id (S28) with one Ban or Unban button by status and
"Revoke trust mark" when verified (both useConfirm); /admin/banned list.

How to work:
- Read CLAUDE.md first; use its conventions, colors, navigation and API contract exactly.
- Before coding, read the files listed under "Already in the codebase" and reuse them (shared components, api
  client, hooks, types). Then list the files you will create or change, and write them in full (TypeScript, Tailwind).
- API calls in src/api/endpoints/<module>.ts with types in src/api/types.ts (snake_case fields as the API returns
  them); TanStack Query hooks inside the feature folder; invalidate the related queries after each mutation.
- Forms: react-hook-form + zod with the same rules as the backend; show server field errors; map `code` to an
  English message through the shared error helper.
- Every screen has loading, empty and error states and works at 360px and 1280px.
- If the endpoint is not merged yet, mock it in the endpoint file behind `import.meta.env.VITE_USE_MOCKS` with the
  exact response shape from docs/api.md.
- Vitest tests for the zod schemas and one component test for the main screen.
- I attach the UI mockups named above; match their layout and texts.
```

<a id="step-51"></a>
### 51. مراجعة طلبات التوثيق

**النوع:** Backend · **المسؤول:** Hossin · **الحالات:** UC-A09 A10 A11

**ماذا تضيف للبرنامج:** يكمل دورة التوثيق: المسؤول يرى الهوية بأمان ويمنح الشارة، فيستطيع المستخدم بعدها نشر الفعاليات.

**تعتمد على:** 33 (طلب شارة التوثيق)، 39 (مراجعة الفعاليات (المسؤول))  
**تعتمد عليها لاحقاً:** 52 (لوحة الإدارة: طلبات التوثيق)

```text
[Quasario · Step 51/61 · Backend · UC-A09 A10 A11]
Project: Quasario, a local services directory (Laravel REST API + React SPA + admin panel). The full
context is in CLAUDE.md: read it before anything else.
Feature: Admin verification review API.
Done before this step: step 33 'Verification request API'; step 39 'Admin event review API'.
Already in the codebase (reuse it, do not recreate it): VerificationRequest rows and private ids/ storage (step 'Verification request API'), NotificationService.
Later steps build on this: step 52 'Admin verification screens'.
So keep these stable and named exactly as written: Verification review endpoints.

Task: Admin verification review.
- GET /admin/verification-requests (pending, oldest first); GET /admin/verification-requests/{id};
  GET .../{id}/image streams the private file (admin only).
- POST .../{id}/accept: accepted, reviewed_by/at, user is_verified true + verified_at, notify VERIFICATION_ACCEPTED.
  /reject: notify VERIFICATION_REJECTED. Pending only.
Tests.

How to work:
- Read CLAUDE.md first; use its table names, enums, error codes and API contract exactly.
- Before coding, read the files listed under "Already in the codebase" and reuse them. Then list the files you
  will create or change, and write them in full.
- Thin controllers; rules in Services; validation in FormRequests; ownership in Policies; output via API Resources.
- Pest feature tests in tests/Feature/<Module>/: success, every error listed above (assert the `code`), and
  rejection of a visitor, another user and a banned user where relevant.
- Do not change migrations or other modules unless this task says so. If you must touch a shared file, say why.
- Update docs/api.md with the endpoints (request, response example, error codes).
- Finish with: routes added, how to run the tests, example curl requests, and anything the frontend must know.
```

<a id="step-52"></a>
### 52. لوحة الإدارة: طلبات التوثيق

**النوع:** Admin (Frontend) · **المسؤول:** Abdulhadi · **الحالات:** UC-A09 A10 A11

**ماذا تضيف للبرنامج:** شاشة المسؤول لمراجعة الهويات ومنح الشارة.

**تعتمد على:** 51 (مراجعة طلبات التوثيق)، 42 (لوحة الإدارة: الهيكل ومراجعة الفعاليات)  
**تعتمد عليها لاحقاً:** 58 (اختبارات طرف إلى طرف (Playwright))

```text
[Quasario · Step 52/61 · Admin (Frontend) · UC-A09 A10 A11]
Project: Quasario, a local services directory (Laravel REST API + React SPA + admin panel). The full
context is in CLAUDE.md: read it before anything else.
Feature: Admin verification screens.
Done before this step: step 51 'Admin verification review API'; step 42 'Admin shell and event review screens'.
Already in the codebase (reuse it, do not recreate it): AdminTable, AdminDetailHeader.
Later steps build on this: step 58 'End-to-end tests'.
So keep these stable and named exactly as written: —

Task: /admin/verification list and /admin/verification/:id showing the ID image fetched as a blob with the token
(never a public URL), Accept / Reject.

How to work:
- Read CLAUDE.md first; use its conventions, colors, navigation and API contract exactly.
- Before coding, read the files listed under "Already in the codebase" and reuse them (shared components, api
  client, hooks, types). Then list the files you will create or change, and write them in full (TypeScript, Tailwind).
- API calls in src/api/endpoints/<module>.ts with types in src/api/types.ts (snake_case fields as the API returns
  them); TanStack Query hooks inside the feature folder; invalidate the related queries after each mutation.
- Forms: react-hook-form + zod with the same rules as the backend; show server field errors; map `code` to an
  English message through the shared error helper.
- Every screen has loading, empty and error states and works at 360px and 1280px.
- If the endpoint is not merged yet, mock it in the endpoint file behind `import.meta.env.VITE_USE_MOCKS` with the
  exact response shape from docs/api.md.
- Vitest tests for the zod schemas and one component test for the main screen.
- I attach the UI mockups named above; match their layout and texts.
```

<a id="step-53"></a>
### 53. معالجة البلاغات

**النوع:** Backend · **المسؤول:** Hossin · **الحالات:** UC-A17 A18 A19

**ماذا تضيف للبرنامج:** يغلق دائرة الحماية: المسؤول يحذف المحتوى المخالف أو يتجاهل البلاغ، ويصل الإشعار لصاحب الشأن.

**تعتمد على:** 43 (البلاغات (جهة المستخدم))، 47 (حذف الحساب)، 49 (إدارة الحسابات والحظر وسحب الشارة)  
**تعتمد عليها لاحقاً:** 54 (لوحة الإدارة: البلاغات)

```text
[Quasario · Step 53/61 · Backend · UC-A17 A18 A19]
Project: Quasario, a local services directory (Laravel REST API + React SPA + admin panel). The full
context is in CLAUDE.md: read it before anything else.
Feature: Admin reports API.
Done before this step: step 43 'Reports API (users)'; step 47 'Account deletion API'; step 49 'Admin accounts API'.
Already in the codebase (reuse it, do not recreate it): Report rows (step 'Reports API'), AccountDeletionService (step 'Account deletion API'), NotificationService.
Later steps build on this: step 54 'Admin reports screens'.
So keep these stable and named exactly as written: Report handling endpoints.

Task: Admin reports.
- GET /admin/reports?status=pending&type=; GET /admin/reports/{id} with a target preview (page card / event / post).
- POST /admin/reports/{id}/delete-content (transaction): page → AccountDeletionService on the owner; event → delete
  event; post → delete post (if it is the last post, delete the event); notify the owner CONTENT_DELETED (reporter not
  notified); remaining pending reports on the same target are resolved or removed by cascade.
- POST /admin/reports/{id}/dismiss: dismissed, handled_by/at, notify reporter REPORT_DISMISSED.
Tests for each target type.

How to work:
- Read CLAUDE.md first; use its table names, enums, error codes and API contract exactly.
- Before coding, read the files listed under "Already in the codebase" and reuse them. Then list the files you
  will create or change, and write them in full.
- Thin controllers; rules in Services; validation in FormRequests; ownership in Policies; output via API Resources.
- Pest feature tests in tests/Feature/<Module>/: success, every error listed above (assert the `code`), and
  rejection of a visitor, another user and a banned user where relevant.
- Do not change migrations or other modules unless this task says so. If you must touch a shared file, say why.
- Update docs/api.md with the endpoints (request, response example, error codes).
- Finish with: routes added, how to run the tests, example curl requests, and anything the frontend must know.
```

<a id="step-54"></a>
### 54. لوحة الإدارة: البلاغات

**النوع:** Admin (Frontend) · **المسؤول:** Abdulhadi · **الحالات:** UC-A17 A18 A19

**ماذا تضيف للبرنامج:** شاشة المسؤول لمعالجة البلاغات.

**تعتمد على:** 53 (معالجة البلاغات)، 42 (لوحة الإدارة: الهيكل ومراجعة الفعاليات)  
**تعتمد عليها لاحقاً:** 58 (اختبارات طرف إلى طرف (Playwright))

```text
[Quasario · Step 54/61 · Admin (Frontend) · UC-A17 A18 A19]
Project: Quasario, a local services directory (Laravel REST API + React SPA + admin panel). The full
context is in CLAUDE.md: read it before anything else.
Feature: Admin reports screens.
Done before this step: step 53 'Admin reports API'; step 42 'Admin shell and event review screens'.
Already in the codebase (reuse it, do not recreate it): AdminTable, AdminDetailHeader, EventDetail/PageCard previews, useConfirm.
Later steps build on this: step 58 'End-to-end tests'.
So keep these stable and named exactly as written: —

Task: Attach S31, S32. One list with type badge (Page / Event / Post) and front-end tabs; detail with reason,
reporter and content preview; "Delete content" (useConfirm; for a page warn that the whole account is deleted) and
"Dismiss".

How to work:
- Read CLAUDE.md first; use its conventions, colors, navigation and API contract exactly.
- Before coding, read the files listed under "Already in the codebase" and reuse them (shared components, api
  client, hooks, types). Then list the files you will create or change, and write them in full (TypeScript, Tailwind).
- API calls in src/api/endpoints/<module>.ts with types in src/api/types.ts (snake_case fields as the API returns
  them); TanStack Query hooks inside the feature folder; invalidate the related queries after each mutation.
- Forms: react-hook-form + zod with the same rules as the backend; show server field errors; map `code` to an
  English message through the shared error helper.
- Every screen has loading, empty and error states and works at 360px and 1280px.
- If the endpoint is not merged yet, mock it in the endpoint file behind `import.meta.env.VITE_USE_MOCKS` with the
  exact response shape from docs/api.md.
- Vitest tests for the zod schemas and one component test for the main screen.
- I attach the UI mockups named above; match their layout and texts.
```

<a id="step-55"></a>
### 55. المساعد الذكي الكامل

**النوع:** Backend · **المسؤول:** Hossin · **الحالات:** UC-V08

**ماذا تضيف للبرنامج:** يحوّل المساعد من مطابقة كلمات إلى فهم حقيقي للمشكلة، مع بقاء البديل الاحتياطي ضماناً.

**تعتمد على:** 30 (المساعد الذكي نسخة أولى (بالبديل الاحتياطي))، 35 (موقع الصفحة وخريطة الصفحات)  
**تعتمد عليها لاحقاً:** 56 (شاشة المساعد الذكي)

```text
[Quasario · Step 55/61 · Backend · UC-V08]
Project: Quasario, a local services directory (Laravel REST API + React SPA + admin panel). The full
context is in CLAUDE.md: read it before anything else.
Feature: AI assistant final.
Done before this step: step 30 'Assistant endpoint v0'; step 35 'Page location and map API'.
Already in the codebase (reuse it, do not recreate it): AssistantService, providers and POST /assistant (steps 'AI spike' and 'Assistant v0'), page_locations.
Later steps build on this: step 56 'Assistant screen'.
So keep these stable and named exactly as written: Same POST /assistant contract, now with the external provider.

Task: Finish the assistant using docs/decisions.md.
- AssistantService uses HttpAiProvider when AI_API_KEY is set; Keyword fallback on failure, invalid JSON or >15s.
- Return the provider's reply text when present, otherwise a standard English reply.
- Cache identical problems 1 hour; log provider errors without the key; 503 assistantUnavailable only if both fail.
Tests with Http::fake: success, invalid JSON, timeout.

How to work:
- Read CLAUDE.md first; use its table names, enums, error codes and API contract exactly.
- Before coding, read the files listed under "Already in the codebase" and reuse them. Then list the files you
  will create or change, and write them in full.
- Thin controllers; rules in Services; validation in FormRequests; ownership in Policies; output via API Resources.
- Pest feature tests in tests/Feature/<Module>/: success, every error listed above (assert the `code`), and
  rejection of a visitor, another user and a banned user where relevant.
- Do not change migrations or other modules unless this task says so. If you must touch a shared file, say why.
- Update docs/api.md with the endpoints (request, response example, error codes).
- Finish with: routes added, how to run the tests, example curl requests, and anything the frontend must know.
```

<a id="step-56"></a>
### 56. شاشة المساعد الذكي

**النوع:** Frontend · **المسؤول:** Batol · **الحالات:** UC-V08

**ماذا تضيف للبرنامج:** الزر الدائري في كل الشاشات: الزائر يصف مشكلته فيقترح عليه التطبيق مقدمَي خدمة مناسبين.

**تعتمد على:** 55 (المساعد الذكي الكامل)، 16 (الصفحة الرئيسية والبحث والتصفية)  
**تعتمد عليها لاحقاً:** 57 (مراجعة التجاوب والتناسق)

```text
[Quasario · Step 56/61 · Frontend · UC-V08]
Project: Quasario, a local services directory (Laravel REST API + React SPA + admin panel). The full
context is in CLAUDE.md: read it before anything else.
Feature: Assistant screen.
Done before this step: step 55 'AI assistant final'; step 16 'Home, search and filter screens'.
Already in the codebase (reuse it, do not recreate it): Round AI button in MainLayout, PageCard, useGeolocation.
Later steps build on this: step 57 'Responsive and consistency pass'.
So keep these stable and named exactly as written: /assistant screen.

Task: Attach S04 and the original assistant screen. /assistant: textarea "Describe your problem", optional "Use my
location" (works without it), Send → reply + up to 2 PageCards. Loading up to 15s, error with retry. Visitors allowed.

How to work:
- Read CLAUDE.md first; use its conventions, colors, navigation and API contract exactly.
- Before coding, read the files listed under "Already in the codebase" and reuse them (shared components, api
  client, hooks, types). Then list the files you will create or change, and write them in full (TypeScript, Tailwind).
- API calls in src/api/endpoints/<module>.ts with types in src/api/types.ts (snake_case fields as the API returns
  them); TanStack Query hooks inside the feature folder; invalidate the related queries after each mutation.
- Forms: react-hook-form + zod with the same rules as the backend; show server field errors; map `code` to an
  English message through the shared error helper.
- Every screen has loading, empty and error states and works at 360px and 1280px.
- If the endpoint is not merged yet, mock it in the endpoint file behind `import.meta.env.VITE_USE_MOCKS` with the
  exact response shape from docs/api.md.
- Vitest tests for the zod schemas and one component test for the main screen.
- I attach the UI mockups named above; match their layout and texts.
```

## التثبيت والتسليم (8/1 – 21/1)

<a id="step-57"></a>
### 57. مراجعة التجاوب والتناسق

**النوع:** Frontend · **المسؤول:** Batol · **الحالات:** كل الشاشات

**ماذا تضيف للبرنامج:** يضمن أن التطبيق يبدو جيداً على الهاتف والحاسوب في يوم العرض، وأن الشاشات متناسقة.

**تعتمد على:** 56 (شاشة المساعد الذكي)، 48 (حذف الحساب)، 46 (شاشات المحادثة)، 44 (زر ونموذج التبليغ)  
**تعتمد عليها لاحقاً:** لا شيء

```text
[Quasario · Step 57/61 · Frontend · كل الشاشات]
Project: Quasario, a local services directory (Laravel REST API + React SPA + admin panel). The full
context is in CLAUDE.md: read it before anything else.
Feature: Responsive and consistency pass.
Done before this step: step 56 'Assistant screen'; step 48 'Delete account screen'; step 46 'Chat screens'; step 44 'Report dialog'.
Already in the codebase (reuse it, do not recreate it): All user screens.
Later steps build on this: none (end of a chain).
So keep these stable and named exactly as written: —

Task: Go through every user screen at 360px, 768px and 1280px. List layout bugs, inconsistent spacing/colors,
missing loading/empty/error states, and fix them using the shared components only.

How to work:
- Read CLAUDE.md first; use its conventions, colors, navigation and API contract exactly.
- Before coding, read the files listed under "Already in the codebase" and reuse them (shared components, api
  client, hooks, types). Then list the files you will create or change, and write them in full (TypeScript, Tailwind).
- API calls in src/api/endpoints/<module>.ts with types in src/api/types.ts (snake_case fields as the API returns
  them); TanStack Query hooks inside the feature folder; invalidate the related queries after each mutation.
- Forms: react-hook-form + zod with the same rules as the backend; show server field errors; map `code` to an
  English message through the shared error helper.
- Every screen has loading, empty and error states and works at 360px and 1280px.
- If the endpoint is not merged yet, mock it in the endpoint file behind `import.meta.env.VITE_USE_MOCKS` with the
  exact response shape from docs/api.md.
- Vitest tests for the zod schemas and one component test for the main screen.
- I attach the UI mockups named above; match their layout and texts.
```

<a id="step-58"></a>
### 58. اختبارات طرف إلى طرف (Playwright)

**النوع:** Test · **المسؤول:** Abdulhadi · **الحالات:** المسارات الحرجة

**ماذا تضيف للبرنامج:** يتأكد آلياً أن المسارات الستة التي ستُعرض أمام اللجنة تعمل من أولها لآخرها، ويكشف أي كسر تسببه تعديلات الأيام الأخيرة.

**تعتمد على:** 50 (لوحة الإدارة: الحسابات)، 52 (لوحة الإدارة: طلبات التوثيق)، 54 (لوحة الإدارة: البلاغات)، 32 (شاشات التقييم الموثّق)، 41 (إدارة فعالياتي)  
**تعتمد عليها لاحقاً:** 59 (مراجعة الأمان)

```text
[Quasario · Step 58/61 · Test · المسارات الحرجة]
Project: Quasario, a local services directory (Laravel REST API + React SPA + admin panel). The full
context is in CLAUDE.md: read it before anything else.
Feature: End-to-end tests.
Done before this step: step 50 'Admin accounts screens'; step 52 'Admin verification screens'; step 54 'Admin reports screens'; step 32 'Endorsement screens'; step 41 'My events screens'.
Already in the codebase (reuse it, do not recreate it): All features and data-testid attributes; DemoSeeder.
Later steps build on this: step 59 'Security review'.
So keep these stable and named exactly as written: frontend/e2e suite run before every demo.

Task: Playwright tests in frontend/e2e against a seeded backend (migrate:fresh --seed):
(1) sign up 2 steps → sign in → edit profile → add gallery image; (2) visitor search + filter → page → Save opens
Sign in required; (3) endorsement request → accept 5 stars → shown on page, average changes; (4) verification
request → admin accepts → user creates event → admin accepts → visitor sees it; (5) report page → admin deletes
content → page gone from search; (6) admin bans → user can't sign in → unban. Add data-testid where missing and a
manually triggered GitHub Actions job.
```

<a id="step-59"></a>
### 59. مراجعة الأمان

**النوع:** Test · **المسؤول:** الجميع · **الحالات:** —

**ماذا تضيف للبرنامج:** يغلق الثغرات قبل أن تجدها اللجنة: مستخدم يعدّل بيانات غيره، أو صورة هوية مكشوفة.

**تعتمد على:** 58 (اختبارات طرف إلى طرف (Playwright))  
**تعتمد عليها لاحقاً:** 60 (الإنتاج والنسخ الاحتياطي)

```text
[Quasario · Step 59/61 · Test · —]
Project: Quasario, a local services directory (Laravel REST API + React SPA + admin panel). The full
context is in CLAUDE.md: read it before anything else.
Feature: Security review.
Done before this step: step 58 'End-to-end tests'.
Already in the codebase (reuse it, do not recreate it): The whole repository.
Later steps build on this: step 60 'Production and backups'.
So keep these stable and named exactly as written: —

Task: Security review of the repository. Check and fix: middleware and Policy on every route (try IDOR by changing
ids); validation and file checks on every input; private files never public; rate limits (auth, assistant, reports,
messages); CORS only FRONTEND_URL; APP_DEBUG=false in production; no secrets committed; tokens deleted on
logout/ban/reset; frontend has no dangerouslySetInnerHTML and clears the token on 401.
Output a table (file, issue, fix) and the patches.
```

<a id="step-60"></a>
### 60. الإنتاج والنسخ الاحتياطي

**النوع:** DevOps · **المسؤول:** Syrien · **الحالات:** —

**ماذا تضيف للبرنامج:** النسخة النهائية التي تُعرض وتُسلّم، مع نسخ احتياطية تحمي البيانات من أي خطأ قبل العرض.

**تعتمد على:** 9 (النشر على الخادم التجريبي)، 59 (مراجعة الأمان)  
**تعتمد عليها لاحقاً:** 61 (بيانات وسيناريو العرض)

```text
[Quasario · Step 60/61 · DevOps · —]
Project: Quasario, a local services directory (Laravel REST API + React SPA + admin panel). The full
context is in CLAUDE.md: read it before anything else.
Feature: Production and backups.
Done before this step: step 9 'Staging deployment'; step 59 'Security review'.
Already in the codebase (reuse it, do not recreate it): Staging setup and deploy.sh (step 'Staging deployment').
Later steps build on this: step 61 'Demo data and script'.
So keep these stable and named exactly as written: —

Task: Production and backups.
- Production .env checklist, `php artisan optimize`, queue worker, scheduler, HTTPS, log rotation.
- scripts/backup.sh: nightly mysqldump (gzip) + tar of storage/app, keep 7 days, copy elsewhere; cron entry.
  scripts/restore.sh and a restore test procedure in docs/operations.md.
- Uptime check on GET /api/v1/health.
```

<a id="step-61"></a>
### 61. بيانات وسيناريو العرض

**النوع:** DevOps · **المسؤول:** Syrien · **الحالات:** —

**ماذا تضيف للبرنامج:** يجعل العرض أمام اللجنة مقنعاً ومضموناً: بيانات واقعية وسيناريو مكتوب ولابتوب احتياطي.

**تعتمد على:** 60 (الإنتاج والنسخ الاحتياطي)  
**تعتمد عليها لاحقاً:** لا شيء

```text
[Quasario · Step 61/61 · DevOps · —]
Project: Quasario, a local services directory (Laravel REST API + React SPA + admin panel). The full
context is in CLAUDE.md: read it before anything else.
Feature: Demo data and script.
Done before this step: step 60 'Production and backups'.
Already in the codebase (reuse it, do not recreate it): All seeders and features.
Later steps build on this: none (end of a chain).
So keep these stable and named exactly as written: —

Task: Demo preparation.
- DemoShowcaseSeeder: realistic English data, 40 pages in 6 cities with photos, bios, sections, locations;
  25 endorsements; 5 accepted + 2 pending events; a pending verification request; 2 pending reports; accounts
  admin@quasario.test, verified@quasario.test, user@quasario.test (passwords from .env).
- docs/demo-script.md: 10-minute demo covering the four actors with exact clicks and accounts.
- Instructions to run everything locally on a laptop as a backup.
```
