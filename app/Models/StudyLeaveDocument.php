<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudyLeaveDocument extends Model
{
    public const TYPE_PLACEMENT_LETTER = 0;
    public const TYPE_SELF_FUNDING_DECLARATION = 1;

    public const TYPE_KEY_PLACEMENT_LETTER = 'placement_letter';
    public const TYPE_KEY_SELF_FUNDING_DECLARATION = 'self_funding_declaration';

    protected $fillable = [
        'study_leave_id',
        'document_type',
        'document_path',
    ];

    protected $casts = [
        'document_type' => 'integer',
    ];

    public static function codeFromKey(string $key): ?int
    {
        return match ($key) {
            self::TYPE_KEY_PLACEMENT_LETTER => self::TYPE_PLACEMENT_LETTER,
            self::TYPE_KEY_SELF_FUNDING_DECLARATION => self::TYPE_SELF_FUNDING_DECLARATION,
            default => null,
        };
    }

    public static function keyFromCode(int $code): ?string
    {
        return match ($code) {
            self::TYPE_PLACEMENT_LETTER => self::TYPE_KEY_PLACEMENT_LETTER,
            self::TYPE_SELF_FUNDING_DECLARATION => self::TYPE_KEY_SELF_FUNDING_DECLARATION,
            default => null,
        };
    }

    public function studyLeave()
    {
        return $this->belongsTo(StudyLeave::class, 'study_leave_id');
    }
}
