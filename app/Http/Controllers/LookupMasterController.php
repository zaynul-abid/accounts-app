<?php

namespace App\Http\Controllers;

use App\Models\IslamicQualification;
use App\Models\JobLocation;
use App\Models\Occupation;
use App\Models\Place;
use App\Models\Qualification;
use App\Models\Relation;
use App\Models\HouseType;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class LookupMasterController extends Controller
{
    public function index(string $type): View
    {
        $config = $this->resolveType($type);
        abort_if($config === null, 404);

        $items = $config['model']::orderBy('name')->paginate(15);

        return view('backend.pages.lookup-masters.index', [
            'type' => $type,
            'title' => $config['title'],
            'items' => $items,
            'availableTypes' => $this->availableTypes(),
        ]);
    }

    public function store(Request $request, string $type): RedirectResponse
    {
        $config = $this->resolveType($type);
        abort_if($config === null, 404);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique($config['table'], 'name')->whereNull('deleted_at'),
            ],
            'description' => ['nullable', 'string'],
            'active' => ['nullable', 'boolean'],
        ]);

        $payload = [
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ];

        if (($config['status_field'] ?? 'active') === 'status') {
            $payload['status'] = ($validated['active'] ?? false) ? 'active' : 'inactive';
        } else {
            $payload['active'] = (bool) ($validated['active'] ?? false);
        }

        $config['model']::create($payload);

        return redirect()
            ->route('admin.lookups.index', $type)
            ->with('success', $config['title'] . ' created successfully.');
    }

    public function update(Request $request, string $type, int $id): RedirectResponse
    {
        $config = $this->resolveType($type);
        abort_if($config === null, 404);

        $item = $config['model']::findOrFail($id);

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique($config['table'], 'name')
                    ->ignore($item->id)
                    ->whereNull('deleted_at'),
            ],
            'description' => ['nullable', 'string'],
            'active' => ['nullable', 'boolean'],
        ]);

        $payload = [
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ];

        if (($config['status_field'] ?? 'active') === 'status') {
            $payload['status'] = ($validated['active'] ?? false) ? 'active' : 'inactive';
        } else {
            $payload['active'] = (bool) ($validated['active'] ?? false);
        }

        $item->update($payload);

        return redirect()
            ->route('admin.lookups.index', $type)
            ->with('success', $config['title'] . ' updated successfully.');
    }

    public function destroy(string $type, int $id): RedirectResponse
    {
        $config = $this->resolveType($type);
        abort_if($config === null, 404);

        $item = $config['model']::findOrFail($id);
        $item->delete();

        return redirect()
            ->route('admin.lookups.index', $type)
            ->with('success', $config['title'] . ' deleted successfully.');
    }

    private function resolveType(string $type): ?array
    {
        $types = $this->availableTypes();
        return $types[$type] ?? null;
    }

    private function availableTypes(): array
    {
        return [
            'relations' => [
                'title' => 'Relations',
                'model' => Relation::class,
                'table' => 'relations',
            ],
            'occupations' => [
                'title' => 'Occupations',
                'model' => Occupation::class,
                'table' => 'occupations',
            ],
            'qualifications' => [
                'title' => 'Qualifications',
                'model' => Qualification::class,
                'table' => 'qualifications',
            ],
            'islamic-qualifications' => [
                'title' => 'Islamic Qualifications',
                'model' => IslamicQualification::class,
                'table' => 'islamic_qualifications',
            ],
            'job-locations' => [
                'title' => 'Job Locations',
                'model' => JobLocation::class,
                'table' => 'job_locations',
            ],
            'places' => [
                'title' => 'Mahallus',
                'model' => Place::class,
                'table' => 'places',
                'status_field' => 'status',
            ],
            'house-types' => [
                'title' => 'House Types',
                'model' => HouseType::class,
                'table' => 'house_types',
                'status_field' => 'status',
            ],
        ];
    }
}
