<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Coupon;
use App\Models\Message;
use App\Models\Review;
use App\Models\Tour;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class CoreWorkflowSafetyTest extends TestCase
{
    use RefreshDatabase;

    public function test_booking_capacity_is_calculated_for_the_same_departure_date(): void
    {
        [$host, $tour] = $this->createTour(['capacity' => 5]);
        $firstUser = $this->tourist();
        $secondUser = $this->tourist();
        $date = now()->addMonth()->toDateString();

        Booking::create($this->bookingData($firstUser, $tour, [
            'start_date' => $date,
            'number_of_people' => 4,
            'total_price' => 4000000,
        ]));

        $this->actingAs($secondUser)
            ->post(route('booking.store', $tour), [
                'phone' => '0912345678',
                'start_date' => $date,
                'number_of_people' => 2,
                'payment_method' => 'bank_transfer',
            ])
            ->assertSessionHasErrors('number_of_people');

        $this->assertSame(1, Booking::where('tour_id', $tour->id)->count());
    }

    public function test_payment_test_mode_phone_and_coupon_are_applied_when_booking(): void
    {
        config(['payment.test_mode' => true]);
        [, $tour] = $this->createTour(['price' => 1000000]);
        $user = $this->tourist();
        Coupon::create([
            'code' => 'GIAM20',
            'discount_type' => 'percent',
            'discount_amount' => 20,
            'usage_limit' => 10,
            'used_count' => 0,
            'is_active' => true,
        ]);

        $this->actingAs($user)
            ->post(route('booking.store', $tour), [
                'phone' => '0987654321',
                'start_date' => now()->addMonth()->toDateString(),
                'number_of_people' => 2,
                'payment_method' => 'bank_transfer',
                'coupon_code' => 'giam20',
            ])
            ->assertRedirect();

        $booking = Booking::firstOrFail();
        $this->assertSame('paid', $booking->payment_status);
        $this->assertSame('1600000.00', $booking->total_price);
        $this->assertSame('0987654321', $user->refresh()->phone);
        $this->assertSame(1, Coupon::firstOrFail()->used_count);
    }

    public function test_bank_transfer_stays_pending_when_payment_test_mode_is_disabled(): void
    {
        config(['payment.test_mode' => false]);
        [, $tour] = $this->createTour();
        $user = $this->tourist();

        $this->actingAs($user)->post(route('booking.store', $tour), [
            'phone' => '0912345678',
            'start_date' => now()->addMonth()->toDateString(),
            'number_of_people' => 1,
            'payment_method' => 'bank_transfer',
        ]);

        $this->assertSame('pending', Booking::firstOrFail()->payment_status);
    }

    public function test_vnpay_callback_checks_amount_and_is_idempotent(): void
    {
        config(['vnpay.vnp_TmnCode' => 'TESTCODE', 'vnpay.vnp_HashSecret' => 'secret-key']);
        [, $tour] = $this->createTour();
        $user = $this->tourist();
        $booking = Booking::create($this->bookingData($user, $tour));

        $invalidAmount = $this->signedVnpayParams([
            'vnp_TmnCode' => 'TESTCODE',
            'vnp_TxnRef' => $booking->id.'_test',
            'vnp_Amount' => 999,
            'vnp_ResponseCode' => '00',
        ]);
        $this->get(route('vnpay.return', $invalidAmount))->assertSessionHas('error');
        $this->assertSame('pending', $booking->refresh()->payment_status);

        $valid = $this->signedVnpayParams([
            'vnp_TmnCode' => 'TESTCODE',
            'vnp_TxnRef' => $booking->id.'_test',
            'vnp_Amount' => 100000000,
            'vnp_ResponseCode' => '00',
        ]);
        $this->actingAs($user)->get(route('vnpay.return', $valid))->assertRedirect(route('booking.success', $booking));
        $this->actingAs($user)->get(route('vnpay.return', $valid))->assertRedirect(route('booking.success', $booking));
        $this->assertSame('paid', $booking->refresh()->payment_status);
    }

    public function test_rejected_tour_returns_to_pending_after_host_changes_content(): void
    {
        [$host, $tour] = $this->createTour(['status' => 'rejected', 'is_active' => false]);

        $this->actingAs($host)
            ->put(route('host.tours.update', $tour), $this->tourFormData(['name' => 'Tour đã chỉnh sửa']))
            ->assertRedirect(route('host.tours.index'));

        $this->assertSame('pending', $tour->refresh()->status);
        $this->assertFalse($tour->is_active);
    }

    public function test_tour_with_booking_and_user_with_business_data_cannot_be_deleted(): void
    {
        [$host, $tour] = $this->createTour();
        $tourist = $this->tourist();
        Booking::create($this->bookingData($tourist, $tour));

        $this->actingAs($host)
            ->delete(route('host.tours.destroy', $tour))
            ->assertSessionHas('error');
        $this->assertDatabaseHas('tours', ['id' => $tour->id]);

        $admin = User::factory()->create();
        $admin->assignRole(Role::findOrCreate('admin'));
        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $tourist))
            ->assertSessionHas('error');
        $this->assertDatabaseHas('users', ['id' => $tourist->id]);
    }

    public function test_deleting_unused_tour_removes_image_record_and_file(): void
    {
        Storage::fake('public');
        [$host, $tour] = $this->createTour();
        Storage::disk('public')->put('tours/remove.jpg', 'image');
        $image = $tour->images()->create(['path' => 'tours/remove.jpg']);

        $this->actingAs($host)->delete(route('host.tours.destroy', $tour))->assertSessionHas('success');

        $this->assertDatabaseMissing('tours', ['id' => $tour->id]);
        $this->assertDatabaseMissing('images', ['id' => $image->id]);
        Storage::disk('public')->assertMissing('tours/remove.jpg');
    }

    public function test_only_a_guest_with_completed_booking_can_review_a_tour(): void
    {
        [, $tour] = $this->createTour();
        $user = $this->tourist();

        $this->actingAs($user)
            ->post(route('tours.reviews.store', $tour), ['rating' => 5, 'comment' => 'Rất tốt'])
            ->assertForbidden();

        Booking::create($this->bookingData($user, $tour, ['status' => 'completed', 'payment_status' => 'paid']));
        $this->actingAs($user)
            ->post(route('tours.reviews.store', $tour), ['rating' => 5, 'comment' => 'Rất tốt'])
            ->assertSessionHas('success');

        $this->assertSame(1, Review::count());
        $this->assertSame(5, Review::firstOrFail()->rating);
    }

    public function test_tourist_can_message_host_and_host_can_reply(): void
    {
        [$host] = $this->createTour();
        $tourist = $this->tourist();

        $this->actingAs($tourist)
            ->post(route('messages.store', $host), ['content' => 'Xin chào Host'])
            ->assertRedirect(route('messages.show', $host));

        $this->actingAs($host)
            ->post(route('messages.store', $tourist), ['content' => 'Chào bạn'])
            ->assertRedirect(route('host.inbox', ['contact' => $tourist->id]));

        $this->assertSame(2, Message::count());
    }

    public function test_location_filter_and_removed_resource_show_routes(): void
    {
        [, $matching] = $this->createTour(['name' => 'Tour Sa Pa', 'slug' => 'tour-sa-pa', 'location' => 'Sa Pa']);
        [, $other] = $this->createTour(['name' => 'Tour Hà Giang', 'slug' => 'tour-ha-giang', 'location' => 'Hà Giang']);

        $this->get(route('tours.index', ['location' => 'Sa Pa']))
            ->assertOk()
            ->assertSee($matching->name)
            ->assertDontSee(route('tours.show', $other->slug));

        $host = $matching->host;
        $this->actingAs($host)->get('/host/tours/'.$matching->id)->assertStatus(405);
    }

    private function createTour(array $overrides = []): array
    {
        $host = User::factory()->create();
        $host->assignRole(Role::findOrCreate('host'));

        $tour = Tour::create(array_merge([
            'host_id' => $host->id,
            'name' => 'Tour kiểm thử',
            'slug' => 'tour-'.uniqid(),
            'location' => 'Sa Pa',
            'description' => 'Nội dung tour kiểm thử.',
            'price' => 1000000,
            'duration_days' => 2,
            'duration_nights' => 1,
            'capacity' => 10,
            'itinerary' => [['title' => 'Ngày 1', 'description' => 'Khởi hành']],
            'status' => 'approved',
            'is_active' => true,
        ], $overrides));

        return [$host, $tour];
    }

    private function tourist(): User
    {
        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate('tourist'));

        return $user;
    }

    private function bookingData(User $user, Tour $tour, array $overrides = []): array
    {
        return array_merge([
            'user_id' => $user->id,
            'tour_id' => $tour->id,
            'start_date' => now()->addMonth()->toDateString(),
            'number_of_people' => 1,
            'total_price' => 1000000,
            'status' => 'pending',
            'payment_status' => 'pending',
            'payment_method' => 'bank_transfer',
        ], $overrides);
    }

    private function tourFormData(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Tour kiểm thử',
            'location' => 'Sa Pa',
            'description' => 'Nội dung tour kiểm thử.',
            'price' => 1000000,
            'duration_days' => 2,
            'duration_nights' => 1,
            'capacity' => 10,
            'itinerary' => [['title' => 'Ngày 1', 'description' => 'Khởi hành']],
            'is_active' => true,
        ], $overrides);
    }

    private function signedVnpayParams(array $params): array
    {
        ksort($params);
        $hashData = collect($params)
            ->map(fn ($value, $key) => urlencode($key).'='.urlencode($value))
            ->implode('&');
        $params['vnp_SecureHash'] = hash_hmac('sha512', $hashData, config('vnpay.vnp_HashSecret'));

        return $params;
    }
}
