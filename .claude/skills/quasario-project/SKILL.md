---
name: quasario-project
description: Use when writing, reviewing or planning code for Quasario, the Laravel API + React SPA local services directory graduation project.
---

# Quasario project

A local services directory: providers have a page (job, city, bio, gallery, links, map location, sections); visitors search and filter, view pages, verified endorsements (1–5) and events; users chat, save, report, request endorsements and a verification badge; verified users publish events reviewed by an admin; an AI assistant suggests providers. Actors: Visitor ⊂ User ⊂ Verified user (`is_verified`), Admin. Deadline 24/1/2027.

Full detail when needed (read only the part you need):
- Context, all 23 tables, API contract: `CLAUDE.md` at the repo root
- Ordered 61 steps with per-step prompts and dependencies: `docs/plan/Quasario-Feature-Prompts.md`
- Design (approved, never edit unless asked): `docs/design/` (Narrative = rules, SSD = operations, ERD = table list in CLAUDE.md section 4)

## Stack
Monorepo `backend/` (Laravel, PHP 8.3, MySQL 8, Sanctum Bearer tokens, Pest, Pint) + `frontend/` (React + Vite + TypeScript, React Router, TanStack Query, axios, react-hook-form + zod, Tailwind, react-leaflet + OSM). Admin panel = same React app under `/admin`. Queue/cache `database`. Chat = polling (5s open chat, 30s list). AI behind `AiProvider` with keyword fallback.

## Conventions
- Routes `/api/v1`; JSON **snake_case** like DB columns; lists paginated 20; eager load.
- Uploads are always **POST multipart**. Images JPG/PNG ≤5MB; chat files also PDF. ID images and chat files on the **private** disk.
- Flow: Route → middleware (`auth:sanctum`, `not_banned`, `verified`, `admin`) → FormRequest → Policy → thin Controller → Service → API Resource.
- Errors: `{"message","code","errors"}`. Business rules throw `DomainError(code, status)`. Codes: 422 requiredField emptyField invalidField weakPassword invalidDates emptyProblem unsupportedFile alreadyUsed selfSelection invalidPassword · 401 invalidCredentials · 403 accountBanned notVerified notAdmin ownContent · 404 notFound · 409 alreadyReported alreadyRequested lastPost · 503 assistantUnavailable.
- Server checks everything even when the UI hides it. Notifications only via `NotificationService::send(user, NotificationType, data)`.
- Frontend: endpoints in `src/api/endpoints/<module>.ts`, types in `src/api/types.ts`, shared `components/ui` (ConfirmDialog/useConfirm for every delete, SignInRequiredDialog/useRequireAuth for visitor actions), loading/empty/error states, 360px and 1280px. UI text English; colors #2b2b2b, #fdd54f, #116754; nav Events / Map / Home / Chats / Profile + AI button.
- Enums identical in PHP and TS: RequestStatus/EventStatus PENDING ACCEPTED REJECTED · ReportStatus PENDING RESOLVED DISMISSED · SectionType MEMBERS INFO · Floor (7) · SocialPlatform (9) · NotificationType (21). DB stores lowercase values.
- Never edit a merged migration; add one. Do not rename names that later steps depend on (listed per step in the prompts file).

## Key data rules
- `users` (role user|admin, status active|banned, is_verified) 1–0..1 `pages` (created at completeRegistration; job category derived from job title).
- Deleting a user = one DELETE; FKs cascade. Admin FKs `reviewed_by`/`handled_by` SET NULL. Report on a page → delete content deletes the whole account.
- `reports`: target_type + exactly one of page_id/event_id/event_post_id. `endorsements` 1:1 accepted `endorsement_requests`; images via pivot to `gallery_images`.
- "One pending" rules (verification, endorsement per pair, report per target, invitation) are enforced in services.
- Average rating, unread counts, job category are derived, never stored.

## Behaviour rules
- Sign up: step 1 `POST /auth/register/check` validates only; step 2 `POST /auth/register` creates user + page in one transaction; no auto-login. Login by email or username; banned blocked; admin uses same Sign in.
- Home sort: average verified rating desc, then newest. Search name + job title; filter many cities + many job titles.
- Banned users' pages/events hidden. No self-report, no self-endorsement. No rejection reasons anywhere.
- Events: verified only, created PENDING, any edit → PENDING, public shows ACCEPTED sorted by start date desc, last post can't be deleted, city copied from owner page.
- Members sections invite people, never add directly.

## Working style
- Follow the matching step in the prompts file: reuse what earlier steps built, keep what later steps need stable.
- Write Pest tests (success, each error code, visitor/other user/banned rejected) and Vitest tests for schemas.
- If something is unclear, pick the option least likely to cause bugs, decide, and say what you chose.
- Reply to Syrien in Arabic; code, prompts and identifiers in English.
