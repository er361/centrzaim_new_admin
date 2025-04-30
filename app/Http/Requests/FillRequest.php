<?php


namespace App\Http\Requests;


use App\Models\User;
use App\Services\SiteService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class FillRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        /** @var User $user */
        $user = Auth::user();

        $stepToFill = $user->fill_status === null ? 1 : $user->fill_status + 1;
        $config = SiteService::getActiveSiteConfiguration();

        return $config['fill_steps'][$stepToFill] ?? [];
    }
}