# المساهمة في Quasario / Contributing

## القاعدة الأساسية / The main rule

لا أحد يرفع تعديلات مباشرة إلى `main`. كل تعديل يمرّ عبر فرع ثم Pull Request يراجعه زميل واحد على الأقل.

Nobody pushes directly to `main`. Every change goes through a branch and a Pull Request reviewed by at least one teammate.

## الخطوات / Steps

1. حدّث نسختك / Update your copy:
   ```bash
   git checkout main
   git pull origin main
   ```
2. أنشئ فرعاً / Create a branch:
   ```bash
   git checkout -b feature/UC-U16-request-endorsement
   ```
3. اعمل وارفع تعديلاتك / Commit and push:
   ```bash
   git add .
   git commit -m "feat(endorsements): request an endorsement"
   git push -u origin feature/UC-U16-request-endorsement
   ```
4. افتح Pull Request إلى `main` على GitHub واملأ القالب، واطلب مراجعة من زميل.
   Open a Pull Request into `main`, fill in the template and request a review.
5. بعد الموافقة ادمج الـPR واحذف الفرع.
   After approval, merge the PR and delete the branch.

## تسمية الفروع / Branch names

| النوع / Type | المثال / Example |
|---|---|
| ميزة / Feature | `feature/UC-U16-request-endorsement` |
| إصلاح / Fix | `fix/login-banned-message` |
| توثيق / Docs | `docs/api-endpoints` |
| إعدادات / Chore | `chore/update-dependencies` |

اكتب رقم حالة الاستخدام (UC) في اسم الفرع عندما تتعلق به.
Put the use case id (UC) in the branch name when there is one.

## رسائل الـCommit / Commit messages

نتبع [Conventional Commits](https://www.conventionalcommits.org/): `feat`، `fix`، `docs`، `test`، `refactor`، `chore`.

```
feat(auth): two-step sign up
fix(events): return event to pending after edit
docs: add API error codes
```

## قبل فتح الـPR / Before opening a PR

- اتبع القواعد في `CLAUDE.md` (المسارات، أسماء الحقول، رموز الأخطاء).
- لا تعدّل migration دُمج سابقاً، أضف واحداً جديداً.
- لا ترفع ملف `.env` أو أي كلمات مرور أو مفاتيح.
- لا تضف ميزات غير موجودة في التصميم المعتمد في `docs/design/`.

- Follow the rules in `CLAUDE.md` (routes, field names, error codes).
- Never edit a merged migration; add a new one.
- Never commit `.env` files, passwords or API keys.
- Do not add features that are not in the approved design in `docs/design/`.
