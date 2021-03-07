<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AgentPerformanceStat extends Model
{
    protected $table = 'agent_performance_stats';

    protected $fillable = ['user_id', 'saving_volume', 'saving_value', 'customer_acquired'];

    protected $hidden = ['created_at', 'updated_at'];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
