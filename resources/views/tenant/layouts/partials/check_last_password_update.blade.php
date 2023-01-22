@if ($vc_check_last_password_update)

    @if ($vc_check_last_password_update->enabled_remember_change_password)
        
        <tenant-remember-change-password :configuration-last-password-update="{{ json_encode($vc_check_last_password_update) }}"></tenant-remember-change-password>
    
    @endif
    
@endif
