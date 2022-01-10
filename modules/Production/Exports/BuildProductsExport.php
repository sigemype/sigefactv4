<?php

    namespace Modules\Production\Exports;

    use Illuminate\Contracts\View\View;
    use Illuminate\Database\Eloquent\Collection;
    use Maatwebsite\Excel\Concerns\Exportable;
    use Maatwebsite\Excel\Concerns\FromView;
    use Maatwebsite\Excel\Concerns\ShouldAutoSize;

    /**
     *  bas LedgerAccountExcelExport
     * Class BuildProductsExport
     *
     * @package Modules\Production\Exports
     */
    class BuildProductsExport implements FromView, ShouldAutoSize
    {
        use Exportable;

        /** @var Collection */
        protected $collection;

        public function view(): View
        {
            $records = $this->getCollection();
            return view('production::production.partial.export',
                compact(
                    'records'
                ));

        }

        /**
         * @return Collection
         */
        public function getCollection(): Collection
        {
            return $this->collection;
        }

        /**
         * @param Collection $collection
         *
         * @return BuildProductsExport
         */
        public function setCollection(Collection $collection): BuildProductsExport
        {
            $this->collection = $collection;
            return $this;
        }


    }
