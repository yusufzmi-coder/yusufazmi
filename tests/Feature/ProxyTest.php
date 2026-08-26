<?php

declare(strict_types=1);

it('treats a proxied request as secure', function () {
    // Easypanel terminates TLS at Traefik. If the app cannot see that, it emits
    // http:// links on an https:// site and silently drops HSTS.
    $this->withServerVariables(['HTTP_X_FORWARDED_PROTO' => 'https'])
        ->get('/login')
        ->assertOk()
        ->assertHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
});

it('sees the real client IP rather than the proxy', function () {
    // Login rate limiting keys on the IP; without this every visitor shares one
    // bucket and a single attacker can lock out the whole school.
    $this->withServerVariables([
        'HTTP_X_FORWARDED_PROTO' => 'https',
        'HTTP_X_FORWARDED_FOR' => '203.0.113.9',
    ])->get('/login')->assertOk();

    expect(request()->ip())->toBe('203.0.113.9');
});

it('still refuses HSTS over plain http', function () {
    $this->get('/login')->assertHeaderMissing('Strict-Transport-Security');
});
