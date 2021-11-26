<?php

    namespace App\CoreFacturalo\Helpers\Template;


    use App\Models\Tenant\Person;
    use App\Models\Tenant\User;
    use Illuminate\Support\Str;

    class ReportHelper
    {

        /**
         * Deuvelve una estructura uniforme para el vendedor en reportes
         *
         * @param string|null $userType
         * @param null        $userId
         *
         * @return array
         * @example
         *         <code>
         * @php
         *         $sellers = \App\CoreFacturalo\Helpers\Template\ReportHelper::getSellersFromRequest(
         *         $params['user_type'] , $params['user_id'] );
         *         // user_id   Creador del documento
         *         // seller_id Usuario Asignado
         *
         * @endphp
         * @if(count($sellers) > 0)
         *         <td>
         *         <p>
         *         <strong>Usuario(s): </strong>
         *         {{-- Creador --}}
         * @if(isset($sellers['user_id']))
         * @foreach ($sellers['user_id'] as $seller) - {{ $seller }} @endforeach
         * @endif
         *         {{-- Asignado --}}
         * @if(isset($sellers['seller_id']))
         * @foreach ($sellers['seller_id'] as $seller) - {{ $seller }} @endforeach
         * @endif
         *         </p>
         *         </td>
         * @endif
         *         </code>
         *
         */
        public static function getSellersFromRequest(?string $userType = null, $userId = null)
        {
            $sellers = [];
            // Vendedor asignado
            $column = 'seller_id';
            /** @var \Illuminate\Http\Request $request */
            $request = request();
            if ((empty($userType) || empty($userId)) && $request !== null) {
                if (empty($userType) && $request->has('user_type')) {
                    $userType = ( !empty($request->user_type)) ? $request->user_type : null;
                }
                if (empty($userId) && $request->has('user_id')) {
                    $userId = ( !empty($request->user_id)) ? $request->user_id : null;
                }
            }

            if (
                !empty($userType) &&
                !empty($userId)
            ) {
                if ($userType == 'CREADOR') {
                    // Quien realiza el documento
                    $column = 'user_id';
                }

                if (Str::startsWith($userId, '[')) {
                    $usersId = json_decode($userId);
                    if (count($usersId) > 0) {
                        foreach ($usersId as $seller_id) {
                            if ( !isset($sellers[$column])) $sellers[$column] = [];
                            $sellers[$column][] = self::getUserName($seller_id);
                        }
                    }
                } else {
                    if ( !isset($sellers[$column])) $sellers[$column] = [];
                    $sellers[$column][] = self::getUserName($userId);
                }

            } elseif (isset($params['sellers'])) {
                $sellers = json_decode($params['sellers']);
                if (count($sellers) > 0) {
                    foreach ($sellers as $seller_id) {
                        if ( !isset($sellers[$column])) $sellers[$column] = [];

                        $sellers[$column][] = self::getUserName($seller_id);
                    }
                }

            }
            return $sellers;
        }

        /**
         * Devuelve el nombre de usuario
         *
         * @param int|null $user_id
         *
         * @return  string
         */
        public static function getUserName(?int $user_id = 0)
        {
            $user = User::find($user_id);

            return ($user !== null) ? $user->name : '';
        }


        /**
         * Devuelve el nombre de persona
         *
         * @param int|null $user_id
         *
         * @return  string
         */
        public static function getPersonName(?int $person_id = 0)
        {
            $person = Person::find($person_id);

            return ($person !== null) ? $person->name : '';

        }

        /**
         * @param $value
         *
         * @return array|string[]
         */
        public static function getLocationData($value)
        {
            $customer = null;
            $district = '';
            $department = '';
            $province = '';
            $type_doc = $value;
            if (
                $type_doc &&
                $type_doc->customer
            ) {
                $customer = $type_doc->customer;
            }
            if ($customer != null) {
                if (
                    $customer->district &&
                    $customer->district->description
                ) {
                    $district = $customer->district->description;
                }
                if (
                    $customer->department &&
                    $customer->department->description
                ) {
                    $department = $customer->department->description;
                }
                if (
                    $customer->province &&
                    $customer->province->description
                ) {
                    $province = $customer->province->description;
                }
            }
            return [
                'district' => $district,
                'department' => $department,
                'province' => $province,
            ];
        }
    }
