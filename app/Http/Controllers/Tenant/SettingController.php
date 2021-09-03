<?php

    namespace App\Http\Controllers\Tenant;

    use App\Http\Controllers\Controller;

    /**
     * Class SettingController
     *
     * @package App\Http\Controllers\Tenant
     * @mixin Controller
     */
    class SettingController extends Controller {
        /**
         * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
         */
        public function listBanks() {
            return view('tenant.settings.list_banks');
        }

        /**
         * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
         */
        public function listAccountBanks() {
            return view('tenant.settings.list_account_banks');
        }

        /**
         * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
         */
        public function listCurrencies() {
            return view('tenant.settings.list_currencies');
        }

        /**
         * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
         */
        public function listCards() {
            return view('tenant.settings.list_cards');
        }

        /**
         * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
         */
        public function listPlatforms() {
            return view('tenant.settings.list_platforms');
        }

        /**
         * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
         */
        public function listAttributes() {
            return view('tenant.settings.list_attributes');
        }

        /**
         * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
         */
        public function listDetractions() {
            return view('tenant.settings.list_detractions');
        }

        /**
         * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
         */
        public function listUnits() {
            return view('tenant.settings.list_units');
        }

        /**
         * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
         */
        public function listPaymentMethods() {
            return view('tenant.settings.list_payment_methods');
        }

        /**
         * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
         */
        public function listIncomes() {
            return view('tenant.settings.list_incomes');
        }

        /**
         * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
         */
        public function listPayments() {
            return view('tenant.settings.list_payments');
        }

        /**
         * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
         */
        public function listVouchersType() {
            return view('tenant.settings.list_vouchers_type');
        }

        /**
         * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
         */
        public function listReports() {
            return view('tenant.reports.list');
        }

        /**
         * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
         */
        public function listTransferReasonTypes() {
            return view('tenant.settings.list_transfer_reason_types');
        }

        /**
         * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
         */
        public function indexSettings() {
            return view('tenant.settings.list_settings');
        }

        /**
         * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\View\View
         */
        public function listExtras() {
            return view('tenant.settings.list_extras');
        }
    }
