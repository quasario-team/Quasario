# Quasario

دليل خدمات محلي: مقدّمو الخدمات ينشئون صفحة فيها المهنة والمدينة والمعرض والموقع، والزوار يبحثون ويصفّون ويرون التقييمات الموثّقة والفعاليات، مع مساعد ذكي ولوحة إدارة.
مشروع تخرّج لفريق من 4 أشخاص، الموعد النهائي 24/1/2027.

A local services directory: providers publish a page (job, city, gallery, location), visitors search, filter and read verified endorsements and events, with an AI assistant and an admin panel. Graduation project, team of 4, deadline 24/1/2027.

## هيكل المستودع / Repository layout

```
quasario/
├── backend/        Laravel REST API (/api/v1, Sanctum)
├── frontend/       React + Vite + TypeScript (includes the admin panel under /admin)
├── docs/
│   ├── design/     Use Case, Narrative, SSD, Activity, Class, ERD (approved)
│   ├── plan/       Development plan + step-by-step feature prompts
│   ├── proposal/   Project proposal (PDF)
│   └── ui/         UI mockups (HTML, zipped)
├── .claude/skills/ Claude Code project skill
├── .github/        PR and issue templates
├── CLAUDE.md       Full project context: stack, conventions, database, API contract
└── CONTRIBUTING.md How we work: branches, commits, pull requests
```

## الفريق / Team

| الاسم / Name | الدور / Role |
|---|---|
| Nazeer | Backend |
| Hossin | Backend |
| Batol | Frontend |
| Abdulhadi | Frontend |

## المتطلبات / Prerequisites

- PHP 8.3+ and Composer
- MySQL 8
- Node.js 22 LTS and npm
- Git

## التشغيل / Getting started

الكود لم يُنشأ بعد. ستُضاف خطوات التشغيل التفصيلية هنا وفي `backend/README.md` و`frontend/README.md` عند إنشاء المشروعين (الخطوة 1 في خطة التطوير).

The application code is not created yet. Setup steps will be added here and in `backend/README.md` and `frontend/README.md` when the projects are created (step 1 of the development plan).

```bash
git clone https://github.com/quasario-team/quasario.git
cd quasario
```

## طريقة العمل / How we work

اقرأ [CONTRIBUTING.md](CONTRIBUTING.md) قبل أول تعديل. وقبل كتابة أي ميزة اقرأ [CLAUDE.md](CLAUDE.md).

Read [CONTRIBUTING.md](CONTRIBUTING.md) before your first change and [CLAUDE.md](CLAUDE.md) before any feature.
