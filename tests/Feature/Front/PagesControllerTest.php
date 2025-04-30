<?php

namespace Tests\Feature\Front;

use Illuminate\Http\Response;
use Tests\TestCase;

class PagesControllerTest extends TestCase
{
    /**
     * Проверяет загрузку главной страницы.
     * @return void
     */
    public function testIndex(): void
    {
        $response = $this->get(route('front.index'));
        $response->assertStatus(Response::HTTP_OK);
    }
}