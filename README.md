# Eudora Vault Academy Website

Starter website project for Eudora Vault Academy. The current Phase 1 site includes homepage content, a contact/interest page, and an Eudora Vertical Challenge event page.

Domain: eudoravaultacademy.com

## Current purpose

This is a simple starter launch structure for a basic marketing website. It is intentionally lightweight and does not require a database or build tools.

## Local setup

Recommended local folder:

```text
C:\laragon\www\eudoravaultacademy
```

After copying these files into that folder, open the folder in VS Code:

```powershell
cd C:\laragon\www\eudoravaultacademy
code .
```

With Laragon running, test in a browser at one of these:

```text
http://localhost/eudoravaultacademy/
http://eudoravaultacademy.test
```

The `.test` URL depends on your Laragon virtual host settings.

## Git setup

Create an empty GitHub repository first. Suggested repository name:

```text
eudora-vault-academy
```

Then run:

```powershell
cd C:\laragon\www\eudoravaultacademy
git init
git branch -M main
git add index.html README.md .gitignore assets docs
git commit -m "Initial website project setup"
git remote add origin https://github.com/YOUR-USERNAME/eudora-vault-academy.git
git push -u origin main
git checkout -b develop
git push -u origin develop
git status
```

Replace `YOUR-USERNAME` with your GitHub username.

## File structure

```text
eudoravaultacademy/
├── index.html
├── contact.html
├── contact-submit.php
├── thank-you.html
├── eudora-vertical-challenge.html
├── README.md
├── .gitignore
├── assets/
│   ├── brand/
│   ├── css/
│   │   └── site.css
│   ├── img/
│   │   └── eudora-vertical-challenge-2026.png
│   └── js/
│       └── site.js
└── docs/
    ├── planning/
    │   ├── launch-checklist.md
    │   └── project-foundation.md
    └── proposal/
        └── proposal-outline.md
```

## Editing notes

- Main homepage content is in `index.html`.
- The interest/contact form page is in `contact.html`.
- The event landing page is in `eudora-vertical-challenge.html`.
- Site styling is in `assets/css/site.css`.
- Minimal JavaScript is in `assets/js/site.js`.
- Add official logo files to `assets/brand/` when ready.
- Add approved facility, coach, and athlete photos to `assets/img/` when ready.
- The interest form posts to `contact-submit.php`, which uses PHP `mail()` with UTF-8 quoted-printable email formatting.
- Successful submissions redirect to `thank-you.html`.
- A honeypot and server-side validation are included. The existing Google Form remains as a fallback.
- The PHP handler requires a PHP-enabled host with a working local mail transport; local browser preview alone does not prove email delivery.
- No database or SMTP credentials are used. Check that the host permits sending with `no-reply@eudoravaultacademy.com` as From and that domain SPF/DKIM are configured for the hosting mail server.

## Contact form deployment smoke test

1. Test the form locally via Laragon (PHP must be enabled). Confirm that validation errors are handled and that normal static pages work.
2. Push to `develop` for review; promote to `main` only after approval.
3. On production, submit a test athlete called `EVA Form Test` with a personal best such as `15’`, and an available email address.
4. Verify the visitor lands on `thank-you.html` only when mail handoff succeeds.
5. Verify that the EVA Gmail inbox receives the email with subject `EVA Form Test: EVA Interest Form` and displays `Current Personal Best: 15’` without `â€™`. Check spam if necessary.
6. Verify Reply-To if an athlete/guardian email was supplied. Verify that empty honeypot/invalid entries are handled.
7. If mail is accepted by PHP but never delivered, review the hosting outgoing-mail policy and SPF/DKIM; consider authorized host SMTP or a transactional email service.
8. Do not send real athlete personal details in test submissions. Remove test emails after testing.

## Important operating rule

Before future patch work, use a fresh date/time-stamped source zip as the source of truth.
