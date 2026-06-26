<?php

namespace App\Http\Controllers\Host;

use App\Http\Controllers\Controller;
use App\Http\Requests\SaveTourRequest;
use App\Models\Tour;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class HostTourController extends Controller
{
    public function index()
    {
        $tours = Tour::where('host_id', auth()->id())
            ->withCount('bookings')
            ->latest()
            ->paginate(10);

        return view('host.tours.index', compact('tours'));
    }

    public function create()
    {
        return view('host.tours.create');
    }

    public function store(SaveTourRequest $request)
    {
        $validated = $request->validated();

        $tourData = $validated;
        unset($tourData['images']);

        $tourData['host_id'] = auth()->id();
        $tourData['slug'] = Str::slug($tourData['name']).'-'.uniqid();
        $tourData['status'] = 'pending'; // Yêu cầu admin duyệt
        $tourData['is_active'] = false;

        $tour = Tour::create($tourData);

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('tours', 'public');
                $tour->images()->create(['path' => $path]);
            }
        }

        return redirect()->route('host.tours.index')->with('success', 'Đã tạo tour mới, đang chờ admin phê duyệt.');
    }

    public function edit(Tour $tour)
    {
        $this->authorize('update', $tour);

        return view('host.tours.edit', compact('tour'));
    }

    public function update(SaveTourRequest $request, Tour $tour)
    {
        $this->authorize('update', $tour);
        $validated = $request->validated();

        $tourData = $validated;
        unset($tourData['images'], $tourData['deleted_images']);

        $imagesToDelete = $tour->images()
            ->whereIn('id', $validated['deleted_images'] ?? [])
            ->get();

        $reviewedFields = [
            'name',
            'location',
            'duration_days',
            'duration_nights',
            'description',
            'price',
            'capacity',
            'itinerary',
            'highlights',
            'included',
            'policies',
        ];

        $tour->fill($tourData);

        $contentChanged = $tour->isDirty($reviewedFields)
            || $request->hasFile('images')
            || $imagesToDelete->isNotEmpty();
        $requiresReview = in_array($tour->status, ['approved', 'rejected'], true) && $contentChanged;

        if ($requiresReview) {
            $tour->status = 'pending';
            $tour->is_active = false;
        } elseif ($tour->status !== 'approved') {
            $tour->is_active = false;
        }

        $tour->save();

        foreach ($imagesToDelete as $image) {
            $path = ltrim($image->path, '/');

            if (! filter_var($path, FILTER_VALIDATE_URL)) {
                if (str_starts_with($path, 'storage/')) {
                    $path = substr($path, strlen('storage/'));
                }

                Storage::disk('public')->delete($path);
            }

            $image->delete();
        }

        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $file) {
                $path = $file->store('tours', 'public');
                $tour->images()->create(['path' => $path]);
            }
        }

        $message = $requiresReview
            ? 'Đã cập nhật tour. Nội dung thay đổi đang chờ admin phê duyệt lại.'
            : 'Đã cập nhật tour thành công.';

        return redirect()->route('host.tours.index')->with('success', $message);
    }

    public function destroy(Tour $tour)
    {
        $this->authorize('delete', $tour);

        if ($tour->bookings()->exists()) {
            return back()->with('error', 'Không thể xóa tour đã có booking. Bạn có thể tạm dừng mở bán tour này.');
        }

        $images = $tour->images()->get();

        DB::transaction(function () use ($tour) {
            $tour->images()->delete();
            $tour->delete();
        }, 3);

        foreach ($images as $image) {
            $path = ltrim($image->path, '/');

            if (! filter_var($path, FILTER_VALIDATE_URL)) {
                Storage::disk('public')->delete(str_starts_with($path, 'storage/') ? substr($path, 8) : $path);
            }
        }

        return redirect()->route('host.tours.index')->with('success', 'Đã xóa tour thành công.');
    }
}
