<?php

namespace Tests\Browser;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class PreviewTest extends DuskTestCase
{
    /** @test */
    public function click_buttons()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('http://127.0.0.1:8000/preview')
                ->waitForText('จองเข้าชมพิพิธภัณฑ์')
                ->click('a[href="http://127.0.0.1:8000/preview_general"]')
                ->assertPathIs('/preview_general');

            $browser->visit('http://127.0.0.1:8000/preview')
                ->waitForText('จองเข้าร่วมกิจกรรม')
                ->click('a[href="http://127.0.0.1:8000/preview_activity"]')
                ->assertPathIs('/preview_activity');

            $browser->visit('http://127.0.0.1:8000/preview')
                ->waitForText('ตรวจสอบสถานะการจอง')
                ->click('a[href="http://127.0.0.1:8000/checkBookingStatus"]')
                ->assertPathIs('/checkBookingStatus');
        });
    }
}
