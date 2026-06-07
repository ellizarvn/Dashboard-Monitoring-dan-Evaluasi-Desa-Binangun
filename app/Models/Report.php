<?php
// ============================================================
// app/Models/Report.php
// ============================================================
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Laporan dokumen desa dengan status Draft/Published.
 */
class Report extends Model
{
    protected $table = 'reports';

    protected $fillable = [
        'title', 'type', 'report_date', 'author_id', 'status',
    ];

    protected function casts(): array
    {
        return [
            'report_date' => 'date',
        ];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }

    public function isPublished(): bool
    {
        return $this->status === 'Published';
    }
}
