<?php

namespace Tests\Feature\Front;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Tests\TestCase;

class RegisterControllerTest extends TestCase
{
    use WithFaker;

    /**
     * Проверяет загрузку страницы регистрации.
     * @return void
     */
    public function testIndex(): void
    {
        $response = $this->get(route('auth.register'));
        $response->assertStatus(Response::HTTP_OK);
    }

    /**
     * Проверяет создание страницы пользователя.
     * @return void
     */
    public function testRegister(): void
    {
        $password = Str::random();
        $mobilePhone = '+7 (999) ' . random_int(100, 999) . '-' . random_int(10, 99) . '-' . random_int(10, 99);
        $mobilePhoneCleared = str_replace(['(', ')', '-', ' '], '', $mobilePhone);

        $data = [
            'last_name' => Str::random(),
            'first_name' => Str::random(),
            'middlename' => Str::random(),
            'email' => $this->faker->email,
            'mphone' => $mobilePhone,
            'birthdate' => $this->faker->date('d.m.Y'),
            'password' => $password,
            'password_confirmation' => $password,
            'terms_agree' => '1',
            'additional_terms_agree' => '1',
        ];

        $response = $this->post(route('auth.register.store'), $data);
        $response->assertStatus(Response::HTTP_FOUND);

        /** @var User $user */
        $user = User::query()->where('email', $data['email'])->first();

        self::assertNotNull($user);

        $fieldsToCheck = [
            'last_name',
            'first_name',
            'middlename',
            'email',
        ];

        foreach ($fieldsToCheck as $field) {
            self::assertEquals($data[$field], $user->$field);
        }

        self::assertEquals(Carbon::parse($data['birthdate'])->toDateString(), $user->birthdate);
        self::assertEquals($mobilePhoneCleared, $user->mphone);
    }
}