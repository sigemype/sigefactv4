<?php
namespace Modules\Report\Http\Controllers\System;

use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\Request;
use App\Models\System\Client;
use App\Http\Resources\System\ClientCollection;


class ReportController extends Controller
{

    public function listReports()
    {
        return view('report::system.list_reports');
    }

    public function clients()
    {
        $records = Client::latest()->get(); 
        return new ClientCollection($records);
    }

}
