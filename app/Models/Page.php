<?php

namespace App\Models;

use App\Models\Redirect;
use App\Traits\Translatable;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use Translatable;

    protected array $translatableAttributes = ['title', 'content', 'meta_title', 'meta_description'];
    protected $fillable = [
        'type',
        'title',
        'slug',
        'content',
        'blocks',
        'status',
        'published_at',
        'show_in_footer',
        'meta_title',
        'meta_description',
        'keywords',
        'meta_image',
    ];

    protected $casts = [
        'blocks' => 'array',
        'status' => 'boolean',
        'show_in_footer' => 'boolean',
        'published_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        // Sanitasi XSS tersimpan SEBELUM validasi/aturan lain (create + update).
        static::saving(function (Page $page) {
            $page->content = \App\Services\Content\SafeHtml::clean($page->content);
            $blocks = $page->blocks;
            if (is_string($blocks)) {
                $blocks = json_decode($blocks, true);
            }
            if (is_array($blocks)) {
                foreach ($blocks as $i => $block) {
                    if (($block['type'] ?? null) === 'html' && isset($block['data']['html'])) {
                        $blocks[$i]['data']['html'] = \App\Services\Content\SafeHtml::clean($block['data']['html']);
                    }
                }
                $page->blocks = $blocks;
            }
        });

        static::updating(function (Page $page) {
            $dirty = array_intersect_key($page->getDirty(), array_flip(['title', 'slug', 'content', 'blocks']));
            if ($dirty !== []) {
                $orig = $page->getOriginal();
                PageRevision::create([
                    'page_id' => $page->getKey(),
                    'title' => $orig['title'] ?? null,
                    'slug' => $orig['slug'] ?? null,
                    'content' => $orig['content'] ?? null,
                    'blocks' => is_string($orig['blocks'] ?? null) ? json_decode($orig['blocks'], true) : ($orig['blocks'] ?? null),
                    'created_by' => auth()->id(),
                ]);
            }
            if ($page->isDirty('slug')) {
                $old = $page->getOriginal('slug');
                $new = $page->slug;
                if ($old && $new && $old !== $new) {
                    try {
                        Redirect::updateOrCreate(
                            ['from_path' => Redirect::normalize('/page/'.$old)],
                            ['to_path' => '/page/'.$new, 'code' => 301, 'is_active' => true]
                        );
                    } catch (\Throwable) {
                    }
                }
            }
        });
    }

    public function revisions()
    {
        return $this->hasMany(PageRevision::class)->latest();
    }

    public function scopeActive($q)
    {
        return $q->where('status', true)
            ->where('slug', '!=', 'trashed')
            ->where(function ($qq) {
                $qq->whereNull('published_at')->orWhere('published_at', '<=', now());
            });
    }

    public function scopeScheduled($q)
    {
        return $q->where('status', true)->where('published_at', '>', now());
    }

    public function scopeTrashedStatus($q)
    {
        return $q->where('status', false);
    }

    public function isTrashedStatus(): bool
    {
        return ! $this->status;
    }
}
