<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Ifsnop\Mysqldump as IMysqldump;
use Exception;

class ChangePass extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'passwords';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cambia las contraseñas';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $sites = \Hyn\Tenancy\Models\Website::all();
        $passwords = [];
        foreach($sites as $site){
            $contra =md5(sprintf(
                '%s.%d',
                \Config::get('app.key'),
                $site->id
            ));
            $temp = [
                'username'=>$site->uuid,
                'password'=>$contra,
                'query'=> "ALTER USER `".$site->uuid."`@`127.0.0.1` IDENTIFIED BY '$contra' ;",
            ];
            $passwords[] = $temp;
            $this->line($temp['query'] );
            try{
                \DB::update( $temp['query'] );

            }catch (\Illuminate\Database\QueryException $e){
                if("HY000"==$e->getCode()){
                    $temp['query'] = "CREATE USER `".$site->uuid."`@`127.0.0.1` IDENTIFIED BY '$contra';";
                    $this->line($temp['query'] );
                    \DB::update( $temp['query'] );

                }
            }
            $this->info("Se ha ejecutado");
        }
        $this->alert("Proceso terminado");
    }
}
