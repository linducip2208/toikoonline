<?php

namespace App\Services\Content;

use App\Models\EmailTemplate;
use App\Models\SmsTemplate;
use Illuminate\Support\Facades\Schema;

/**
 * Multilingual template renderer. Replaces {{var}} placeholders safely:
 * no eval, unknown vars left intact, values HTML-escaped except $safeKeys.
 *
 * Agent 3 mailer wiring (do NOT edit Agent 3 files — paste this at the call site):
 *
 *   [$subject, $body] = \App\Services\Content\TemplateRenderer::render(
 *       'order_created', app()->getLocale(), ['name' => $user->name, 'code' => $order->code]
 *   );
 */
class TemplateRenderer
{
    /**
     * @return array{0: string, 1: string} [subject, body]
     */
    public static function render(string $identifier, ?string $locale = null, array $vars = [], array $safeKeys = []): array
    {
        $locale = strtolower(trim((string) ($locale ?: app()->getLocale())));

        $email = static::findEmail($identifier, $locale);
        if ($email) {
            return [
                static::replaceVars((string) $email->subject, $vars, $safeKeys, true),
                static::replaceVars((string) $email->default_text, $vars, $safeKeys, false),
            ];
        }

        $sms = static::findSms($identifier, $locale);
        if ($sms) {
            return ['', static::replaceVars((string) $sms->body, $vars, $safeKeys, true)];
        }

        return ['', ''];
    }

    public static function replaceVars(string $text, array $vars, array $safeKeys = [], bool $escape = true): string
    {
        return preg_replace_callback('/\{\{\s*([a-zA-Z0-9_.]+)\s*\}\}/', function ($m) use ($vars, $safeKeys, $escape) {
            $key = $m[1];
            if (! array_key_exists($key, $vars) || $vars[$key] === null) {
                return $m[0]; // unknown vars left intact
            }
            $value = (string) $vars[$key];
            if ($escape && ! in_array($key, $safeKeys, true)) {
                return e($value);
            }

            return $value;
        }, $text) ?? $text;
    }

    protected static function findEmail(string $identifier, string $locale): ?EmailTemplate
    {
        try {
            if (! Schema::hasTable('email_templates')) {
                return null;
            }
            $q = EmailTemplate::where('identifier', $identifier)->where('status', true);
            if (Schema::hasColumn('email_templates', 'locale')) {
                $localized = (clone $q)->where('locale', $locale)->first();
                if ($localized) {
                    return $localized;
                }
                $fallback = (clone $q)->where('locale', 'id')->first();
                if ($fallback) {
                    return $fallback;
                }
            }

            return $q->first();
        } catch (\Throwable) {
            return null;
        }
    }

    protected static function findSms(string $identifier, string $locale): ?SmsTemplate
    {
        try {
            if (! Schema::hasTable('sms_templates')) {
                return null;
            }
            $q = SmsTemplate::where('identifier', $identifier)->where('status', true);
            if (Schema::hasColumn('sms_templates', 'locale')) {
                $localized = (clone $q)->where('locale', $locale)->first();
                if ($localized) {
                    return $localized;
                }
                $fallback = (clone $q)->where('locale', 'id')->first();
                if ($fallback) {
                    return $fallback;
                }
            }

            return $q->first();
        } catch (\Throwable) {
            return null;
        }
    }
}
