<?php
namespace App\Http\Requests\Admin;

use App\Services\PostbackService\PostbackServiceStepDecider;
use App\Services\SettingsService\Enums\FrontendSettingsEnum;
use App\Services\SettingsService\Enums\SettingNameEnum;
use App\Services\SettingsService\SettingsService;
use App\Services\SiteService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SettingsStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
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
        $rules = [
            'is_phone_verification_enabled' => [
                'required',
                'in:0,1',
            ],

            'is_payments_enabled' => [
                'required',
                'in:0,1',
            ],

            'should_redirect_to_register_page_from_sources' => [
                'required',
                'in:0,1',
            ],
            'show_banks_notification_modal' => [
                'required',
                'in:0,1',
            ],

            'postback_step' => [
                'required',
                'string',
                Rule::in(
                    array_keys(PostbackServiceStepDecider::STEPS)
                ),
            ],
        ];

        $config = SiteService::getActiveSiteConfiguration();
        $steps = array_keys($config['fill_steps']);
        $stepRules = [];

        foreach ($steps as $step) {
            $stepRules['is_account_fill_step_'.$step.'_enabled'] = [
                'required',
                'in:0,1',
            ];
        }

        foreach (SettingNameEnum::cases() as $setting) {
            $stepRules[$setting->value] = [
                'nullable',
                'string',
                'max:255',
            ];
        }

        $allRules = $rules + $stepRules + FrontendSettingsEnum::getValidationRules();
        return $allRules;
    }
}
