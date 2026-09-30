<?php

namespace Tests\Unit;

use App\Services\Content\SafeHtml;
use PHPUnit\Framework\TestCase;

class SafeHtmlTest extends TestCase
{
    public function test_removes_script_and_event_handlers(): void
    {
        $out = SafeHtml::clean('<p onclick="evil()">Hi</p><script>alert(1)</script>');
        $this->assertStringNotContainsString('<script', $out);
        $this->assertStringNotContainsString('onclick', $out);
        $this->assertStringContainsString('<p>Hi</p>', $out);
    }

    public function test_blocks_javascript_urls(): void
    {
        $out = SafeHtml::clean('<a href="javascript:alert(1)">x</a>');
        $this->assertStringNotContainsString('javascript:', $out);
    }

    public function test_keeps_formatting_and_links(): void
    {
        $out = SafeHtml::clean('<h2>T</h2><ul><li>a</li></ul><a href="https://x.id" target="_blank">b</a>');
        $this->assertStringContainsString('<h2>T</h2>', $out);
        $this->assertStringContainsString('noopener', $out);
    }

    public function test_null_and_empty_passthrough(): void
    {
        $this->assertNull(SafeHtml::clean(null));
        $this->assertSame('', SafeHtml::clean(''));
    }
}
