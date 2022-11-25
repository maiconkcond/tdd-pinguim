<?php

namespace Tests\Feature;

use App\Mail\Invitation;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class InvitationTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function itShouldBeAbleToInviteSemeoneToThePlatform()
    {
        Mail::fake();

        /** @var User $user */
        $user = User::factory()->create();

        $this->actingAs($user);

        $this->post('invites', ['email' => 'novo@email.com']);

        Mail::assertSent(Invitation::class, function ($mail) {
            return $mail->hasTo('novo@email.com');
        });

        $this->assertDatabaseHas('invites', ['email' => 'novo@email.com']);
    }
}
