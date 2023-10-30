<?php

namespace App\Http\Controllers;

use App\Enums\EventSearchMode;
use App\Http\Requests\Events\EventCreateRequest;
use App\Http\Requests\Events\EventListRequest;
use App\Http\Requests\Events\EventUpdateRequest;
use App\Models\Category;
use App\Models\Event;
use App\Models\Image;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): Response
    {
        $events = Event::all()->load(['category', 'image']);
        $categories = Category::all();
        return Inertia::render('Events/List', [
            'events' => $events,
            'categories' => $categories
        ]);
    }

    public function list(EventListRequest $request): Collection
    {
        $query = Event::query();
        /** @var EventSearchMode|null $mode */
        $mode = EventSearchMode::tryFrom($request->query('mode'));
        $dateFrom = $request->query('dateFrom');
        $dateTo = $request->query('dateTo');
        if ($mode === EventSearchMode::ANY && $dateFrom && $dateTo) {
            $query->where(function (Builder $builder) use ($dateTo, $dateFrom) {
                $builder->where('start_date' ,'<=', $dateTo)
                    ->where('end_date', '>=', $dateFrom);
            })->orWhere(function (Builder $builder) use ($dateFrom, $dateTo) {
                $builder->where('end_date', '>=', $dateFrom)
                    ->where('start_date', '<=', $dateTo);
            });
        } else {
            $this->applyDateFilters($query, $mode, $dateFrom, $dateTo);
        }
        return $query->get();

    }

    private function applyDateFilters(Builder $query, EventSearchMode|null $mode, string|null $dateFrom, string|null $dateTo)
    {
        if ($mode && $dateFrom) {
            $column = match ($mode) {
                EventSearchMode::START => 'start_date',
                EventSearchMode::END, EventSearchMode::ANY => 'end_date'
            };
            $query->where($column, '>=', $dateFrom);
        }
        if ($mode && $dateTo) {
            $column = match ($mode) {
                EventSearchMode::START, EventSearchMode::ANY => 'start_date',
                EventSearchMode::END => 'end_date'
            };
            $query->where($column, '<=', $dateTo);
        }
    }

    public function get(Event $event): Event
    {
        return $event->load(['category', 'image']);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EventCreateRequest $request): Event
    {
        $data = $request->all();
        /** @var UploadedFile|null $file */
        $file = $data['image'] ?? null;
        /** @var Event $event */
        $event = Event::query()->create($data);
        if ($file) {
            $filename = uniqid() . '.' . $file->extension();
            Storage::disk('images')->put($filename, $file->getContent());
            Image::query()->create(['filename' => $filename, 'event_id' => $event->id]);
        }
        return $event->refresh();
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EventUpdateRequest $request, Event $event): Event
    {
        $data = $request->all();
        /** @var UploadedFile|null $file */
        $file = $data['image'] ?? null;
        if ($request->has('image')) {
            $event->image()?->delete();
        }
        if ($file) {
            $filename = uniqid() . '.' . $file->extension();
            Storage::disk('images')->put($filename, $file->getContent());
            Image::query()->create(['filename' => $filename, 'event_id' => $event->id]);
        }
        $event->update($data);
        return $event->load(['category', 'image']);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Event $event): void
    {
        $event->delete();
    }
}
