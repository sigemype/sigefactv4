<?php

    namespace App\Http\Controllers\Tenant;

    use App\Http\Controllers\Controller;
    use App\Models\Tenant\Configuration;
    use App\Models\Tenant\User;
    use Auth;
    use Illuminate\Contracts\View\Factory;
    use Illuminate\Foundation\Application;
    use Illuminate\View\View;

    /**
     * Class SettingController
     *
     * @package App\Http\Controllers\Tenant
     * @mixin Controller
     */
    class SettingController extends Controller
    {
        /**
         * @return Factory|Application|View
         */
        public function listBanks()
        {
            return view('tenant.settings.list_banks');
        }

        /**
         * @return Factory|Application|View
         */
        public function listAccountBanks()
        {
            return view('tenant.settings.list_account_banks');
        }

        /**
         * @return Factory|Application|View
         */
        public function listCurrencies()
        {
            return view('tenant.settings.list_currencies');
        }

        /**
         * @return Factory|Application|View
         */
        public function listCards()
        {
            return view('tenant.settings.list_cards');
        }

        /**
         * @return Factory|Application|View
         */
        public function listPlatforms()
        {
            return view('tenant.settings.list_platforms');
        }

        /**
         * @return Factory|Application|View
         */
        public function listAttributes()
        {
            return view('tenant.settings.list_attributes');
        }

        /**
         * @return Factory|Application|View
         */
        public function listDetractions()
        {
            return view('tenant.settings.list_detractions');
        }

        /**
         * @return Factory|Application|View
         */
        public function listUnits()
        {
            return view('tenant.settings.list_units');
        }

        /**
         * @return Factory|Application|View
         */
        public function listPaymentMethods()
        {
            return view('tenant.settings.list_payment_methods');
        }

        /**
         * @return Factory|Application|View
         */
        public function listIncomes()
        {
            return view('tenant.settings.list_incomes');
        }

        /**
         * @return Factory|Application|View
         */
        public function listPayments()
        {
            return view('tenant.settings.list_payments');
        }

        /**
         * @return Factory|Application|View
         */
        public function listVouchersType()
        {
            return view('tenant.settings.list_vouchers_type');
        }

        /**
         * @return Factory|Application|View
         */
        public function listReports()
        {
            $configuration = Configuration::first();
            return view('tenant.reports.list', compact('configuration'));
        }

        /**
         * @return Factory|Application|View
         */
        public function listTransferReasonTypes()
        {
            return view('tenant.settings.list_transfer_reason_types');
        }

        /**
         * @return Factory|Application|View
         */
        public function indexSettings()
        {
            /** @var User $user */
            $user = Auth::user();
            $companyMenu = $user->levels->firstWhere('value', 'configuration_company');
            $visualMenu = $user->levels->firstWhere('value', 'configuration_visual');
            $advanceMenu = $user->levels->firstWhere('value', 'configuration_advance');

            return view('tenant.settings.list_settings', compact(
                'user',
                'companyMenu',
                'visualMenu',
                'advanceMenu'
            ));
        }

        /**
         * @return Factory|Application|View
         */
        public function listExtras()
        {
            // vista blade no vue
            $configuration = Configuration::first();
            return view('tenant.settings.list_extras')->with('apk_url', $configuration->apk_url);
        }
    }
