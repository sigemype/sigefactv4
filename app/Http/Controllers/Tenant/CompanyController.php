<?php
namespace App\Http\Controllers\Tenant;

use App\Models\Tenant\Company;
use App\Models\Tenant\SoapType;
use App\Http\Controllers\Controller;
use App\Http\Requests\Tenant\CompanyRequest;
use App\Http\Resources\Tenant\CompanyResource;
use Illuminate\Http\Request;
use App\Http\Requests\Tenant\CompanyPseRequest;


/**
 * Class CompanyController
 *
 * @package App\Http\Controllers\Tenant
 * @mixin  Controller
 */
class CompanyController extends Controller
{
    public function create()
    {
        return view('tenant.companies.form');
    }

    public function tables()
    {
        $soap_sends = config('tables.system.soap_sends');
        $soap_types = SoapType::all();


        return compact('soap_types', 'soap_sends');
    }

    public function record()
    {
        $company = Company::active();
        $record = new CompanyResource($company);

        return $record;
    }

    public function store(CompanyRequest $request)
    {
        $id = $request->input('id');
        $company = Company::find($id);
        $company->fill($request->all());
        $company->save();

        return [
            'success' => true,
            'message' => 'Empresa actualizada'
        ];
    }

    public function uploadFile(Request $request)
    {
        if ($request->hasFile('file')) {

            $company = Company::active();

            $type = $request->input('type');

            $file = $request->file('file');
            $ext = $file->getClientOriginalExtension();
            $name = $type.'_'.$company->number.'.'.$ext;


            if (($type === 'logo')) {
                $v = request()->validate(['file' => 'required|mimes:jpeg,png,jpg,gif,svg|max:2048']);
                $file->storeAs(($type === 'logo') ? 'public/uploads/logos' : 'certificates', $name);
            }

            if (($type === 'logo_store')) {
                request()->validate(['file' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048']);
                $file->storeAs(($type === 'logo_store') ? 'public/uploads/logos' : 'certificates', $name);
            }

			if (($type === 'favicon')) {
                request()->validate(['file' => 'required|image|mimes:png|max:1024']);
                $filename = time() . '.' . $ext;
                $name = 'storage/uploads/favicons/' . $filename;
			    $file->storeAs('public/uploads/favicons', $filename);
            }

            if (($type === 'app_logo')) 
            {
                request()->validate(['file' => 'required|mimes:jpeg,png,jpg,gif,svg|max:2048']);
                $file->storeAs('public/uploads/logos', $name);
            }


            if (($type === 'img_firm')) request()->validate(['file' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048']);

            $file->storeAs(($type === 'img_firm') ? 'public/uploads/firms' : 'certificates', $name);


            $company->$type = $name;

            $company->save();

            return [
                'success' => true,
                'message' => __('app.actions.upload.success'),
                'name' => $name,
                'type' => $type
            ];
        }
        return [
            'success' => false,
            'message' =>  __('app.actions.upload.error'),
        ];
    }

    
    /**
     * Registrar datos de configuracion para enviar xml/cdr a PSE
     *
     * @param  Request $request
     * @return array
     */
    public function storeSendPse(CompanyPseRequest $request)
    {
        $company = Company::firstOrFail();
        $company->send_document_to_pse = $request->send_document_to_pse;
        $company->url_signature_pse = $request->url_signature_pse;
        $company->url_send_cdr_pse = $request->url_send_cdr_pse;
        $company->client_id_pse = $request->client_id_pse;
        $company->url_login_pse = $request->url_login_pse;
        $company->user_pse = $request->user_pse;
        $company->password_pse = $request->password_pse ?? $company->password_pse;
        $company->save();

        return [
            'success' => true,
            'message' => 'Datos guardados correctamente'
        ];
    }


    /**
     * Obtener datos de configuracion de PSE
     *
     * @param  Request $request
     * @return array
     */
    public function recordSendPse()
    {

        $company = Company::firstOrFail();

        return [
            'send_document_to_pse' => $company->send_document_to_pse,
            'url_signature_pse' => $company->url_signature_pse,
            'url_send_cdr_pse' => $company->url_send_cdr_pse,
            'client_id_pse' => $company->client_id_pse,
            'url_login_pse' => $company->url_login_pse,
            'user_pse' => $company->user_pse,
            // 'password_pse' => $company->password_pse,
        ];
        
    }

}
