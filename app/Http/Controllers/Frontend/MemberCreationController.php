<?php

namespace App\Http\Controllers\Frontend;

use App\Models\HouseCreation;
use App\Models\IslamicQualification;
use App\Models\JobLocation;
use App\Models\Member;
use App\Models\MemberReport;
use App\Models\Occupation;
use App\Models\Qualification;
use App\Models\Relation;
use App\Support\SlNumberGenerator;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

class MemberCreationController extends Controller
{
    /**
     * Display member creation page with all lookups
     */
    public function index()
    {
        $this->houseOwnerRelation();
        $relations = Relation::where('active', 1)->get();
        $qualifications = Qualification::where('active', 1)->get();
        $islamicQualifications = IslamicQualification::where('active', 1)->get();
        $occupations = Occupation::where('active', 1)->get();
        $jobLocations = JobLocation::where('active', 1)->get();
        $selectedHouse = null;

        if ($oldHouseId = session()->getOldInput('house_id')) {
            $selectedHouse = HouseCreation::with('place')->find($oldHouseId);
        }

        return view('frontend.pages.member-creation.index', compact(
            'relations',
            'qualifications',
            'islamicQualifications',
            'occupations',
            'jobLocations',
            'selectedHouse'
        ));
    }

    public function memberList(Request $request)
    {
        $housesQuery = HouseCreation::with(['mahallu', 'houseType'])
            ->withCount('members')
            ->latest();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $housesQuery->where(function ($query) use ($search) {
                $query->where('house_name', 'LIKE', "%{$search}%")
                    ->orWhere('house_no', 'LIKE', "%{$search}%")
                    ->orWhere('jamath_house_no', 'LIKE', "%{$search}%")
                    ->orWhere('house_owner', 'LIKE', "%{$search}%");
            });
        }

        $houses = $housesQuery->paginate(12)->withQueryString();

        $selectedHouse = null;
        if ($request->filled('house_id')) {
            $selectedHouse = HouseCreation::with([
                'mahallu',
                'houseType',
                'members.relation',
                'members.qualification',
                'members.islamicQualification',
                'members.occupation',
                'members.jobLocation',
            ])->find($request->integer('house_id'));
        }

        $ownerRelation = $this->houseOwnerRelation();
        $relations = Relation::where('active', 1)->orderBy('name')->get();
        $qualifications = Qualification::where('active', 1)->orderBy('name')->get();
        $islamicQualifications = IslamicQualification::where('active', 1)->orderBy('name')->get();
        $occupations = Occupation::where('active', 1)->orderBy('name')->get();
        $jobLocations = JobLocation::where('active', 1)->orderBy('name')->get();

        return view('frontend.pages.member-creation.member-list', compact(
            'houses',
            'selectedHouse',
            'ownerRelation',
            'relations',
            'qualifications',
            'islamicQualifications',
            'occupations',
            'jobLocations'
        ));
    }

    /**
     * Search houses by name/number
     */
    public function searchHouses(Request $request)
    {
        $query = $request->input('q');

        $houses = HouseCreation::where('active', 1)
            ->where(function($q) use ($query) {
                $q->where('house_name', 'LIKE', "%{$query}%")
                  ->orWhere('house_no', 'LIKE', "%{$query}%")
                  ->orWhere('jamath_house_no', 'LIKE', "%{$query}%");
            })
            ->with('place', 'houseType')
            ->limit(10)
            ->get();

        return response()->json($houses);
    }

    /**
     * Get house details for auto-fill
     */
    public function getHouseDetails(HouseCreation $house)
    {
        return response()->json($house->load('place'));
    }

    /**
     * Get house members
     */
    public function getHouseMembers(HouseCreation $house)
    {
        $members = $house->members()->with('relation')->get();
        return response()->json($members);
    }

    /**
     * Store a new member
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'house_id' => 'required|exists:house_creations,id',
            'date' => 'required|date',
            'name' => 'required|string|max:255',
            'adhar_number' => 'nullable|string|max:20|unique:members,adhar_number',
            'owner_image' => 'nullable|image|max:2048',
            'father_name' => 'nullable|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'marital_status' => 'required|in:Single,Married',
            'spouse_name' => 'nullable|string|max:255',
            'relation_id' => 'nullable|exists:relations,id',
            'dob' => 'nullable|date',
            'age' => 'nullable|integer',
            'gender' => 'nullable|in:Male,Female,Other',
            'blood_group' => 'nullable|string',
            'mobile_number' => 'nullable|string|max:20',
            'whatsapp_number' => 'nullable|string|max:20',
            'islamic_qualification_id' => 'nullable|exists:islamic_qualifications,id',
            'qualification_id' => 'nullable|exists:qualifications,id',
            'occupation_id' => 'nullable|exists:occupations,id',
            'job_location_id' => 'nullable|exists:job_locations,id',
            'subscription' => 'nullable|boolean',
            'default_subscription' => 'nullable|boolean',
            'subscription_amount' => 'nullable|numeric',
            'amount' => 'nullable|numeric',
            'subscription_type' => 'nullable|in:Monthly,Yearly',
            'narration' => 'nullable|string',
            'op_amount' => 'nullable|numeric',
            'active' => 'nullable|boolean',
        ]);

        // Calculate age from DOB only when age is not entered manually
        if (($validated['dob'] ?? null) && empty($validated['age'])) {
            $validated['age'] = \Carbon\Carbon::parse($validated['dob'])->age;
        }

        // Accept "amount" input name from form and map to subscription_amount
        if ($request->filled('amount')) {
            $validated['subscription_amount'] = $request->input('amount');
        }
        unset($validated['amount']);

        // Handle checkbox values
        $validated['subscription'] = $request->has('subscription') ? 1 : 0;
        $validated['default_subscription'] = $request->has('default_subscription') ? 1 : 0;
        $validated['active'] = $request->has('active') ? 1 : 0;

        // If subscription is disabled, clear related fields
        if (! $validated['subscription']) {
            $validated['default_subscription'] = 0;
            $validated['subscription_amount'] = null;
            $validated['subscription_type'] = null;
        }

        $house = HouseCreation::with('mahallu')->findOrFail($validated['house_id']);
        $validated['sl_number'] = SlNumberGenerator::forMember($house->mahallu);

        $member = Member::create($validated);

        // Create member report if subscription is enabled
        if ($validated['subscription'] && $validated['subscription_amount'] && $validated['subscription_type']) {
            $subscriptionType = $validated['subscription_type'];
            $currentYear = now()->year;
            $nextYear = $currentYear + 1;

            // Format posting year
            if ($subscriptionType === 'Yearly') {
                $posting_year = $currentYear;
                $transaction_type = 'Yearly Subscription';
            } else {
                $posting_year = $currentYear . '-' . substr($currentYear + 1, -2);
                $transaction_type = 'Monthly Subscription';
            }

            MemberReport::create([
                'member_id' => $member->id,
                'receipt_no' => MemberReport::generateReceiptNo(),
                'date' => now()->toDateString(),
                'name' => $member->name,
                'transaction_type' => $transaction_type,
                'posting_year' => $posting_year,
                'description' => $validated['narration'] ?? null,
                'debit' => $validated['subscription_amount'],
                'credit' => 0,
                'balance' => $validated['subscription_amount'],
                'status' => 'completed',
            ]);
        }

        return redirect()
            ->route('members.index')
            ->with('success', 'Member created successfully!');
    }

    /**
     * Update member
     */
    public function update(Request $request, Member $member)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'adhar_number' => 'nullable|string|max:20|unique:members,adhar_number,' . $member->id,
            'owner_image' => 'nullable|image|max:2048',
            'father_name' => 'nullable|string|max:255',
            'mother_name' => 'nullable|string|max:255',
            'marital_status' => 'required|in:Single,Married',
            'spouse_name' => 'nullable|string|max:255',
            'relation_id' => 'nullable|exists:relations,id',
            'dob' => 'nullable|date',
            'age' => 'nullable|integer',
            'gender' => 'nullable|in:Male,Female,Other',
            'blood_group' => 'nullable|string',
            'mobile_number' => 'nullable|string|max:20',
            'whatsapp_number' => 'nullable|string|max:20',
            'islamic_qualification_id' => 'nullable|exists:islamic_qualifications,id',
            'qualification_id' => 'nullable|exists:qualifications,id',
            'occupation_id' => 'nullable|exists:occupations,id',
            'job_location_id' => 'nullable|exists:job_locations,id',
            'subscription' => 'nullable|boolean',
            'default_subscription' => 'nullable|boolean',
            'subscription_amount' => 'nullable|numeric',
            'amount' => 'nullable|numeric',
            'subscription_type' => 'nullable|in:Monthly,Yearly',
            'narration' => 'nullable|string',
            'op_amount' => 'nullable|numeric',
            'active' => 'nullable|boolean',
        ]);

        // Calculate age from DOB only when age is not entered manually
        if (($validated['dob'] ?? null) && empty($validated['age'])) {
            $validated['age'] = \Carbon\Carbon::parse($validated['dob'])->age;
        }

        // Accept "amount" input name from form and map to subscription_amount
        if ($request->filled('amount')) {
            $validated['subscription_amount'] = $request->input('amount');
        }
        unset($validated['amount']);

        // Handle checkbox values
        $validated['subscription'] = $request->has('subscription') ? 1 : 0;
        $validated['default_subscription'] = $request->has('default_subscription') ? 1 : 0;
        $validated['active'] = $request->has('active') ? 1 : 0;

        // If subscription is disabled, clear related fields
        if (! $validated['subscription']) {
            $validated['default_subscription'] = 0;
            $validated['subscription_amount'] = null;
            $validated['subscription_type'] = null;
        }

        if ($request->hasFile('owner_image')) {
            if ($member->owner_image) {
                Storage::disk('public')->delete($member->owner_image);
            }

            $validated['owner_image'] = $request->file('owner_image')->store('owner-images', 'public');
        } else {
            unset($validated['owner_image']);
        }

        $member->update($validated);

        $ownerRelation = $this->houseOwnerRelation();
        if ($ownerRelation && (int) ($validated['relation_id'] ?? 0) === (int) $ownerRelation->id) {
            $member->house?->members()
                ->where('id', '!=', $member->id)
                ->where('relation_id', $ownerRelation->id)
                ->update(['relation_id' => null]);

            $member->house?->update(['house_owner' => $member->name]);
        }

        return response()->json(['success' => true, 'message' => 'Member updated successfully!']);
    }

    public function changeOwner(HouseCreation $house, Member $member)
    {
        abort_if((int) $member->house_id !== (int) $house->id, 404);

        $ownerRelation = $this->houseOwnerRelation();

        $house->members()
            ->where('id', '!=', $member->id)
            ->where('relation_id', $ownerRelation->id)
            ->update(['relation_id' => null]);

        $member->update(['relation_id' => $ownerRelation->id]);
        $house->update(['house_owner' => $member->name]);

        return redirect()
            ->route('members.list', ['house_id' => $house->id])
            ->with('success', $member->name . ' is now the house owner.');
    }

    private function houseOwnerRelation(): Relation
    {
        $houseOwner = Relation::firstOrCreate(
            ['name' => 'House Owner'],
            ['description' => 'Default relation for house owner', 'active' => 1]
        );

        $owner = Relation::where('name', 'Owner')
            ->where('id', '!=', $houseOwner->id)
            ->first();

        if ($owner) {
            Member::where('relation_id', $owner->id)->update(['relation_id' => $houseOwner->id]);
            $owner->delete();
        }

        return $houseOwner;
    }

    /**
     * Delete member
     */
    public function destroy(Member $member)
    {
        $member->delete();
        return response()->json(['success' => true, 'message' => 'Member deleted successfully!']);
    }

    /**
     * Store new relation
     */
    public function storeRelation(Request $request)
    {
        if (strcasecmp(trim((string) $request->input('name')), 'Owner') === 0) {
            $request->merge(['name' => 'House Owner']);
        }

        $validated = $request->validate([
            'name' => 'required|string|unique:relations,name',
            'description' => 'nullable|string',
        ]);

        $relation = Relation::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'active' => 1,
        ]);

        return response()->json($relation);
    }

    /**
     * Store new islamic qualification
     */
    public function storeIslamicQualification(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:islamic_qualifications,name',
            'description' => 'nullable|string',
        ]);

        $islamicQualification = IslamicQualification::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'active' => 1,
        ]);

        return response()->json($islamicQualification);
    }

    /**
     * Store new qualification
     */
    public function storeQualification(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:qualifications,name',
            'description' => 'nullable|string',
        ]);

        $qualification = Qualification::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'active' => 1,
        ]);

        return response()->json($qualification);
    }

    /**
     * Store new occupation
     */
    public function storeOccupation(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:occupations,name',
            'description' => 'nullable|string',
        ]);

        $occupation = Occupation::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'active' => 1,
        ]);

        return response()->json($occupation);
    }

    /**
     * Store new job location
     */
    public function storeJobLocation(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|unique:job_locations,name',
            'description' => 'nullable|string',
        ]);

        $jobLocation = JobLocation::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'active' => 1,
        ]);

        return response()->json($jobLocation);
    }
}
