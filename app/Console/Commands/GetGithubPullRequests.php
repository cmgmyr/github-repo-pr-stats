<?php

namespace App\Console\Commands;

use App\Models\PullRequest;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class GetGithubPullRequests extends Command
{
    protected $signature = 'github:prs';
    protected $description = 'Generate Github stats for the team';

    public function handle(): int
    {
        $client = Http::withToken(config('services.github.token'))
            ->accept('application/vnd.github+json')
            ->withHeader('X-GitHub-Api-Version', '2022-11-28')
            ->baseUrl('https://api.github.com');

        $endOfYear = false;
        $page = 1;
        while ($endOfYear === false) {
            $response = $client->get('repos/'.config('services.github.org').'/'.config('services.github.repo').'/pulls', [
                'state' => 'closed',
                'base' => 'master',
                'sort' => 'created',
                'direction' => 'desc',
                'per_page' => 100,
                'page' => $page,
            ]);

            collect($response->json())->each(function ($pullRequest) use (&$endOfYear) {
                if ($pullRequest['merged_at'] === null) {
                    return;
                }

                if (Carbon::parse($pullRequest['merged_at'])->lt(Carbon::create(2024, 1, 1))) {
                    $endOfYear = true;

                    return;
                }

                PullRequest::firstOrCreate([
                    'number' => $pullRequest['number'],
                ], [
                    'url' => $pullRequest['html_url'],
                    'title' => $pullRequest['title'],
                    'user' => $pullRequest['user']['login'],
                    'merged_at' => $pullRequest['merged_at'],
                    'merged_at_year' => Carbon::parse($pullRequest['merged_at'])->year,
                ]);
            });

            $page++;
        }

        return Command::SUCCESS;
    }
}
