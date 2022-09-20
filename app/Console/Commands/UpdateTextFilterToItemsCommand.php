<?php

namespace App\Console\Commands;

use App\Models\Tenant\Item;
use Illuminate\Console\Command;

class UpdateTextFilterToItemsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'update:text_filter_to_items';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Actualización';

    /**
     * Create a new command instance.
     *
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
        $this->info('Hora de inicio: ' . date('Y-m-d H:i:s'));
        $this->info('Inicializando Proceso');

        $items = Item::query()
            ->with('category', 'brand')
            ->get();
        foreach ($items as $item) {
            $item->save();
        }

        $this->info('Finalizando Proceso');
        $this->info('Hora de término: ' . date('Y-m-d H:i:s'));
    }
}
