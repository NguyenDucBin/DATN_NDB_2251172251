<?php

namespace Tests\Feature;

use App\Models\Booking;
use App\Models\Refund;
use App\Models\Tour;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class RefundWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_paid_booking_can_be_refunded_with_legacy_refund_fields(): void
    {
        [$user, $booking] = $this->createPaidBooking();

        $this->actingAs($user)
            ->post(route('profile.refunds.store', $booking), ['reason' => 'Không thể tham gia'])
            ->assertSessionHas('success');

        $refund = Refund::firstOrFail();
        $this->assertSame('pending', $refund->status);
        $this->assertSame('pending', $booking->refresh()->status);
        $this->assertFalse($booking->load('refunds')->canBeConfirmedByHost());

        $admin = User::factory()->create();
        $admin->assignRole(Role::findOrCreate('admin'));

        $this->actingAs($admin)
            ->post(route('admin.refunds.process', $refund), ['status' => 'processed'])
            ->assertSessionHas('success');

        $this->assertSame('processed', $refund->refresh()->status);
        $this->assertSame('refunded', $booking->refresh()->status);
        $this->assertSame('refunded', $booking->payment_status);
        $this->assertFalse(Schema::hasColumn('refunds', 'admin_note'));
        $this->assertFalse(Schema::hasColumn('refunds', 'processed_at'));
    }

    public function test_rejected_refund_keeps_the_booking_status(): void
    {
        [$user, $booking] = $this->createPaidBooking('confirmed');

        $this->actingAs($user)
            ->post(route('profile.refunds.store', $booking), ['reason' => 'Đổi kế hoạch']);

        $refund = Refund::firstOrFail();
        $admin = User::factory()->create();
        $admin->assignRole(Role::findOrCreate('admin'));

        $this->actingAs($admin)
            ->post(route('admin.refunds.process', $refund), ['status' => 'rejected'])
            ->assertSessionHas('success');

        $this->assertSame('rejected', $refund->refresh()->status);
        $this->assertSame('confirmed', $booking->refresh()->status);
        $this->assertSame('paid', $booking->payment_status);
    }

    private function createPaidBooking(string $status = 'pending'): array
    {
        $host = User::factory()->create();
        $host->assignRole(Role::findOrCreate('host'));

        $user = User::factory()->create();
        $user->assignRole(Role::findOrCreate('tourist'));

        $tour = Tour::create([
            'host_id' => $host->id,
            'name' => 'Tour kiểm thử hoàn tiền',
            'slug' => 'tour-kiem-thu-hoan-tien',
            'description' => 'Tour dùng cho kiểm thử.',
            'price' => 1000000,
            'capacity' => 10,
            'itinerary' => [],
            'status' => 'approved',
            'is_active' => true,
        ]);

        $booking = Booking::create([
            'user_id' => $user->id,
            'tour_id' => $tour->id,
            'start_date' => now()->addWeek()->toDateString(),
            'number_of_people' => 1,
            'total_price' => 1000000,
            'status' => $status,
            'payment_status' => 'paid',
            'payment_method' => 'bank_transfer',
        ]);

        return [$user, $booking];
    }
}
