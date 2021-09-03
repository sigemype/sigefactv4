<?php

namespace Modules\Report\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\FromCollection;

class ItemExport implements  FromView, ShouldAutoSize
{
    use Exportable;

    public function records($records) {
        $this->records = $records;

        return $this;
    }

    public function company($company) {
        $this->company = $company;

        return $this;
    }

    public function establishment($establishment) {
        $this->establishment = $establishment;

        return $this;
    }

    /**
     * @return string
     */
    public function getType()
    : string {
        return empty($this->type)?'':$this->type;
    }

    /**
     * @param string $type
     *
     * @return ItemExport
     */
    public function setType(string $type)
    : ItemExport {
        $this->type = $type;
        return $this;
    }


    public function view(): View {
        return view('report::items.report_excel', [
            'records'=> $this->records,
            'company' => $this->company,
            'type' => $this->getType(),
            'establishment'=>$this->establishment
        ]);
    }
}
