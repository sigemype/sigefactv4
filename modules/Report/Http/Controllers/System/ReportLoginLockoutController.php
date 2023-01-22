<?php
namespace Modules\Report\Http\Controllers\System;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use App\Models\System\Client;
use Hyn\Tenancy\Environment;
use Modules\LevelAccess\Http\Controllers\SystemActivityLogGeneralController;


class ReportLoginLockoutController extends Controller
{

    public function index() 
    {
        return view('report::system.system_activity_logs.login_lockout.index');
    }
   
    
    /**
     *
     * @return array
     */
    public function columns()
    {
        return app(SystemActivityLogGeneralController::class)->columns();
    }


    public function records(Request $request)
    {
        $client = Client::findOrFail($request->record_id);
        $tenancy = app(Environment::class);
        $tenancy->tenant($client->hostname->website);

        return app(SystemActivityLogGeneralController::class)->records($request);
    }

}
