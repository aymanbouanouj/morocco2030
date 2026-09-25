# Security Policy

## Supported versions

| Version | Supported |
|---|---|
| 1.x | Yes |
| Earlier development snapshots | No |

Security fixes target the current `main` branch and the latest published 1.x release. No long-term-support branch is currently declared.

## Report vulnerabilities privately

Do not open a public issue for an exploitable vulnerability, active secret, personal data exposure, or private infrastructure detail.

Use GitHub Private Vulnerability Reporting through the repository's Security tab. If that channel is temporarily unavailable, retain the report privately and contact the maintainer through a non-public channel listed on the maintainer's GitHub profile. No private contact credential is stored in this repository.

Include the affected component and version, prerequisites, reproducible steps, impact, and a minimal safe proof of concept. Redact credentials, tokens, private records, session identifiers, and infrastructure details.

## Disclosure process

The maintainer will acknowledge a complete report, reproduce and assess it, prepare and verify a fix, and coordinate disclosure. Do not test against systems or data you do not own or have explicit permission to assess.

## Security expectations

- Never commit `.env`, application keys, provider tokens, database or mail passwords, private keys, logs, sessions, local databases, or private uploads.
- Use unique keys and credentials per environment; production should use `APP_ENV=production`, `APP_DEBUG=false`, HTTPS, and secure cookies.
- Keep Composer and npm lockfiles reviewed and run `composer audit` and `npm audit` regularly.
- Preserve CSRF protection, output escaping, authorization policies, account-type separation, and audit redaction.
- Tests must use SQLite in memory and `Http::fake`; real external API calls are prohibited.
- If a secret enters Git history, rotate it and publish only from a sanitized lineage. Deleting the current file is insufficient.

This policy describes responsible disclosure and engineering controls; it is not a claim of formal penetration testing or certification.
