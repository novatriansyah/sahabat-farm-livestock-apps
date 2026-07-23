<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DataQualityIssue extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'data_quality_issues';

    protected $fillable = [
        'idempotency_key',
        'issue_code',
        'record_type',
        'record_id',
        'animal_id',
        'tag_id',
        'category',
        'field_name',
        'description',
        'severity',
        'status',
        'blocked_processes',
        'assigned_role',
        'remediation_url',
        'evidence_needed',
        'resolved_by',
        'resolved_at',
        'audit_trail',
    ];

    protected $casts = [
        'blocked_processes' => 'array',
        'audit_trail' => 'array',
        'resolved_at' => 'datetime',
    ];
}
