<?php

declare(strict_types=1);

it('sends the baseline security headers', function () {
    $response = $this->get('/login');

    $response->assertHeader('X-Content-Type-Options', 'nosniff')
        ->assertHeader('X-Frame-Options', 'DENY')
        ->assertHeader('Referrer-Policy', 'strict-origin-when-cross-origin');
});

it('does not send HSTS over plain http', function () {
    // Setting it in local development would pin http://localhost into the browser's
    // HSTS cache for a year.
    $this->get('/login')->assertHeaderMissing('Strict-Transport-Security');
});

it('sends HSTS over https', function () {
    $this->get('https://localhost/login')
        ->assertHeader('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
});
