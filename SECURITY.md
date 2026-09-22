# Sécurité de l'application

Le projet applique les contrôles OWASP Top 10 au niveau de l'application :
contrôle d'accès des commandes et de l'administration, validation stricte des
entrées et fichiers, protection CSRF/XSS par CSP avec nonce, limitation de débit,
chiffrement des sessions et des secrets marketing, journalisation des événements
d'authentification et traitement générique des erreurs en production.

## Configuration de production requise

- `APP_ENV=production` et `APP_DEBUG=false`
- `APP_KEY` persistant et secret
- `DB_SSLMODE=require`
- `SESSION_DRIVER=database`, `SESSION_ENCRYPT=true`
- `SESSION_SECURE_COOKIE=true`, `SESSION_COOKIE=__Host-alo-session`
- `LOG_CHANNEL=stderr`, `LOG_LEVEL=info`
- secrets Vercel marqués **Sensitive** et jamais ajoutés au dépôt

Après la première installation, supprimer `SETUP_TOKEN` de Vercel. Consulter les
journaux Vercel pour les échecs et blocages de connexion administrateur, les
changements de mot de passe, la création de comptes et les échecs d'API externe.

## Maintenance

Dependabot vérifie Composer et npm chaque semaine. Avant chaque mise en
production, exécuter :

```sh
composer audit --locked
npm audit --omit=dev
php artisan test
npm run build
```

Ne publiez pas les détails d'une vulnérabilité exploitable dans une issue
publique. Utilisez le canal privé du propriétaire du dépôt GitHub.
