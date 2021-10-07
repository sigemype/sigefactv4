<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class TransfersExport implements FromView, ShouldAutoSize{
    use Exportable;

    public function records($records) {
        $this->records = $records;
        return $this;
    }

    // public function cant_item($value) {
    //     $this->cant_item = $value;
    //     return $this;
    // }

    public function view(): View {
        return view('tenant.reports.transfers.report_excel', [
            'records'=> $this->records,
            // 'cant_item'=> $this->cant_item,
        ]);
    }
}
