# Deployment

Ship from GitHub `main`. Prefer a free PaaS first; AWS is optional.

## 1. GitHub

```bash
cd lumenai
git init -b main
git add .
git commit -m "Initial LumenAI Laravel SaaS with RAG and MCP"
gh repo create lumenai --private --source=. --remote=origin --push
```

CI runs on every push to `main` (`.github/workflows/ci.yml`).

## 2. Free / cheap hosts (recommended)

| Host | Why |
| --- | --- |
| [Render](https://render.com) free web + MySQL | Simple PHP Docker |
| [Railway](https://railway.app) hobby | MySQL plugin |
| [Fly.io](https://fly.io) | Dockerfile in repo |
| Laravel Cloud | First-party Laravel host |

Required env on the host:

```
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain
APP_KEY=base64:...
DB_CONNECTION=mysql
DB_HOST=...
DB_DATABASE=lumenai
DB_USERNAME=...
DB_PASSWORD=...
OPENAI_API_KEY=...
QUEUE_CONNECTION=database
```

Release command:

```bash
php artisan migrate --force
php artisan config:cache
php artisan route:cache
```

## 3. Docker

```bash
docker compose up --build
```

App: `http://localhost:8000`  
MySQL: `localhost:3306`

## 4. AWS (optional, not free at scale)

Cheapest realistic path:

1. Lightsail PHP instance or ECS Fargate with the included `Dockerfile`
2. RDS MySQL (or Lightsail database)
3. GitHub Action SSHs or uses ECR on `main`

AWS always-free is not enough for a full Laravel + RDS stack. Use Render/Railway until you have traffic.

## Laravel production files

- `public/` is the web root
- Never deploy `.env` in git
- Run a queue worker: `php artisan queue:work`
- Health: `GET /up`
