<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\Activation;
use App\Models\Option;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;

class ActivationController extends AdminController
{
    public function callAction($method, $parameters)
    {
        $this->authorize('super_admin');

        return parent::callAction($method, $parameters);
    }

    public function index(): View
    {
        return \view('admin.activation.index');
    }

    public function process(): RedirectResponse
    {
        $data = request()->only(
            [
                'personal_token',
                'purchase_code',
            ]
        );

        $validator = \validator(
            $data,
            [
                'personal_token' => ['required', 'string'],
                'purchase_code' => ['required', 'string'],
            ]
        );

        if ($validator->fails()) {
            return \to_route('admin.activation')->withErrors($validator)->withInput();
        }

        $result = Activation::licenseCurlRequest($data);

        if (isset($result['item_id']) && intval($result['item_id']) === 23491785) {
            \Cache::put('license_response_result', encrypt($result), 30 * 24 * 60 * 60);

            $personal_token = Option::where('name', 'personal_token')->first();
            $personal_token->value = trim($data['personal_token']);
            $personal_token->save();

            $purchase_code = Option::where('name', 'purchase_code')->first();
            $purchase_code->value = trim($data['purchase_code']);
            $purchase_code->save();

            \session()->flash('message', __('Your license has been verified.'));

            return \to_route('admin.dashboard');
        }

        if (isset($result['message']) && !empty($result['message'])) {
            \session()->flash('danger', $result['message']);
        }

        return \to_route('admin.activation')->withInput();
    }
}
