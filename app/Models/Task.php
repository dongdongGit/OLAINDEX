<?php

namespace App\Models;

class Task extends Model
{
    const UPDATED_AT = null;

    protected $casts = [
        'onedrive_id'  => 'integer',
        'completed_at' => 'datetime',
        'failed_at'    => 'datetime'
    ];

    protected $columns = [
        'id',
        'gid',
        'status',
        'onedrive_id',
        'source',
        'target',
        'created_at',
        'completed_at',
        'failed_at'
    ];

    public function onedrive()
    {
        return $this->belongsTo(OneDrive::class)->withTrashed();
    }
}
