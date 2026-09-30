<?php

namespace Tests\Feature;

use App\Http\Middleware\SetLocale;
use App\Services\Locale\LocaleManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Tests\TestCase;

class LocaleTest extends TestCase
{
    use RefreshDatabase;

    public function test_locale_middleware_applies_session_locale(): void
    {
        if (! class_exists(SetLocale::class) || ! class_exists(LocaleManager::class)) {
            $this->markTestSkipped('Agent 1 locale middleware not present yet.');
        }

        $active = LocaleManager::activeLocales();
        $this->assertNotEmpty($active);
        $default = LocaleManager::defaultLocale();
        $this->assertContains($default, $active);

        // Direct middleware invocation (no provider registration needed).
        $other = collect($active)->first(fn ($l) => $l !== $default) ?? $default;
        $request = Request::create('/', 'GET');
        $request->setLaravelSession(session()->driver());
        session()->put('locale', $other);

        (new SetLocale)->handle($request, fn ($req) => response('ok'));

        $this->assertEquals($other, app()->getLocale());
    }

    public function test_locale_manager_number_formatting(): void
    {
        if (! class_exists(LocaleManager::class)) {
            $this->markTestSkipped('Agent 1 locale middleware not present yet.');
        }

        $this->assertSame('1.000.000', LocaleManager::formatNumber(1000000, 'id'));
        $this->assertSame('1,000,000', LocaleManager::formatNumber(1000000, 'en'));
    }
}
