# Eudora Vault Academy Website

Starter website project for Eudora Vault Academy.

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
├── README.md
├── .gitignore
├── assets/
│   ├── brand/
│   ├── css/
│   │   └── site.css
│   ├── img/
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

- Main content is currently in `index.html`.
- Site styling is in `assets/css/site.css`.
- Minimal JavaScript is in `assets/js/site.js`.
- Add official logo files to `assets/brand/` when ready.
- Add approved photos to `assets/img/` when ready.

## Important operating rule

Before future patch work, use a fresh date/time-stamped source zip as the source of truth.
