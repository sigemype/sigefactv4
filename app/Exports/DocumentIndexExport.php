<?php

namespace App\Exports;

use Carbon\Carbon;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class DocumentIndexExport implements FromView, ShouldAutoSize{
    use Exportable;
    public function records($records) {
        $this->records = $records;
        return $this;
    }

    public function view(): View {
        return view('tenant.documents.report', [
            'records'=> $this->records,
            'date_now'=> Carbon::now()
        ]);
    }
}
