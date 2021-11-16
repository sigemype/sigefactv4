<?php

    namespace Modules\Report\Http\Controllers;

    use App\Models\Tenant\Catalogs\DocumentType;
    use App\Http\Controllers\Controller;
    use Barryvdh\DomPDF\Facade as PDF;
    use Illuminate\Database\Eloquent\Builder;
    use Modules\Report\Exports\CommissionExport;
    use Illuminate\Http\Request;
    use App\Models\Tenant\Establishment;
    use App\Models\Tenant\SaleNote;
    use App\Models\Tenant\User;
    use App\Models\Tenant\Document;
    use App\Models\Tenant\Company;
    use Carbon\Carbon;
    use Modules\Report\Http\Resources\ReportCommissionCollection;

    class ReportCommissionController extends Controller
    {


        public function filter()
        {

            $document_types = [];

            $establishments = Establishment::all()->transform(function ($row) {
                return [
                    'id' => $row->id,
                    'name' => $row->description
                ];
            });
            $sellers = $this->getSellers();


            return compact('document_types', 'sellers', 'establishments');
        }


        public function index()
        {

            return view('report::commissions.index');
        }

        public function records(Request $request)
        {
            /** @var \Illuminate\Database\Eloquent\Builder  $records */
            $records = $this->getRecords($request->all(), User::class);

            return new ReportCommissionCollection($records->paginate(config('tenant.items_per_page')));
        }


        public function getRecords($request, $model)
        {

            $document_type_id = $request['document_type_id'];
            $establishment_id = $request['establishment_id'];
            $period = $request['period'];
            $date_start = $request['date_start'];
            $date_end = $request['date_end'];
            $month_start = $request['month_start'];
            $month_end = $request['month_end'];
            $seller_id = $request['seller_id'] ?? 0;

            $d_start = null;
            $d_end = null;
            /** @todo: Eliminar periodo, fechas y cambiar por

            $date_start = $request['date_start'];
            $date_end = $request['date_end'];
            \App\CoreFacturalo\Helpers\Functions\FunctionsHelper\FunctionsHelper::setDateInPeriod($request, $date_start, $date_end);
             */

            switch ($period) {
                case 'month':
                    $d_start = Carbon::parse($month_start . '-01')->format('Y-m-d');
                    $d_end = Carbon::parse($month_start . '-01')->endOfMonth()->format('Y-m-d');
                    break;
                case 'between_months':
                    $d_start = Carbon::parse($month_start . '-01')->format('Y-m-d');
                    $d_end = Carbon::parse($month_end . '-01')->endOfMonth()->format('Y-m-d');
                    break;
                case 'date':
                    $d_start = $date_start;
                    $d_end = $date_start;
                    break;
                case 'between_dates':
                    $d_start = $date_start;
                    $d_end = $date_end;
                    break;
            }

            $records = $this->data($document_type_id, $establishment_id, $d_start, $d_end, $model, $seller_id);

            return $records;

        }


        /**
         * @param     $document_type_id
         * @param     $establishment_id
         * @param     $date_start
         * @param     $date_end
         * @param     $model
         * @param int $seller_id
         *
         * @return Builder
         */
        private function data($document_type_id, $establishment_id, $date_start, $date_end, $model, $seller_id = 0)
        {

            /** @var Builder $data */
            $data = $model::with(['documents' => function ($q) use ($date_start, $date_end, $seller_id) {
                $q->whereIn('state_type_id', ['01', '03', '05', '07', '13'])
                    ->whereIn('document_type_id', ['01', '03', '08'])
                    ->whereBetween('date_of_issue', [$date_start, $date_end]);
                if($seller_id != 0){
                    // @todo #1081
                    $q->where('user_id', $seller_id);
                    // $q->where('seller_id', $seller_id);
                }
            }, 'sale_notes' => function ($z) use ($date_start, $date_end, $seller_id) {
                $z->whereIn('state_type_id', ['01', '03', '05', '07', '13'])
                    ->whereBetween('date_of_issue', [$date_start, $date_end]);
                if($seller_id != 0) {
                    // @todo #1081
                    $z->where('user_id', $seller_id);
                    // $z->where('seller_id', $seller_id);
                }
                }]);
            if ($establishment_id) {
                $data = $data->where('establishment_id', $establishment_id);
            }
            if($model == (User::class) && $seller_id != 0){
                $data->where('id',$seller_id);
            }

            return $data->latest()->whereTypeUser();

        }


        public function pdf(Request $request)
        {

            $company = Company::first();
            $establishment = ($request->establishment_id) ? Establishment::findOrFail($request->establishment_id) : auth()->user()->establishment;
            $records = $this->getRecords($request->all(), User::class)->get();

            $pdf = PDF::loadView('report::commissions.report_pdf', compact("records", "company", "establishment"));

            $filename = 'Reporte_Comision_Vendedor_' . date('YmdHis');

            return $pdf->download($filename . '.pdf');
        }


        public function excel(Request $request)
        {

            $company = Company::first();
            $establishment = ($request->establishment_id) ? Establishment::findOrFail($request->establishment_id) : auth()->user()->establishment;

            $records = $this->getRecords($request->all(), User::class)->get();

            return (new CommissionExport())
                ->records($records)
                ->company($company)
                ->establishment($establishment)
                ->download('Reporte_Comision_Vendedor' . Carbon::now() . '.xlsx');

        }
    }
