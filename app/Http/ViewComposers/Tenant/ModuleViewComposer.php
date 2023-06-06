<?php

namespace App\Http\ViewComposers\Tenant;

use App\Models\System\Configuration;
use App\Models\Tenant\Configuration as TenantConfiguration;
use App\Models\Tenant\Module;

class ModuleViewComposer
{
    public function compose($view)
    {
        $modules = auth()->user()->modules()->pluck('value')->toArray();
        /*
        $systemConfig = Configuration::select('use_login_global')->first();
        */
        $systemConfig = Configuration::getDataModuleViewComposer();

        if(count($modules) > 0) {
            $view->vc_modules = $modules;
        } else {
            $view->vc_modules = Module::all()->pluck('value')->toArray();
        }
        $view->vc_configuration = TenantConfiguration::first();

        $view->useLoginGlobal = $systemConfig->use_login_global;

        $view->tenant_show_ads = $systemConfig->tenant_show_ads;
        $view->url_tenant_image_ads = $systemConfig->getUrlTenantImageAds();

    }
}
