<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\HouseType;
use App\Models\Place;
use App\Models\HouseCreation;
use App\Models\Member;
use App\Models\Relation;
use App\Models\Qualification;
use App\Models\IslamicQualification;
use App\Models\Occupation;
use App\Models\JobLocation;
use App\Support\SlNumberGenerator;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class HouseCreationController extends Controller
{
    public function index()
    {
        $this->houseOwnerRelation();
        $mahallus = Place::where('status', 'active')->get();
        $houseTypes = HouseType::where('status', 'active')->get();
        $houses = HouseCreation::with(['mahallu', 'houseType'])
            ->latest()
            ->paginate(15);
        $relations = Relation::where('active', 1)->get();
        $qualifications = Qualification::where('active', 1)->get();
        $islamicQualifications = IslamicQualification::where('active', 1)->get();
        $occupations = Occupation::where('active', 1)->get();
        $jobLocations = JobLocation::where('active', 1)->get();

        return view('frontend.pages.house-creation.index', compact(
            'mahallus',
            'houseTypes',
            'houses',
            'relations',
            'qualifications',
            'islamicQualifications',
            'occupations',
            'jobLocations'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'registration_date' => 'required|date',
            'place_id'          => 'required|exists:places,id',
            'house_owner'       => 'required|string|max:255',
            'house_name'        => 'required|string|max:255',
            'jamath_house_no'   => 'required|string|max:50',
            'house_type_id'     => 'required|exists:house_types,id',
            'floors'            => 'required|integer|min:0',
            'ward_no'           => 'nullable|string|max:50',
            'house_no'          => 'nullable|string|max:50',
            'address'           => 'nullable|string',
            'phone'             => 'nullable|string|max:20',
            'mobile'            => 'required|string|max:20',
            'reg_fee'           => 'nullable|numeric|min:0',
            'default_amount'    => 'required_if:house_sub,1|nullable|numeric|min:0',
            'due_amount'        => 'required_if:house_sub,1|nullable|numeric|min:0',
            'owner_member_name' => 'nullable|string|max:255',
            'owner_member_adhar_number' => 'nullable|string|max:20|unique:members,adhar_number',
            'owner_member_image' => 'nullable|image|max:2048',
            'owner_member_father_name' => 'nullable|string|max:255',
            'owner_member_mother_name' => 'nullable|string|max:255',
            'owner_member_dob' => 'nullable|date',
            'owner_member_age' => 'nullable|integer',
            'owner_member_gender' => 'nullable|in:Male,Female,Other',
            'owner_member_blood_group' => 'nullable|string|max:10',
            'owner_member_mobile' => 'nullable|string|max:20',
            'owner_member_whatsapp' => 'nullable|string|max:20',
            'owner_member_marital_status' => 'nullable|in:Single,Married',
            'owner_member_spouse_name' => 'nullable|string|max:255',
            'owner_member_date' => 'nullable|date',
            'owner_member_relation_id' => 'nullable|exists:relations,id',
            'owner_member_islamic_qualification_id' => 'nullable|exists:islamic_qualifications,id',
            'owner_member_qualification_id' => 'nullable|exists:qualifications,id',
            'owner_member_occupation_id' => 'nullable|exists:occupations,id',
            'owner_member_job_location_id' => 'nullable|exists:job_locations,id',
            'owner_member_subscription' => 'nullable|boolean',
            'owner_member_default_subscription' => 'nullable|boolean',
            'owner_member_subscription_amount' => 'nullable|numeric|min:0',
            'owner_member_subscription_type' => 'nullable|in:Monthly,Yearly',
            'owner_member_narration' => 'nullable|string',
            'owner_member_op_amount' => 'nullable|numeric|min:0',
            'owner_member_active' => 'nullable|boolean',
        ], [], [
            'place_id' => 'mahallu',
        ]);

        $validated['house_sub'] = $request->has('house_sub') ? 1 : 0;
        $validated['active']    = $request->has('active') ? 1 : 0;
        $validated['ward_no']   = $validated['ward_no'] ?? '';
        $validated['house_no']  = $validated['house_no'] ?? '';
        $validated['reg_fee']   = $validated['reg_fee'] ?? 0;

        $houseData = Arr::only($validated, [
            'registration_date',
            'place_id',
            'house_owner',
            'house_name',
            'jamath_house_no',
            'house_type_id',
            'floors',
            'ward_no',
            'house_no',
            'address',
            'phone',
            'mobile',
            'reg_fee',
            'house_sub',
            'default_amount',
            'due_amount',
            'active',
        ]);

        DB::transaction(function () use ($houseData, $request) {
            $mahallu = Place::findOrFail($houseData['place_id']);
            $houseData['sl_number'] = SlNumberGenerator::forHouse($mahallu);

            $house = HouseCreation::create($houseData);

            // Ensure house owner is also available as a member record.
            $ownerRelation = $this->houseOwnerRelation();

            $ownerImagePath = $request->hasFile('owner_member_image')
                ? $request->file('owner_member_image')->store('owner-images', 'public')
                : null;

            Member::create([
                'house_id' => $house->id,
                'date' => $request->input('owner_member_date', $house->registration_date),
                'sl_number' => SlNumberGenerator::forMember($mahallu),
                'name' => $request->input('owner_member_name', $house->house_owner),
                'adhar_number' => $request->input('owner_member_adhar_number'),
                'owner_image' => $ownerImagePath,
                'father_name' => $request->input('owner_member_father_name'),
                'mother_name' => $request->input('owner_member_mother_name'),
                'dob' => $request->input('owner_member_dob'),
                'age' => $request->input('owner_member_age'),
                'gender' => $request->input('owner_member_gender'),
                'blood_group' => $request->input('owner_member_blood_group'),
                'relation_id' => $request->input('owner_member_relation_id', $ownerRelation->id),
                'mobile_number' => $request->input('owner_member_mobile', $house->mobile),
                'whatsapp_number' => $request->input('owner_member_whatsapp'),
                'marital_status' => $request->input('owner_member_marital_status', 'Single'),
                'spouse_name' => $request->input('owner_member_spouse_name'),
                'islamic_qualification_id' => $request->input('owner_member_islamic_qualification_id'),
                'qualification_id' => $request->input('owner_member_qualification_id'),
                'occupation_id' => $request->input('owner_member_occupation_id'),
                'job_location_id' => $request->input('owner_member_job_location_id'),
                'subscription' => $request->boolean('owner_member_subscription') ? 1 : 0,
                'default_subscription' => $request->boolean('owner_member_default_subscription') ? 1 : 0,
                'subscription_amount' => $request->input('owner_member_subscription_amount'),
                'subscription_type' => $request->input('owner_member_subscription_type'),
                'narration' => $request->input('owner_member_narration'),
                'op_amount' => $request->input('owner_member_op_amount'),
                'active' => $request->boolean('owner_member_active') ? 1 : 0,
            ]);
        });

        return redirect()->route('house-creations.index')
            ->with('success', 'House record created successfully!');
    }

    public function update(Request $request, HouseCreation $houseCreation)
    {
        $validated = $request->validate([
            'registration_date' => 'required|date',
            'place_id'          => 'required|exists:places,id',
            'house_owner'       => 'required|string|max:255',
            'house_name'        => 'required|string|max:255',
            'jamath_house_no'   => 'required|string|max:50',
            'house_type_id'     => 'required|exists:house_types,id',
            'floors'            => 'required|integer|min:0',
            'ward_no'           => 'nullable|string|max:50',
            'house_no'          => 'nullable|string|max:50',
            'address'           => 'nullable|string',
            'phone'             => 'nullable|string|max:20',
            'mobile'            => 'required|string|max:20',
            'reg_fee'           => 'nullable|numeric|min:0',
            'default_amount'    => 'required_if:house_sub,1|nullable|numeric|min:0',
            'due_amount'        => 'required_if:house_sub,1|nullable|numeric|min:0',
        ], [], [
            'place_id' => 'mahallu',
        ]);

        $validated['house_sub'] = $request->has('house_sub') ? 1 : 0;
        $validated['active']    = $request->has('active') ? 1 : 0;
        $validated['ward_no']   = $validated['ward_no'] ?? '';
        $validated['house_no']  = $validated['house_no'] ?? '';
        $validated['reg_fee']   = $validated['reg_fee'] ?? 0;

        if ((int) $validated['place_id'] !== (int) $houseCreation->place_id || blank($houseCreation->sl_number)) {
            $validated['sl_number'] = SlNumberGenerator::forHouse(
                Place::findOrFail($validated['place_id']),
                $houseCreation->id
            );
        }

        $houseCreation->update($validated);

        return redirect()->route('house-creations.index')
            ->with('success', 'House record updated successfully!');
    }

    public function destroy(HouseCreation $houseCreation)
    {
        try {
            // Optional: Add authorization check if needed
            // $this->authorize('delete', $houseCreation);

            // 1. Optional: Check if the house has related records
            //    (members, payments, etc.) - prevent deletion if important data exists
            // if ($houseCreation->members()->exists()) {
            //     return redirect()->back()
            //         ->with('error', 'Cannot delete this house because it has registered members.');
            // }

            // 2. Delete the record
            $houseCreation->delete();

            return redirect()
                ->route('house-creations.index')
                ->with('success', 'House record deleted successfully.');

        } catch (\Exception $e) {
            // You can log the error here if you want
            // \Log::error('House deletion failed: ' . $e->getMessage());

            return redirect()
                ->back()
                ->with('error', 'Failed to delete the house record. Please try again.');
        }
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
}
