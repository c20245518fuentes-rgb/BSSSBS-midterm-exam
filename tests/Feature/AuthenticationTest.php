<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_can_register_login_and_logout(): void
    {
        $this->get(route('login'))->assertOk();
        $this->get(route('register'))->assertOk();

        $this->post(route('register.store'), [
            'name' => '<b>Salon Manager</b>',
            'email' => ' Manager@Example.COM ',
            'password' => 'long-password-123',
            'password_confirmation' => 'long-password-123',
        ])->assertRedirect(route('appointments.index'));

        $user = User::query()->where('email', 'manager@example.com')->firstOrFail();
        $this->assertSame('Salon Manager', $user->name);
        $this->assertSame(User::ROLE_STUDIO, $user->role);
        $this->assertDatabaseCount('clients', 0);
        $this->assertAuthenticatedAs($user);
        $this->assertTrue(Hash::check('long-password-123', $user->password));

        $this->post(route('logout'))->assertRedirect(route('login'));
        $this->assertGuest();

        $this->post(route('register.store'), [
            'name' => 'Jamie Customer',
            'email' => 'jamie@example.com',
            'phone' => '+63 (917) 123-4567',
            'password' => 'long-password-456',
            'password_confirmation' => 'long-password-456',
        ])->assertRedirect(route('appointments.index'));

        $customer = User::query()->where('email', 'jamie@example.com')->firstOrFail();
        $this->assertSame(User::ROLE_CUSTOMER, $customer->role);
        $this->assertDatabaseHas('clients', [
            'user_id' => $customer->id,
            'name' => 'Jamie Customer',
            'phone' => '+639171234567',
        ]);

        $this->post(route('logout'))->assertRedirect(route('login'));

        $this->post(route('login.store'), [
            'email' => ' MANAGER@example.com ',
            'password' => 'long-password-123',
            'remember' => '1',
        ])->assertRedirect(route('appointments.index'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_public_registration_creates_customer_accounts_after_initial_setup(): void
    {
        User::factory()->create();

        $this->get(route('register'))->assertOk();

        $this->post(route('register.store'), [
            'name' => 'Another User',
            'email' => 'another@example.com',
            'phone' => '09171234567',
            'password' => 'long-password-123',
            'password_confirmation' => 'long-password-123',
        ])->assertRedirect(route('appointments.index'));

        $user = User::query()->where('email', 'another@example.com')->firstOrFail();
        $this->assertSame(User::ROLE_CUSTOMER, $user->role);
        $this->assertDatabaseHas('clients', [
            'user_id' => $user->id,
            'phone' => '09171234567',
        ]);
    }

    public function test_login_rejects_invalid_password_and_throttles_repeated_attempts(): void
    {
        $user = User::factory()->create();

        foreach (range(1, 5) as $attempt) {
            $this->from(route('login'))
                ->post(route('login.store'), [
                    'email' => $user->email,
                    'password' => 'incorrect-password',
                ])
                ->assertRedirect(route('login'))
                ->assertSessionHasErrors('email');
        }

        $this->post(route('login.store'), [
            'email' => $user->email,
            'password' => 'password',
        ])->assertStatus(429);
    }

    public function test_password_reset_link_can_be_requested_and_used_once(): void
    {
        Notification::fake();
        $user = User::factory()->create();

        $this->get(route('password.request'))->assertOk();
        $this->post(route('password.email'), ['email' => $user->email])
            ->assertRedirect(route('password.request'))
            ->assertSessionHas('status');

        $token = null;
        Notification::assertSentTo($user, ResetPassword::class, function (ResetPassword $notification) use (&$token): bool {
            $token = $notification->token;

            return true;
        });

        $this->get(route('password.reset', ['token' => $token, 'email' => $user->email]))
            ->assertOk();

        $this->post(route('password.update'), [
            'token' => $token,
            'email' => $user->email,
            'password' => 'new-secure-password',
            'password_confirmation' => 'new-secure-password',
        ])->assertRedirect(route('login'))
            ->assertSessionHas('status');

        $this->assertTrue(Hash::check('new-secure-password', $user->fresh()->password));
        $this->assertGuest();

        $this->from(route('password.reset', ['token' => 'expired-token', 'email' => $user->email]))
            ->post(route('password.update'), [
                'token' => 'expired-token',
                'email' => $user->email,
                'password' => 'another-secure-password',
                'password_confirmation' => 'another-secure-password',
            ])
            ->assertRedirect(route('password.reset', ['token' => 'expired-token', 'email' => $user->email]))
            ->assertSessionHasErrors('email');
    }

    public function test_password_reset_request_does_not_disclose_unknown_emails(): void
    {
        Notification::fake();

        $this->from(route('password.request'))
            ->post(route('password.email'), ['email' => 'unknown@example.com'])
            ->assertRedirect(route('password.request'))
            ->assertSessionHas('status', 'If an account exists for that email, a password reset link has been sent.');

        Notification::assertNothingSent();
    }

    public function test_salon_routes_require_authentication(): void
    {
        $this->get(route('appointments.index'))->assertRedirect(route('login'));
        $this->get(route('services.index'))->assertRedirect(route('login'));
        $this->post(route('logout'))->assertRedirect(route('login'));
    }
}
