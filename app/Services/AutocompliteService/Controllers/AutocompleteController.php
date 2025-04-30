<?php

namespace App\Services\AutocompliteService\Controllers;

use App\Http\Controllers\Controller;
use App\Services\AutocompliteService\Models\FullNameHelper;
use Illuminate\Http\Request;

class AutocompleteController extends Controller
{
    public function search(Request $request)
    {
        $query = urldecode($request->input('query'));
        $searchTerm = explode(' ', $query);

        $lastName = trim($searchTerm[0] ?? '');
        $firstName = trim($searchTerm[1] ?? '');
        $fatherName = trim($searchTerm[2] ?? '');

        $lastNameMatch = $this->getExactMatch(FullNameHelper::TYPE_LAST_NAME, $lastName);
        $firstNameMatch = $this->getExactMatch(FullNameHelper::TYPE_FIRST_NAME, $firstName);
        $fatherNameMatch = $this->getExactMatch(FullNameHelper::TYPE_FATHER_NAME, $fatherName);

        $gender = $lastNameMatch ? $lastNameMatch->gender : null;

        $lastNameRes = $this->getNames(FullNameHelper::TYPE_LAST_NAME, $lastName, null);
        $firstNameRes = $this->getNames(FullNameHelper::TYPE_FIRST_NAME, $firstName, $gender);
        $fatherNameRes = $this->getNames(FullNameHelper::TYPE_FATHER_NAME, $fatherName, $gender);

        if ($lastNameMatch) {
            $lastNameRes = array_fill(0, 10, $lastNameMatch->value);
        }
        if ($firstNameMatch) {
            $firstNameRes = array_fill(0, 10, $firstNameMatch->value);
        }

        $combinedNames = $this->combineNames($lastNameRes, $firstNameRes, $fatherNameRes);

        return response()->json([
            'names' => $combinedNames,
            'match' => [
                'last_name' => $lastNameMatch?->value,
                'first_name' => $firstNameMatch?->value,
                'father_name' => $fatherNameMatch?->value,
            ],
        ]);
    }

    private function getExactMatch($type, $value)
    {
        return FullNameHelper::query()
            ->where('type', $type)
            ->where('value', $value)
            ->first();
    }

    private function getNames($type, $value, $gender = null)
    {
        $query = FullNameHelper::query()
            ->where('type', $type);

        if ($type !== FullNameHelper::TYPE_LAST_NAME && $gender !== null) {
            $query->where(function ($q) use ($gender) {
                if ($gender === 'f') {
                    $q->whereIn('gender', ['f', 'u']);
                } elseif ($gender === 'm') {
                    $q->whereIn('gender', ['m', 'u']);
                } else {
                    $q->where('gender', 'u')
                    ->orWhereNull('gender');
                }
            });
        }

        if (empty($value)) {
            return $query->limit(10)->pluck('value')->toArray();
        } else {
            return $query->where('value', 'like', $value . '%')->limit(10)->pluck('value')->toArray();
        }
    }

    private function combineNames($lastNameRes, $firstNameRes, $fatherNameRes)
    {
        $combinedNames = [];
        $minLength = min(count($lastNameRes), count($firstNameRes), count($fatherNameRes));

        for ($i = 0; $i < $minLength; $i++) {
            $combinedNames[] = [
                'last_name' => $lastNameRes[$i],
                'first_name' => $firstNameRes[$i],
                'father_name' => $fatherNameRes[$i]
            ];
        }

        return $combinedNames;
    }
}
