<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use PHPUnit\Framework\Test;
use Tests\TestCase;

/**
 * @internal
 *
 * @coversNothing
 */
class RegisterTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @test
     */
    public function itShouldBeAbleToRegisterAsNewUser()
    {
        $responseReturned = $this->post('register', [
            'name' => 'João Mineiro',
            'email' => 'joao@test.com',
            'email_confirmation' => 'joao@test.com',
            'password' => 'issoeumasenhA',
        ]);

        $responseReturned->assertRedirect('dashboard');

        $this->assertDatabaseHas('users', [
            'name' => 'João Mineiro',
            'email' => 'joao@test.com',
        ]);

        /** @var User $user */
        $user = User::whereEmail('joao@test.com')->firstOrFail();

        $this->assertTrue(
            Hash::check('issoeumasenhA', $user->password),
            ''
        );
    }

    /** @test */
    public function nameShouldBeRequired()
    {
        $this->post('register', [])
            ->assertSessionHasErrors([
                'name' => __('validation.required', ['attribute' => 'name']),
            ])
        ;
    }

    /** @test */
    public function nameShouldHaveAMaxOf255Characters()
    {
        $this->post('register', [
            'name' => str_repeat('a', 256),
        ])->assertSessionHasErrors([
            'name' => __('validation.max.string', ['attribute' => 'name', 'max' => 255]),
        ]);
    }

    /** @test */
    public function emailShouldBeRequired()
    {
        $this->post('register', [])
            ->assertSessionHasErrors([
                'email' => __('validation.required', ['attribute' => 'email']),
            ])
        ;
    }

    /** @test */
    public function emailShouldBeValidEmail()
    {
        $this->post('register', [
            'email' => 'dasdqwewqd-ewqeqe',
        ])->assertSessionHasErrors([
            'email' => __('validation.email', ['attribute' => 'email']),
        ]);
    }

    /** @test */
    public function emailShouldBeUnique()
    {
        User::factory()->create([
            'email' => 'joao@test.com',
        ]);

        $this->post('register', [
            'email' => 'joao@test.com',
        ])->assertSessionHasErrors([
            'email' => __('validation.unique', ['attribute' => 'email']),
        ]);
    }

    /** @test */
    public function emailShouldBeConfirmed()
    {
        $this->post('register', [
            'email' => 'joao@test.com',
            'email_confirmation' => '',
        ])->assertSessionHasErrors([
            'email' => __('validation.confirmed', ['attribute' => 'email']),
        ]);
    }

    /** @test */
    public function passwordShouldBeRequired()
    {
        $this->post('register', [])
            ->assertSessionHasErrors([
                'password' => __('validation.required', ['attribute' => 'password']),
            ])
        ;
    }

    /** @test */
    public function passwordShouldHaveAtLeast1Uppercase()
    {
        $this->post('register', [
            'password' => 'password-without-repeat',
        ])->assertSessionHasErrors([
            'password' => 'The password must contain at least one uppercase and one lowercase letter.',
        ]);
    }
}
