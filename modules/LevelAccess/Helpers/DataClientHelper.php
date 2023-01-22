<?php

namespace Modules\LevelAccess\Helpers;

use Stevebauman\Location\Facades\Location;


class DataClientHelper
{

    private $ip;
            
    /**
     * @return void
     */
    public function __construct()
    {
        $this->setClientIp();
    }

        
    /**
     *
     * @return string
     */
    public function getClientIp()
    {
        return $this->ip;
    }

        
    /**
     * 
     * Obtener información de la ubicacion desde la ip
     *
     * @param  string $ip
     * @return array
     */
    public function getLocation($ip = null)
    {
        $current_ip = $ip ?? $this->ip;
        $response = null;

        if($current_ip)
        {
            $position = Location::get($current_ip);
    
            if($position) 
            {
                $response = [
                    'country_name' => $position->countryName,
                    'country_code' => $position->countryCode,
                    'region_code' => $position->regionCode,
                    'region_name' => $position->regionName,
                    'city_name' => $position->cityName,
                    'latitude' => $position->latitude,
                    'longitude' => $position->longitude,
                    'timezone' => $position->timezone,
                ];
            }
        }
        
        return $response;
    }


    /**
     * Asignar ip del cliente
     * 
     * @return void
     */
    public function setClientIp()
    {

        if (!empty($_SERVER['HTTP_CLIENT_IP'])) 
        {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } 
        else if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) 
        {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } 
        else 
        {
            $ip = $_SERVER['REMOTE_ADDR'] ?? null;
        }

        $this->ip = $this->isValidIp($ip) ? $ip : null;
    }


    /**
     *
     * @param string $ip
     * @return bool
     */
    public function isValidIp($ip)
    {
        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) 
        {
            return true;
        }

        return false;
    }

 
}