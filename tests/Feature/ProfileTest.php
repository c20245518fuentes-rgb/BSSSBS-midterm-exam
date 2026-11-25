<?php

namespace Tests\Feature;

use App\Models\Client;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_customer_can_view_and_update_profile_information(): void
    {
        $user = User::factory()->create([
            'name' => 'Old Name',
            'email' => 'old@example.com',
        ]);
        $client = Client::factory()->for($user)->create([
            'name' => 'Old Name',
            'phone' => '09170000001',
        ]);

        $this->actingAs($user);

        $this->get(route('profile.show'))
            ->assertOk()
            ->assertInertia(fn ($page) => $page
                ->component('Profile/Show')
                ->where('user.name', 'Old Name')
                ->where('user.email', 'old@example.com')
                ->where('user.client.phone', '09170000001')
            );

        $this->put(route('profile.update'), [
            'name' => ' <b>New Name</b> ',
            'email' => ' NEW@example.com ',
            'phone' => '+63 (917) 000-0002',
        ])->assertRedirect(route('profile.show'))
            ->assertSessionHas('success');

        $user->refresh();
        $client->refresh();

        $this->assertSame('New Name', $user->name);
        $this->assertSame('new@example.com', $user->email);
        $this->assertSame('New Name', $client->name);
        $this->assertSame('+639170000002', $client->phone);
    }

    public function test_customer_can_change_password_with_current_password(): void
    {
        $user = User::factory()->create();
        Client::factory()->for($user)->create();

        $this->actingAs($user);

        $this->from(route('profile.show'))
            ->put(route('profile.password.update'), [
                'current_password' => 'wrong-password',
                'password' => 'new-secure-password',
                'password_confirmation' => 'new-secure-password',
            ])->assertRedirect(route('profile.show'))
            ->assertSessionHasErrors('current_password');

        $this->put(route('profile.password.update'), [
            'current_password' => 'password',
            'password' => 'new-secure-password',
            'password_confirmation' => 'new-secure-password',
        ])->assertRedirect(route('profile.show'))
            ->assertSessionHas('success');

        $this->assertTrue(Hash::check('new-secure-password', $user->fresh()->password));
    }

    public function test_studio_users_cannot_access_customer_profile_routes(): void
    {
        $user = User::factory()->studio()->create();
        Client::factory()->for($user)->create();

        $this->actingAs($user);

        $this->get(route('profile.show'))->assertForbidden();
        $this->put(route('profile.update'), [
            'name' => 'Studio User',
            'email' => 'studio@example.com',
            'phone' => '09171234567',
        ])->assertForbidden();
        $this->put(route('profile.password.update'), [
            'current_password' => 'password',
            'password' => 'new-secure-password',
            'password_confirmation' => 'new-secure-password',
        ])->assertForbidden();
    }
}
