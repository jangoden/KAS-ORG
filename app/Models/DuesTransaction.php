<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DuesTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'type',
        'description',
        'recipient_name',
        'amount',
        'member_id',
        'period_year',
        'period_month',
    ];

    protected static function booted(): void
    {
        static::creating(function (DuesTransaction $duesTransaction) {
            if ($duesTransaction->type === 'masuk') {
                // Set jumlah default iuran
                $duesTransaction->amount = 10000;

                // Set deskripsi otomatis berdasarkan member dan periode
                $member = Member::find($duesTransaction->member_id);
                if ($member) {
                    $month = Carbon::create(null, $duesTransaction->period_month)->isoFormat('MMMM');
                    $year = $duesTransaction->period_year;
                    $duesTransaction->description = "Kas {$member->name} periode {$month} {$year}";
                }
            }
        });
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    protected $casts = [
        'date' => 'date',
    ];

}
