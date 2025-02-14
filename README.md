# GitHub Org/Repo Stats

This project is a simple Laravel application to gather Pull Request data from a GitHub organization and repo for further calculations and statistics. It was typically used as a leaderboard to show most additions, deletions, and net changes over time.

## Installation

```bash
touch database/database.sqlite
cp .env.example .env
composer install
php artisan key:generate
php artisan migrate
```

Add your GitHub personal token, org name, and repo to the `.env` file.

```bash
GITHUB_TOKEN=
GITHUB_ORG=
GITHUB_REPO=
```

## Run the data commands

```bash
php artisan github:prs
php artisan github:pr-data
```

## Example Query (2025)
```mysql
SELECT user,
       SUM(total_files)                       AS total_files,
       SUM(total_additions)                   AS total_additions,
       SUM(total_deletions)                   AS total_deletions,
       SUM(total_additions - total_deletions) AS net_lines_changed
FROM pull_requests
WHERE merged_at_year = 2025
GROUP BY user
ORDER BY net_lines_changed ASC, total_deletions DESC, total_additions DESC;
```

Note: This project was hacked together quickly, don't take it too seriously :D
