<?php

namespace App\Console\Commands;

use App\Models\PullRequest;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class GetGithubPullRequestData extends Command
{
    protected $signature = 'github:pr-data';
    protected $description = 'Generate Github stats for the team';

    public function handle(): int
    {
        $client = Http::withToken(config('services.github.token'))
            ->accept('application/vnd.github+json')
            ->withHeader('X-GitHub-Api-Version', '2022-11-28')
            ->baseUrl('https://api.github.com');

        $pullRequests = PullRequest::query()
            ->where('merged_at', '>=', Carbon::create(2024, 1, 1))
            ->where(function($query) {
                $query->whereNull('total_files')->orWhereNull('total_additions')->orWhereNull('total_deletions');
            })
            ->orderByDesc('merged_at')
            ->get();

        $pullRequests->each(function (PullRequest $pullRequest, $i) use ($client) {
            $response = $client->get('repos/'.config('services.github.org').'/'.config('services.github.repo').'/pulls/' . $pullRequest->number)->object();
            $pullRequest->update([
                'total_files' => $response->changed_files,
                'total_additions' => $response->additions,
                'total_deletions' => $response->deletions,
            ]);

            if ($i % 10 === 0) {
                sleep(1);
            }
        });

        return Command::SUCCESS;
    }
}
