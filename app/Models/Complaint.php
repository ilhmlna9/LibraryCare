<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Complaint extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_number',
        'user_id',
        'visitor_name',
        'visitor_email',
        'visitor_phone',
        'visitor_telegram',
        'category_id',
        'title',
        'description',
        'attachment',
        'status',
        'source',
        'admin_id',
    ];

    public static function generateTicketNumber(): string
    {
        $year = date('Y');
        $prefix = "LIB-{$year}-";

        $lastComplaint = self::where('ticket_number', 'LIKE', "{$prefix}%")
            ->orderBy('id', 'desc')
            ->first();

        if ($lastComplaint) {
            $lastNumber = intval(substr($lastComplaint->ticket_number, strlen($prefix)));
            $nextNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $nextNumber = '0001';
        }

        return $prefix . $nextNumber;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function admin(): BelongsTo
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(ComplaintCategory::class, 'category_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(ComplaintReply::class)->orderBy('created_at', 'asc');
    }

    public function statusHistories(): HasMany
    {
        return $this->hasMany(ComplaintStatusHistory::class)->orderBy('created_at', 'desc');
    }

    public function satisfaction(): HasOne
    {
        return $this->hasOne(CustomerSatisfaction::class);
    }

    public function notificationLogs(): HasMany
    {
        return $this->hasMany(NotificationLog::class)->orderBy('created_at', 'desc');
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'pending' => [
                'label' => 'Menunggu',
                'bg' => 'bg-amber-100 text-amber-800 dark:bg-amber-900/40 dark:text-amber-300 border border-amber-300 dark:border-amber-700',
                'icon' => 'clock',
            ],
            'processing' => [
                'label' => 'Diproses',
                'bg' => 'bg-sky-100 text-sky-800 dark:bg-sky-900/40 dark:text-sky-300 border border-sky-300 dark:border-sky-700',
                'icon' => 'refresh-cw',
            ],
            'resolved' => [
                'label' => 'Selesai',
                'bg' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900/40 dark:text-emerald-300 border border-emerald-300 dark:border-emerald-700',
                'icon' => 'check-circle-2',
            ],
            'rejected' => [
                'label' => 'Ditolak',
                'bg' => 'bg-rose-100 text-rose-800 dark:bg-rose-900/40 dark:text-rose-300 border border-rose-300 dark:border-rose-700',
                'icon' => 'x-circle',
            ],
            default => [
                'label' => ucfirst($this->status),
                'bg' => 'bg-slate-100 text-slate-800 dark:bg-slate-800 dark:text-slate-200',
                'icon' => 'help-circle',
            ],
        };
    }

    public function getSourceBadgeAttribute(): array
    {
        return match ($this->source) {
            'website' => ['label' => 'Website', 'icon' => 'globe', 'color' => 'indigo'],
            'whatsapp' => ['label' => 'WhatsApp', 'icon' => 'message-circle', 'color' => 'emerald'],
            'telegram' => ['label' => 'Telegram', 'icon' => 'send', 'color' => 'sky'],
            'google_forms' => ['label' => 'Google Forms', 'icon' => 'file-spreadsheet', 'color' => 'purple'],
            default => ['label' => ucfirst($this->source), 'icon' => 'inbox', 'color' => 'slate'],
        };
    }
}

