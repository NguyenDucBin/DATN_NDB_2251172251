<?php

namespace Tests\Feature;

use App\Models\Tour;
use App\Models\User;
use App\Models\Image;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class HostTourImageManagementTest extends TestCase
{
    use RefreshDatabase;

    public function test_host_can_upload_images_when_creating_a_tour(): void
    {
        Storage::fake('public');
        $host = $this->createHost();

        $this->actingAs($host)
            ->post(route('host.tours.store'), $this->validTourData([
                'images' => [UploadedFile::fake()->create('new-tour.jpg', 500, 'image/jpeg')],
            ]))
            ->assertRedirect(route('host.tours.index'))
            ->assertSessionHas('success');

        $tour = Tour::query()->where('host_id', $host->id)->firstOrFail();
        $image = $tour->images()->firstOrFail();

        $this->assertSame('pending', $tour->status);
        $this->assertFalse((bool) $tour->is_active);
        $this->assertStringStartsWith('tours/', $image->path);
        Storage::disk('public')->assertExists($image->path);
    }

    public function test_host_can_upload_images_when_updating_a_tour(): void
    {
        Storage::fake('public');
        [$host, $tour] = $this->createHostAndTour();

        $this->actingAs($host)
            ->put(route('host.tours.update', $tour), $this->validTourData([
                'images' => [UploadedFile::fake()->create('updated-tour.webp', 500, 'image/webp')],
            ]))
            ->assertRedirect(route('host.tours.index'))
            ->assertSessionHas('success');

        $image = $tour->images()->firstOrFail();

        $this->assertSame('pending', $tour->refresh()->status);
        $this->assertFalse((bool) $tour->is_active);
        $this->assertStringStartsWith('tours/', $image->path);
        Storage::disk('public')->assertExists($image->path);
    }

    public function test_host_can_remove_an_existing_tour_image_during_update(): void
    {
        Storage::fake('public');

        [$host, $tour] = $this->createHostAndTour();
        Storage::disk('public')->put('tours/old-image.jpg', 'image-content');
        $image = $tour->images()->create(['path' => 'tours/old-image.jpg']);

        $this->actingAs($host)
            ->get(route('host.tours.edit', $tour))
            ->assertOk()
            ->assertSee($image->url(), false)
            ->assertSee("markExistingImageForDeletion({$image->id})", false)
            ->assertSee('deleted_images[]', false);

        $this->actingAs($host)
            ->put(route('host.tours.update', $tour), $this->validTourData([
                'deleted_images' => [$image->id],
            ]))
            ->assertRedirect(route('host.tours.index'))
            ->assertSessionHas('success');

        $this->assertDatabaseMissing('images', ['id' => $image->id]);
        Storage::disk('public')->assertMissing('tours/old-image.jpg');
        $this->assertSame('pending', $tour->refresh()->status);
        $this->assertFalse((bool) $tour->is_active);
    }

    public function test_host_cannot_remove_an_image_from_another_tour(): void
    {
        [$host, $tour] = $this->createHostAndTour();
        [, $otherTour] = $this->createHostAndTour('other-tour');
        $otherImage = $otherTour->images()->create(['path' => 'tours/other-image.jpg']);

        $this->actingAs($host)
            ->put(route('host.tours.update', $tour), $this->validTourData([
                'deleted_images' => [$otherImage->id],
            ]))
            ->assertRedirect(route('host.tours.index'));

        $this->assertDatabaseHas('images', ['id' => $otherImage->id]);
        $this->assertSame('approved', $tour->refresh()->status);
    }

    public function test_missing_tour_image_uses_local_fallback_url(): void
    {
        Storage::fake('public');

        $image = new Image(['path' => 'tours/missing-image.jpg']);

        $this->assertSame(asset('images/static/destination-sa-pa.jpg'), $image->url());
    }

    public function test_existing_public_tour_image_keeps_storage_url(): void
    {
        Storage::fake('public');
        Storage::disk('public')->put('tours/existing-image.jpg', 'image-content');

        $image = new Image(['path' => 'tours/existing-image.jpg']);

        $this->assertSame(asset('storage/tours/existing-image.jpg'), $image->url());
    }

    private function createHostAndTour(string $slug = 'host-tour'): array
    {
        $host = $this->createHost();

        $tour = Tour::create([
            'host_id' => $host->id,
            'name' => 'Tour kiểm thử hình ảnh',
            'slug' => $slug,
            'location' => 'Sa Pa',
            'description' => 'Nội dung tour kiểm thử.',
            'price' => 1200000,
            'duration_days' => 2,
            'duration_nights' => 1,
            'capacity' => 10,
            'itinerary' => [['title' => 'Ngày 1', 'description' => 'Khởi hành']],
            'is_active' => true,
            'status' => 'approved',
        ]);

        return [$host, $tour];
    }

    private function createHost(): User
    {
        $host = User::factory()->create();
        $host->assignRole(Role::findOrCreate('host'));

        return $host;
    }

    private function validTourData(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Tour kiểm thử hình ảnh',
            'location' => 'Sa Pa',
            'description' => 'Nội dung tour kiểm thử.',
            'price' => 1200000,
            'duration_days' => 2,
            'duration_nights' => 1,
            'capacity' => 10,
            'itinerary' => [['title' => 'Ngày 1', 'description' => 'Khởi hành']],
            'is_active' => true,
        ], $overrides);
    }
}
