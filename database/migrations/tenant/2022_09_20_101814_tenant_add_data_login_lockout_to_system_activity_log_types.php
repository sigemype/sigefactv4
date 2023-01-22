<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use App\CoreFacturalo\Helpers\Functions\SetDataHelper;
use Modules\LevelAccess\Models\SystemActivityLogType;


class TenantAddDataLoginLockoutToSystemActivityLogTypes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        SetDataHelper::createSystemActivityLogType('login_lockout', 'Bloqueo de usuario por exceder límite de intentos permitidos al iniciar sesión');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        SetDataHelper::deleteGeneralRecord(SystemActivityLogType::class, 'login_lockout');
    }
}
