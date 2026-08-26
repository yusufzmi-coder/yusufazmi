<?php

declare(strict_types=1);

use App\Models\Integration;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Exception\TransportException;

beforeEach(function () {
    Config::set('mail.default', 'sendscape');
    Integration::create([
        'key' => 'sendscape',
        'enabled' => true,
        'config' => ['api_key' => 'ss_test_key', 'from_address' => 'admin@jadual.test'],
    ]);
});

it('sends mail through the Sendscape API', function () {
    Http::fake(['api.sendscape.ai/*' => Http::response(['id' => 'msg_1'], 202)]);

    Mail::raw('Jadual kelas anak anda telah dikemas kini.', function ($message) {
        $message->to('ibubapa@example.com')
            ->from('admin@jadual.test')
            ->subject('Kemas kini jadual');
    });

    Http::assertSent(function ($request) {
        return $request->url() === 'https://api.sendscape.ai/v1/emails'
            && $request->hasHeader('Authorization', 'Bearer ss_test_key')
            && $request['to'] === ['ibubapa@example.com']
            && $request['subject'] === 'Kemas kini jadual';
    });
});

it('surfaces a rejection from Sendscape instead of silently dropping the mail', function () {
    Http::fake(['api.sendscape.ai/*' => Http::response(['error' => 'domain not verified'], 422)]);

    expect(fn () => Mail::raw('ujian', fn ($m) => $m->to('a@b.my')->from('admin@jadual.test')->subject('x')))
        ->toThrow(TransportException::class);
});

it('refuses to send when no API key is configured', function () {
    Integration::where('key', 'sendscape')->update(['config' => encrypt(json_encode([]))]);
    Config::set('services.sendscape.key', null);

    expect(fn () => Mail::raw('ujian', fn ($m) => $m->to('a@b.my')->from('admin@jadual.test')->subject('x')))
        ->toThrow(TransportException::class);
});
