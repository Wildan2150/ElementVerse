<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use App\Models\PasswordResetRequest;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Fortify\Features;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->skipUnlessFortifyHas(Features::resetPasswords());
    }

    public function test_reset_password_link_screen_can_be_rendered()
    {
        $response = $this->get(route('password.request'));

        $response->assertOk();
    }

    public function test_reset_password_link_can_be_requested()
    {
        $user = User::factory()->create();

        $response = $this->post(route('password.email'), [
            'email' => $user->email,
        ]);

        $this->assertDatabaseHas('password_reset_requests', [
            'user_id' => $user->id,
            'status' => 'pending',
        ]);

        $resetRequest = PasswordResetRequest::where('user_id', $user->id)->first();
        $response->assertRedirect(route('password-reset-request.waiting', ['token' => $resetRequest->token]));
    }

    public function test_reset_password_screen_can_be_rendered()
    {
        $user = User::factory()->create();

        $resetRequest = PasswordResetRequest::create([
            'user_id' => $user->id,
            'token' => 'test-token-123',
            'status' => 'approved',
        ]);

        $response = $this->get(route('password-reset-request.waiting', ['token' => $resetRequest->token]));

        $response->assertOk();
    }

    public function test_password_can_be_reset_with_valid_token()
    {
        $user = User::factory()->create();

        $resetRequest = PasswordResetRequest::create([
            'user_id' => $user->id,
            'token' => 'test-token-123',
            'status' => 'approved',
        ]);

        $response = $this->post(route('password-reset-request.reset', ['token' => $resetRequest->token]), [
            'password' => 'new-password-123',
            'password_confirmation' => 'new-password-123',
        ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect(route('login'));

        $this->assertTrue(\Illuminate\Support\Facades\Hash::check('new-password-123', $user->fresh()->password));
    }

    public function test_password_cannot_be_reset_with_invalid_token(): void
    {
        $user = User::factory()->create();

        $response = $this->post(route('password-reset-request.reset', ['token' => 'invalid-token']), [
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
        ]);

        $response->assertNotFound();
    }
}
