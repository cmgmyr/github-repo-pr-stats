<?php
declare(strict_types=1);
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PullRequest extends Model
{
    protected $guarded = [];

    protected $casts = [
        'merged_at' => 'datetime',
    ];
}
