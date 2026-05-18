# GitHub Actions Deployment Notes

This patch adds:

- `.github/workflows/deploy.yaml`

## Required GitHub Repository Secrets

Add these under:

GitHub repo → Settings → Secrets and variables → Actions → New repository secret

Required secrets:

- `SFTP_HOST`
- `SFTP_PORT`
- `SFTP_USERNAME`
- `SFTP_PASSWORD`
- `PROD_PATH`

## Behavior

- Deploys automatically when `main` receives a push.
- Can also be run manually from the GitHub Actions tab.
- Uses `lftp` over SFTP.
- Excludes `.git`, `.github`, docs, backups, exports, `.env`, SQL dumps, zip files, and logs.
- Mirrors the repository root to the remote production path.
- Deletes remote files that no longer exist locally, except excluded files.

## Important

Confirm `PROD_PATH` carefully before the first deployment.

Examples might look like:

- `/home/ACCOUNT/public_html/eudoravaultacademy.com`
- `/home/ACCOUNT/public_html`
- `/home/ACCOUNT/eudoravaultacademy.com`

The correct value depends on the hosting account structure.
