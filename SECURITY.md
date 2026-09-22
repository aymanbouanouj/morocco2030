# Security Policy

## Supported Version

Security fixes are prepared against the current `main` branch. No long-term support branches are declared.

## Reporting a Vulnerability

Do not open a public issue containing exploit details, credentials, personal data, or private infrastructure information. Use GitHub private vulnerability reporting after the repository owner enables it. Until that channel is available, retain the report privately and contact the repository owner through a non-public channel listed on their GitHub profile.

Include the affected component, reproduction steps, impact, and any safe proof of concept. Remove real secrets and personal data from evidence. The maintainer should acknowledge a complete report, assess severity, prepare a fix, and coordinate disclosure before publishing details.

## Secret Handling

Never commit `.env`, application keys, provider tokens, database passwords, mail credentials, private keys, logs, sessions, local databases, or private uploads. If a secret enters Git history, rotate it and publish from a sanitized lineage; deleting the current file alone is insufficient.
