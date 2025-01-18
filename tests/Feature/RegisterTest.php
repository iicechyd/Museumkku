<?php

namespace Tests\Feature\Auth;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class RegisterTest extends TestCase
{
    use WithFaker, RefreshDatabase;
    public function test_successful_Register_WithFaker()
    {
        $email = $this->faker->unique()->safeEmail;

        $response = $this->post('/register', [
            'name' => $this->faker->name,
            'email' => $email,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);

        $response->assertRedirect('/register');
        $response->assertSessionHas('success', 'สมัครสมาชิกเสร็จสิ้น กรุณารอการตรวจสอบและอนุมัติจากผู้ดูแลระบบ');

        $this->assertDatabaseHas('users', [
            'email' => $email,
        ]);
    }
    public function testUserCannotRegisterWithEmptyFields()
    {
        $response = $this->post('/register', [
            'name' => '',
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        $response->assertSessionHasErrors(['name']);

        $response = $this->post('/register', [
            'name' => $this->faker->name,
            'email' => '',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ]);
        $response->assertSessionHasErrors(['email']);

        $response = $this->post('/register', [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => '',
            'password_confirmation' => 'password123',
        ]);
        $response->assertSessionHasErrors(['password']);

        $response = $this->post('/register', [
            'name' => $this->faker->name,
            'email' => $this->faker->unique()->safeEmail,
            'password' => 'password123',
            'password_confirmation' => '',
        ]);
        $response->assertSessionHasErrors(['password']);
    }
}
