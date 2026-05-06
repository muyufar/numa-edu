<?php

namespace App\Models;

use App\Models\Concerns\BelongsToSekolah;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Berita extends Model
{
    use BelongsToSekolah;

    protected $fillable = [
        'sekolah_id',
        'judul',
        'slug',
        'ringkasan',
        'isi',
        'is_published',
        'published_at',
        'user_id',
    ];

    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Berita $berita): void {
            if ($berita->isDirty('judul') || empty($berita->slug)) {
                $base = Str::slug($berita->judul) ?: 'berita';
                $slug = $base;
                $n = 1;
                while (
                    static::query()
                        ->where('slug', $slug)
                        ->where('id', '!=', $berita->id ?? 0)
                        ->exists()
                ) {
                    $slug = $base.'-'.$n++;
                }
                $berita->slug = $slug;
            }

            if ($berita->is_published && $berita->published_at === null) {
                $berita->published_at = now();
            }

            if (! $berita->is_published) {
                $berita->published_at = null;
            }
        });
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true)->whereNotNull('published_at');
    }
}
