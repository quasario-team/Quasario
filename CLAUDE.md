# Quasario — project context for Claude

> Claude Code reads this file automatically from the repository root.

## 1. What we are building
Quasario is a **local services directory** (graduation project, team of 4, deadline 24/1/2027).
Service providers (doctors, plumbers, designers, clinics, ...) create a **page** with their job title, city, bio,
work gallery, social links, location on a map and sections (team members / contact info). Visitors search and
filter pages, view them, see **verified endorsements** (ratings 1–5 with work photos) and browse **events**.
Registered users chat, save pages, report content, request endorsements and request a **verification badge**
(trust mark). Verified users can publish events (reviewed by an admin). An **AI assistant** suggests suitable
providers for a problem the visitor describes. An **admin panel** manages accounts, verification, events and reports.

Actors: **Visitor** (not logged in) ⊂ **User** ⊂ **Verified user** (User with `is_verified = true`), and **Admin**.
External systems: **Maps API** (Leaflet + OpenStreetMap tiles) and **AI API** (behind an `AiProvider` interface).

## 2. Stack and repository
- Monorepo: `backend/` (Laravel, PHP 8.3+, MySQL 8) and `frontend/` (React + Vite + TypeScript).
- Backend: Laravel latest stable, **Sanctum API tokens (Bearer)**, FormRequests, Policies, API Resources,
  Pest tests, Laravel Pint. Queue/cache driver `database` (Redis optional in production). Mail `log` in dev.
- Frontend: React Router, TanStack Query, axios, react-hook-form + zod, Tailwind CSS, Leaflet/react-leaflet,
  Vitest + Testing Library, Playwright for E2E.
- Admin panel lives in the same React app under `/admin` with its own `AdminLayout`.
- UI language: **English, LTR** (as in the UI mockups). Mobile first, must also work on desktop (360px and 1280px).
- Colors: background `#2b2b2b`, primary buttons yellow `#fdd54f`, cards green `#116754`.
- Mobile bottom nav: **Events / Map / Home / Chats / Profile** + a round AI assistant button. Desktop: top nav.

```
backend/app/
  Enums/  Models/  Policies/  Services/  Exceptions/DomainError.php
  Http/Controllers/Api/V1/{Auth,Public,Me,Admin}/  Http/Requests/  Http/Resources/  Http/Middleware/
backend/database/{migrations,factories,seeders}   backend/routes/api.php   backend/tests/Feature/<Module>/
frontend/src/
  app/ (router, providers, queryClient)  api/ (client.ts, endpoints/<module>.ts, types.ts)
  components/ui/  components/map/  layouts/  hooks/  lib/
  features/<feature>/{pages,components,hooks,schemas.ts}   features/admin/<feature>/...
```

## 3. Conventions (follow them in every change)
- All routes under `/api/v1`. JSON fields are **snake_case** exactly like DB columns (no camelCase conversion).
- GET read, POST create/action (accept, reject, ban...), PATCH update, DELETE delete.
  **Any request with a file upload is POST multipart** (PHP does not parse multipart PATCH).
- Lists are paginated (20 per page) with Laravel's `data / meta / links` format. Use eager loading (no N+1).
- Request flow: Route → Middleware (`auth:sanctum`, `not_banned`, `admin`, `verified`) → FormRequest (validation)
  → Policy (ownership) → thin Controller → Service (business rules, notifications) → API Resource.
- **The server checks everything**, even if the UI hides a button. Every endpoint has a test that another
  user / a visitor is rejected.
- Error body is always: `{"message": "...", "code": "<errorCode>", "errors": {"field": ["..."]}}`.
  Business-rule errors are thrown as `DomainError(code, httpStatus)`.
- Error codes (from the SSDs) and HTTP status:
  - 422: `requiredField`, `emptyField`, `invalidField`, `weakPassword`, `invalidDates`, `emptyProblem`,
    `unsupportedFile`, `alreadyUsed`, `selfSelection`, `invalidPassword`
  - 401 `invalidCredentials` · 403 `accountBanned`, `notVerified`, `notAdmin`, `ownContent` · 404 `notFound`
  - 409 `alreadyReported`, `alreadyRequested`, `lastPost` · 503 `assistantUnavailable`
  - `connectionError` is frontend-only (network failure / 5xx).
- Files: images JPG/PNG ≤ 5MB (chat attachments JPG/PNG/PDF ≤ 5MB). Public disk for profile/cover/gallery/
  place/event images. **Private disk** for ID images and chat attachments, served only through authorized endpoints.
- Delete confirmations use one shared `ConfirmDialog`. Visitors see Save / Message / Report buttons, which open a
  shared `SignInRequiredDialog`.
- Frontend: one file per module in `api/endpoints/`, TanStack Query hooks per feature, zod schemas mirror
  backend validation, every screen has loading / empty / error states. Error messages are built from `code`.
- Enum names/values are identical in PHP (`app/Enums`) and TypeScript (`api/types.ts`).
- Git: short branches `feature/UC-U16-request-endorsement`, Conventional Commits, PR reviewed by one teammate,
  CI green (pint, pest, eslint, tsc, vitest, build). Never edit a merged migration; add a new one.
- Do not invent features that are not in the design. If something is unclear, choose the option least likely to
  cause bugs and say what you chose.

## 4. Database (MySQL, 23 tables) — source: ERD
All tables have `id` bigint PK and `created_at/updated_at` unless noted. `→` = FK with on-delete rule.
- `cities(name UK)` · `job_categories(name UK)` · `job_titles(job_category_id → job_categories RESTRICT, name; UK(job_category_id,name))`
- `users(username UK varchar50, email UK, phone varchar20, password, role enum(user,admin)=user, status enum(active,banned)=active, is_verified bool=false, verified_at NULL, remember_token)`
- `password_reset_tokens(email PK, token, created_at)` (Laravel default)
- `notifications(user_id → users CASCADE, type enum(21 types), data json, read_at NULL)` — custom table, not Laravel's morph table; User uses `RoutesNotifications`, not `Notifiable`.
- `conversations(user_one_id → users CASCADE, user_two_id → users CASCADE, last_message_at NULL; UK(user_one_id,user_two_id) with user_one_id < user_two_id)`
- `messages(conversation_id → conversations CASCADE, sender_id → users CASCADE, body text NULL, attachment_path NULL, read_at NULL)`
- `verification_requests(user_id → users CASCADE, id_image, status enum(pending,accepted,rejected), reviewed_by → users NULL SET NULL, reviewed_at NULL)`
- `pages(user_id → users CASCADE UNIQUE, display_name varchar100, job_title_id → job_titles RESTRICT, city_id → cities RESTRICT, profile_image NULL, cover_image NULL, bio NULL, study NULL, experience NULL)` — one page per user, created by completeRegistration; job category is derived from the job title.
- `page_locations(page_id → pages CASCADE UNIQUE, latitude decimal(10,7), longitude decimal(10,7), place_image NULL, floor enum NULL, directions text NULL)`
- `social_links(page_id → pages CASCADE, platform enum, url varchar2048; UK(page_id,platform))`
- `gallery_images(page_id → pages CASCADE, image_path, description NULL)`
- `saved_pages(user_id → users CASCADE, page_id → pages CASCADE; UK(user_id,page_id))`
- `sections(page_id → pages CASCADE, name varchar100, type enum(members,info), phone NULL, email NULL)`
- `section_members(section_id → sections CASCADE, user_id → users CASCADE; UK(section_id,user_id))`
- `section_invitations(section_id → sections CASCADE, invitee_id → users CASCADE, status enum(pending,accepted,rejected), responded_at NULL)`
- `endorsement_requests(requester_id → users CASCADE, endorser_id → users CASCADE, status enum(pending,accepted,rejected), responded_at NULL)`
- `endorsement_request_images(endorsement_request_id → endorsement_requests CASCADE, gallery_image_id → gallery_images CASCADE; UK pair)` (no timestamps)
- `endorsements(endorsement_request_id → endorsement_requests CASCADE UNIQUE, comment text, rating tinyint 1..5)` — the endorsed page is the requester's page (derived).
- `reports(reporter_id → users CASCADE, target_type enum(page,event,post), page_id NULL → pages CASCADE, event_id NULL → events CASCADE, event_post_id NULL → event_posts CASCADE, reason varchar500, status enum(pending,resolved,dismissed), handled_by → users NULL SET NULL, handled_at NULL)` — exactly one target FK is set.
- `events(user_id → users CASCADE, name varchar150, description text, start_date date, end_date date ≥ start_date, latitude, longitude, city_id → cities RESTRICT (copied from owner's page city at creation), status enum(pending,accepted,rejected))`
- `event_posts(event_id → events CASCADE, image_path, text)` — an event has 1..* posts; the last post cannot be deleted.

Derived, never stored: average rating, job category of a page, unread counts.
"Only one pending X" rules are enforced in services (MySQL has no partial unique index).
Deleting a user is one `DELETE FROM users` — cascades remove page, gallery, sections, memberships, requests,
endorsements, chats, reports, events. Admin FKs (`reviewed_by`, `handled_by`) become NULL.

## 5. Enums
- AccountRole: USER, ADMIN · AccountStatus: ACTIVE, BANNED
- RequestStatus (verification, endorsement, invitation): PENDING, ACCEPTED, REJECTED
- EventStatus: PENDING, ACCEPTED, REJECTED · ReportStatus: PENDING, RESOLVED, DISMISSED
- SectionType: MEMBERS, INFO
- Floor: BASEMENT, GROUND, FIRST, SECOND, THIRD, FOURTH, FIFTH_OR_ABOVE
- SocialPlatform: WHATSAPP, FACEBOOK, LINKEDIN, YOUTUBE, X, INSTAGRAM, TELEGRAM, GITHUB, WEBSITE
- NotificationType (21): NEW_MESSAGE, ENDORSEMENT_REQUEST, ENDORSEMENT_ACCEPTED, ENDORSEMENT_REJECTED,
  ENDORSEMENT_DELETED, SECTION_DELETED, SECTION_INVITATION, REMOVED_FROM_SECTION, INVITATION_ACCEPTED,
  INVITATION_REJECTED, MEMBER_LEFT, TRUST_MARK_REVOKED, ACCOUNT_BANNED, ACCOUNT_UNBANNED, VERIFICATION_ACCEPTED,
  VERIFICATION_REJECTED, EVENT_ACCEPTED, EVENT_REJECTED, EVENT_DELETED, CONTENT_DELETED, REPORT_DISMISSED
(DB stores lowercase snake values, e.g. `pending`, `fifth_or_above`, `new_message`.)

## 6. Global business rules (from the approved Narrative)
- Sign up has two steps: step 1 email, username, phone, password (≥ 8 chars); step 2 job category → job title,
  city (required), experience (optional). No auto-login after sign up (go to Sign in), no email confirmation.
- Login with email **or** username. Banned accounts cannot log in (`accountBanned`). Admin uses the same Sign in
  screen and is redirected to `/admin`. "Forgot password" always shows the same message whether the email exists or not.
- Home: pages sorted by average verified rating desc, then newest; pages without rating show "-".
  Text search on display name + job title. Filter: multiple cities and multiple job titles.
- Banned users' pages and events are hidden from everyone except admins.
- No self-actions: no reporting own content (`ownContent`), no endorsement request to yourself (`selfSelection`).
- One pending at a time: verification request per user; endorsement request per (requester, endorser);
  report per (reporter, target); invitation per (section, invitee).
- No rejection reasons anywhere (verification, events, endorsements). Reporter is not notified on content deletion.
- Events: only verified users manage events; created as PENDING; **any saved edit returns the event to PENDING**;
  public lists show ACCEPTED events only, sorted by start date desc; ended events stay visible. Filter events by city.
  If a badge is revoked, the user's events stay as they are, but the user can no longer manage them.
- Report on a page → "delete content" deletes the whole account (same cascade as account deletion).

## 7. API contract (one endpoint per SSD operation, prefix /api/v1)
Public: `GET /home` getHomePage · `GET /pages?q=&cities[]=&jobs[]=` searchPages/filterPages · `GET /filters`
getFilterOptions (cities, job categories with titles, social platforms, floors) · `GET /pages/{page}` getPage ·
`GET /pages/{page}/location` getPageLocation · `GET /endorsements/{id}` getEndorsement · `GET /map/pages`
openInteractiveMap · `POST /assistant` askAssistant(problem, lat?, lng?) · `GET /events?name=&city_id=`
getEvents/searchEvents/filterEvents · `GET /events/{event}` getEvent (showEventLocation is frontend-only).

Auth: `POST /auth/register/check` register (validate step 1 only, saves nothing) · `POST /auth/register`
completeRegistration (all fields; creates user + page in one transaction) · `POST /auth/login` (returns token,
user with role) · `POST /auth/logout` · `POST /auth/forgot-password` requestPasswordReset ·
`POST /auth/reset-password` resetPassword(token, email, password).

Me (auth): `GET /me/profile` getProfileForEdit · `POST /me/profile` updateProfile (multipart, only changed fields;
password change needs current_password) · `DELETE /me` deleteAccount · `GET /me/page` getMyPage ·
`POST /pages/{page}/save` savePage · `DELETE /pages/{page}/save` removeSavedPage · `GET /me/saved-pages` ·
`POST /me/gallery` addGalleryImage · `DELETE /me/gallery/{image}` · `POST /me/social-links` addSocialLink ·
`DELETE /me/social-links/{link}` · `POST /me/page/location` setPageLocation (multipart) ·
`PATCH /me/page/location` updatePageLocation (coordinates only) · `POST /me/sections/members`
createMembersSection(name, member_ids) · `POST /me/sections/info` createInfoSection(name, phone, email) ·
`PATCH /me/sections/{section}` updateSection · `DELETE /me/sections/{section}` ·
`POST /me/sections/{section}/invitations` inviteMember · `DELETE /me/sections/{section}/members/{user}` removeMember ·
`GET /me/section-invitations` · `POST /section-invitations/{id}/accept|reject` · `POST /sections/{section}/leave` ·
`GET /people?name=` searchPeople · `POST /endorsement-requests` requestEndorsement(endorser_id, image_ids[]) ·
`GET /endorsement-requests` (incoming) · `GET /endorsement-requests/{id}` · `POST /endorsement-requests/{id}/accept`
(comment, rating) · `POST /endorsement-requests/{id}/reject` · `DELETE /endorsements/{id}` deleteEndorsement ·
`GET /me/verification` getVerificationStatus · `POST /me/verification` requestVerification(id_image) ·
`POST /pages/{page}/reports` · `POST /events/{event}/reports` · `POST /posts/{post}/reports` (reason) ·
`POST /conversations` openConversation(other_user_id) · `GET /conversations` · `GET /conversations/{id}/messages?after=`
· `POST /messages` sendMessage(receiver_id, text, file) · `GET /notifications` · `POST /notifications/read`.

Verified (auth + verified): `GET /me/events` · `POST /me/events` createEvent (multipart: name, description,
start_date, end_date, latitude, longitude, post_image, post_text) · `POST /me/events/{event}` updateEvent
(multipart) · `DELETE /me/events/{event}` · `DELETE /me/posts/{post}` deletePost.

Admin (auth + admin): `GET /admin/accounts?name=&status=` getAccounts/searchAccounts/getBannedAccounts ·
`GET /admin/accounts/{user}` · `POST /admin/accounts/{user}/revoke-trust-mark|ban|unban` ·
`GET /admin/verification-requests` · `GET /admin/verification-requests/{id}` (+ `/image`, private) ·
`POST /admin/verification-requests/{id}/accept|reject` · `GET /admin/events?status=&name=`
getSubmittedEvents/getPublishedEvents/searchAdminEvents · `GET /admin/events/{event}` getAdminEvent ·
`POST /admin/events/{event}/accept|reject` · `DELETE /admin/events/{event}` (delete published) ·
`GET /admin/reports` · `GET /admin/reports/{id}` · `POST /admin/reports/{id}/delete-content` · `POST /admin/reports/{id}/dismiss`.

## 8. Definition of Done for any feature
Endpoint matches the contract; all validation rules and alternative flows implemented with the right error code;
Pest tests for success, one validation error and unauthorized access; screen matches the UI mockup, works at 360px
and 1280px, has loading/empty/error states; no console errors; lint, types, tests and build pass.
