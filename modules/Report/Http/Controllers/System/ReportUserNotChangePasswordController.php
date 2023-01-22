<?php
namespace Modules\Report\Http\Controllers\System;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use App\Models\System\Client;
use Hyn\Tenancy\Environment;
use Modules\LevelAccess\Http\Controllers\SystemActivityLogGeneralController;
use App\Models\Tenant\{
    User,
    Configuration
};
use Carbon\Carbon;
use Modules\Report\Http\Resources\System\UserCollection;


class ReportUserNotChangePasswordController extends Controller
{

    public function index() 
    {
        return view('report::system.user_not_change_password.index');
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

        $all_users = User::whereFilterWithOutRelations()->get();
        $quantity_month_remember_change_password = Configuration::getRecordIndividualColumn('quantity_month_remember_change_password');
        
        $users_not_change_password = $all_users->filter(function($row) use($quantity_month_remember_change_password){

            $quantity_month = $quantity_month_remember_change_password;
            $last_password_update = $row->last_password_update;
    
            $change_success = true;
    
            if(!is_null($last_password_update))
            {
                $limit_date = Carbon::parse($last_password_update)->addMonths($quantity_month);
                $today = Carbon::now();
                
                if($limit_date->lte($today)) return $change_success;
    
                return false;
            }
    
            return $change_success;
        });

        return new UserCollection($users_not_change_password);

    }


}
