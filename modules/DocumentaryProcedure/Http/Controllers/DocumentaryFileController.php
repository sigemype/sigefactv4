<?php

    namespace Modules\DocumentaryProcedure\Http\Controllers;

    use App\Models\Tenant\Company;
    use App\Models\Tenant\Person;
    use Barryvdh\DomPDF\Facade as PDF;
    use Carbon\Carbon;
    use Illuminate\Http\Request;
    use Illuminate\Http\UploadedFile;
    use Illuminate\Routing\Controller;
    use Modules\DocumentaryProcedure\Exports\TramiteExport;
    use Modules\DocumentaryProcedure\Models\DocumentaryAction;
    use Modules\DocumentaryProcedure\Models\DocumentaryDocument;
    use Modules\DocumentaryProcedure\Models\DocumentaryFile as Expediente;
    use Modules\DocumentaryProcedure\Models\DocumentaryFileOffice as FileRelStage;
    use Modules\DocumentaryProcedure\Models\DocumentaryFilesArchives as FilesFolder;
    use Modules\DocumentaryProcedure\Models\DocumentaryGuidesNumber as Guides;
    use Modules\DocumentaryProcedure\Models\DocumentaryObservation as Observation;
    use Modules\DocumentaryProcedure\Models\DocumentaryOffice as Stage;
    use Modules\DocumentaryProcedure\Models\DocumentaryProcess as Tramite;
    use Modules\DocumentaryProcedure\Models\RelUserToDocumentaryOffices as UserRelStages;
    use Throwable;

    /**
     * Class DocumentaryFileController
     *
     * @package Modules\DocumentaryProcedure\Http\Controllers
     */
    class DocumentaryFileController extends Controller {

        protected $holidays;

        /**
         * DocumentaryFileController constructor.
         *
         * @param $holidays
         */
        public function __construct() {
            $year = Carbon::now()->format('Y');

            // Dias feriados
            $this->holidays = [
                '01-01-'.$year,
                '01-04-'.$year,
                '02-04-'.$year,
                '01-05-'.$year,
                '02-06-'.$year,
                '28-07-'.$year,
                '29-07-'.$year,
                '30-08-'.$year,
                '08-10-'.$year,
                '01-11-'.$year,
                '08-12-'.$year,
                '25-12-'.$year,
            ];
        }

        /**
         * Return Collection to json
         *
         * @param \Illuminate\Http\Request $request
         *
         * @return mixed
         */
        public function getData(Request $request) {

            $holiday = $this->holidays;
            $record = $this
                ->getRecords($request)
                ->get()
                ->transform(function ($row) use($holiday){
                    /** @var Expediente $row */
                    return $row->getCollectionData($holiday);
                });
            return json_decode($record);
        }

        /**
         * @param \Illuminate\Http\Request $request
         * @param int                      $id
         *
         * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder|\Modules\DocumentaryProcedure\Models\Expediente
         */
        public function getRecords(Request $request, $id = 0) {
            $files = Expediente::with('offices')
                               ->orderBy('id', 'DESC');
            $dateStart = ($request->has('date_start')) ? $request->date_start : Carbon::now()->format('Y-m-d');
            $dateEnd = ($request->has('date_end')) ? $request->date_end : Carbon::now()->format('Y-m-d');

            $files->whereBetween('date_register', [$dateStart, $dateEnd]);
            $userType = auth()->user()->type;
            if ($userType !== 'admin') {
                $etapas = UserRelStages::where([
                                                   'user_id' => auth()->user()->id,
                                               ])->get()->pluck('documentary_office_id');

                $files->wherein('documentary_office_id', $etapas);
            }

            if ($request->has('subject')) {
                $files->where('subject', 'like', "%".$request->subject."%");
            }
            if ($request->has('invoice')) {
                $files->where('invoice', 'like', "%".$request->invoice."%");
            }
            if ($request->has('documentary_office_id') && !empty($request->documentary_office_id)) {
                $files->where('documentary_office_id', $request->documentary_office_id);

                /*
                $files->whereHas('offices', function ($query) use ($request) {
                    $query->where('documentary_office_id', $request->documentary_office_id);
                });
                */
            }
            if ($id != 0) {
                $files->where('id', $id);
            }

            return $files;
        }

        /**
         * @param \Illuminate\Http\Request $request
         *
         * @return \Illuminate\Contracts\View\Factory|\Illuminate\Foundation\Application|\Illuminate\Http\JsonResponse|\Illuminate\View\View
         */
        public function index(Request $request) {
            $holiday = $this->holidays;
            $files = $this
                ->getRecords($request)
                ->get()
                ->transform(function ($row) use($holiday) {
                    /** @var Expediente $row */
                    return $row->getCollectionData($holiday);
                });
            if (request()->ajax()) {
                return response()->json(['data' => $files], 200);
            }
            $holidays = $this->holidays;
            $processes = Tramite::orderBy('name')
                                ->whereActive(true)
                                ->get()
                                ->transform(function ($row) use ($holidays) {
                                    /** @var Tramite $row */
                                    return $row->getCollectionData($holidays);
                                });
            $actions = DocumentaryAction::orderBy('name')
                                        ->whereActive(true)
                                        ->get()
                                        ->transform(function ($row) {
                                            /** @var DocumentaryAction $row */
                                            return $row->getCollectionData();
                                        });
            $customers = Person::with('addresses')
                               ->whereIsEnabled()
                               ->orderBy('name')
                               ->take(20)
                               ->get()
                               ->transform(function ($row) {
                                   /** @var Person $row */
                                   return $row->getCollectionData();
                               });

            $offices = Stage::orderBy('id')
                            ->whereActive(true)
                            ->get()
                            ->transform(function ($row) {
                                /** @var Stage $row */
                                return $row->getCollectionData();
                            });


            $documentTypes = DocumentaryDocument::orderBy('name')
                                                ->whereActive(true)
                                                ->get()
                                                ->transform(function ($row) {
                                                    return $row->getCollectionData();
                                                });
            return view('documentaryprocedure::files',
                        compact(
                            'files',
                            'processes',
                            'documentTypes',
                            'actions',
                            'customers',
                            'offices'));
        }

        /**
         * Save Expediente
         *
         * @param \Illuminate\Http\Request $request
         *
         * @return \Illuminate\Http\JsonResponse
         */
        public function store(Request $request) {

            try {
                $file = new Expediente();
                $file = $this->saveExpediente($request, $file);
                return response()->json([
                                            'data'    => $file,
                                            'message' => 'Expediente guardada de forma correcta.',
                                            'succes'  => true,
                                        ], 200);
            } catch (Throwable $th) {

                return response()->json([
                                            'message' => 'Ocurrió un error al procesar su petición. Detalles: '.$th->getMessage(),
                                            'succes'  => false,
                                        ], 500);
            }
        }


        /**
         * @param \Illuminate\Http\Request                             $request
         * @param \Modules\DocumentaryProcedure\Models\DocumentaryFile $file
         *
         * @return \Modules\DocumentaryProcedure\Models\DocumentaryFile
         */
        public function saveExpediente(Request $request, Expediente $file) {
            $person_id = $request->person_id;
            $sender = json_decode($request->person);
            $request->merge(['sender' => $sender]);
            if ($request->hasFile('attachFile') && $request->file('attachFile')->isValid()) {
                $request->merge(['attached_file' => $this->storeFile($request->file('attachFilefile'))]);
            }
            $file
                // ->setDocumentaryOfficeId($request->documentary_process_id)
                ->setDocumentaryProcessId($request->documentary_process_id)
                ->setYear($request->year)
                ->setInvoice($request->invoice)
                ->setDateRegister($request->date_register)
                ->setTimeRegister($request->time_register)
                ->setSenderAttribute($sender)
                ->setDocumentaryDocumentId(0)
                ->setPersonId($person_id);

            //$file = new Expediente($request->all());
            // $file->load('offices');
            if ($request->has('requirements_id')) {
                $requirements = [];
                $requirements_id = (json_decode($request->requirements_id));
                foreach ($requirements_id as $requirement) {
                    $requirement = (int)$requirement;
                    if ($requirement != 0) {
                        $requirements[] = $requirement;
                    }
                }
                $file->setRequirements($requirements);
            }

            $file->push();
            $file_id = $file->id;

            $holidays = $this->holidays;
            $tramite = Tramite::find($request->documentary_process_id);

            $col = $tramite->getCollectionData($holidays);
            $etapas = $col['full_stages'];
            /** @var \Illuminate\Database\Eloquent\Collection $relacion_etapas */
            $relacion_etapas = FileRelStage::where('documentary_file_id', $file_id)->get();

            /** @var array $stage */
            if ($relacion_etapas->count() == 0) {
                foreach ($etapas as $stage) {
                    $data = [
                        'documentary_file_id'    => $file->id,
                        'documentary_office_id'  => $stage['id'],
                        'documentary_action_id'  => 0,
                        'observation'            => 0,
                        // 'status',
                        'office_name'            => $stage['name'],
                        'process_name'           => $col['name'],
                        'documentary_process_id' => $request->documentary_process_id,
                        'complete'               => 0,
                        'start_date'             => $stage['start_date'],
                        'end_date'               => $stage['end_date'],
                        'days'                   => $stage['days'],
                    ];
                    if ($file->documentary_office_id == 0) {
                        $file->setDocumentaryOfficeId($stage['id'])->push();
                    }
                    $eta = new FileRelStage($data);
                    $eta->push();
                }
            }

            if ($request->has('attachments')) {
                $file_documentary_office_id = (int)$file->documentary_office_id;
                foreach ($request->attachments as $attachment) {
                    /** @var \Illuminate\Http\UploadedFile $attachment */
                    $data = [
                        'user_id'               => auth()->user()->id,
                        'documentary_file_id'   => $file_id,
                        'documentary_office_id' => $file_documentary_office_id,
                        'observation'           => '',
                        'attached_file'         => FilesFolder::saveFile($attachment),
                    ];
                    $files = new FilesFolder($data);
                    $files->push();

                }
            }
            return $file;
        }

        /**
         * @param \Illuminate\Http\UploadedFile $file
         *
         * @return string
         */
        private function storeFile(UploadedFile $file)
        : string {
            $ext = $file->getClientOriginalExtension();
            $filenameOriginal = str_replace('.'.$ext, '', $file->getClientOriginalName());
            $name = $filenameOriginal.'-'.time().'.'.$ext;
            $path = 'storage/uploads/files/';
            $fullpath = $path.$name;
            $file->storeAs('public/uploads/files', $name);

            return $fullpath;
        }

        /**
         * @param \Illuminate\Http\Request $request
         *
         * @return \Illuminate\Http\JsonResponse
         */
        public function nextStep(Request $request) {
            $guides = json_decode($request->guides);
            $documentary_action_id = (int)$request->documentary_action_id;
            $documentary_office_id = (int)$request->documentary_office_id;
            $next = null;
            $hadObservation = ($request->hadObservation == 'true') ? true : false;
            $observation = $request->observation;
            if (empty($observation)) $observation = null;
            $office = Expediente::find($request->id);
            $currentStage = FileRelStage::where([
                                                    'documentary_file_id'   => $office->id,
                                                    'documentary_office_id' => $office->documentary_office_id,
                                                ])->first();
            $current_office = (int)$office->documentary_office_id;
            $next = FileRelStage::where('documentary_file_id', $office->id)
                                ->where('documentary_office_id', '>', $office->documentary_office_id)
                                ->first();
            if ($current_office <= $documentary_office_id) {
                if ($hadObservation === true) {
                    // No debe avanzar por tener observacion
                    $documentary_office_id = $current_office;
                } elseif ($current_office == $documentary_office_id) {
                    if (!empty($next)) {
                        /*Se toma el siguiente id */
                        $documentary_office_id = $next->documentary_office_id;
                    }
                }
            }
            // Se genera la observacion si existe
            if ($hadObservation == true) {
                $ob = new Observation();
                $ob->setDocFileId($office->id)->setObservation($observation)->push();
                $ob->push();
            }
            // Si tiene numeros de referencia, se almacenan
            if (!empty($guides)) {
                foreach ($guides as $guide) {
                    $guide = (array)$guide;
                    $guide['doc_file_id'] = $guide['doc_file_id'] ?? $office->id;
                    $guide['doc_office_id'] = $guide['doc_office_id'] ?? $office->documentary_office_id;
                    $guide_record = Guides::firstOrCreate($guide);
                    $guide_record->push();
                }
            }

            if ($hadObservation == false) {
                $office
                    ->setDocumentaryOfficeId($documentary_office_id)
                    ->setObservation($observation);
                $office->push();
            }
            // Si no tiene observacion y si existe la etapa actual, se guarda
            $nextstages = null;
            if ($hadObservation == false && !empty($currentStage)) {
                if ($currentStage->getDocumentaryOfficeId() <= $documentary_office_id) {
                    $nextstages = FileRelStage::where([
                                                          'documentary_file_id' => $office->id,
                                                          //'documentary_office_id' => $office->documentary_office_id,
                                                      ])->where('documentary_office_id', '<=', $documentary_office_id)
                                              ->get();
                    foreach ($nextstages as $st) {
                        $st->setComplete(1)->push();
                    }
                }
            }


            $files = $this->getRecords($request)
                          ->get()
                          ->transform(function ($row) {
                              /** @var Expediente $row */
                              return $row->getCollectionData();
                          });

            // Si existen archivos, se guardan
            if ($request->has('file')) {
                foreach ($request->file as $file) {
                    /** @var \Illuminate\Http\UploadedFile $file */
                    $data = [
                        'user_id'               => auth()->user()->id,
                        'documentary_file_id'   => $request->id,
                        'documentary_office_id' => $current_office,
                        'observation'           => $observation,
                        'attached_file'         => $this->storeFile($file),
                    ];
                    $newFile = new FilesFolder($data);
                    $newFile->push();
                }

            }
            return response()->json([
                                        'data'           => $office,
                                        'nextstages'     => $nextstages,
                                        'files'          => $files,
                                        'current_office' => $current_office,
                                        'next_office'    => $office->documentary_office_id,
                                        'message'        => 'Expediente guardado de forma correcta.',
                                        'succes'         => true,
                                    ], 200);
        }

        /**
         * @param \Illuminate\Http\Request $request
         *
         * @return \Illuminate\Http\JsonResponse
         */
        public function backStep(Request $request) {

            $guides = json_decode($request->guides);
            $next = null;
            $documentary_action_id = (int)$request->documentary_action_id;
            $documentary_office_id = (int)$request->documentary_office_id;
            $observation = $request->observation;
            $hadObservation = ($request->hadObservation == 'true') ? true : false;
            if (empty($observation)) $observation = null;

            $office = Expediente::find($request->id);
            $currentStage = FileRelStage::where([
                                                    'documentary_file_id'   => $office->id,
                                                    'documentary_office_id' => $office->documentary_office_id,
                                                ])->first();
            $current_office = $office->documentary_office_id;

            if (($current_office > $documentary_office_id) && $hadObservation === true) {
                // No debe avanzar por tener observacion
                $documentary_office_id = $current_office;
            } else {
                if ($current_office === $documentary_office_id) {
                    $next = FileRelStage::where('documentary_file_id', $office->id)
                                        ->where('documentary_office_id', '<', $office->documentary_office_id)
                                        ->first();
                    if (!empty($next)) {
                        $documentary_office_id = $next->documentary_office_id;
                    }
                }
            }

            if ($hadObservation == true) {
                $ob = new Observation();
                $ob->setDocFileId($office->id)->setObservation($observation)->push();
                $ob->push();
            }

            if (!empty($guides)) {
                foreach ($guides as $guide) {
                    $guide = (array)$guide;
                    $guide['doc_file_id'] = $guide['doc_file_id'] ?? $office->id;
                    $guide['doc_office_id'] = $guide['doc_office_id'] ?? $office->documentary_office_id;
                    $guide_record = Guides::firstOrCreate($guide);
                    $guide_record->push();
                }
            }

            if ($hadObservation == false) {

                $office
                    ->setDocumentaryOfficeId($documentary_office_id)
                    ->setObservation($observation);
                $office->push();
            }
            if ($hadObservation == false && !empty($currentStage)) {
                if ($currentStage->getDocumentaryOfficeId() <= $documentary_office_id) {
                    $nextstages = FileRelStage::where([
                                                          'documentary_file_id' => $office->id,
                                                          //'documentary_office_id' => $office->documentary_office_id,
                                                      ])->where('documentary_office_id', '>=', $documentary_office_id)
                                              ->get();
                    foreach ($nextstages as $st) {
                        $st->setComplete(0)->push();
                    }
                }
            }

            $files = $this->getRecords($request)
                          ->get()
                          ->transform(function ($row) {
                              /** @var Expediente $row */
                              return $row->getCollectionData();
                          });
            if ($request->has('file')) {
                foreach ($request->file as $file) {
                    /** @var \Illuminate\Http\UploadedFile $file */
                    $data = [
                        'user_id'               => auth()->user()->id,
                        'documentary_file_id'   => $request->id,
                        'documentary_office_id' => $current_office,
                        'observation'           => $observation,
                        'attached_file'         => $this->storeFile($file),
                    ];
                    $newFile = new FilesFolder($data);
                    $newFile->push();
                }

            }

            return response()->json([
                                        'data'           => $office,
                                        'files'          => $files,
                                        'message'        => 'Expediente guardado de forma correcta.',
                                        'back'           => $next,
                                        'current_office' => $current_office,
                                        'succes'         => true,
                                    ], 200);
        }

        /**
         * @param \Illuminate\Http\Request $request
         * @param                          $fileId
         *
         * @return \Illuminate\Http\JsonResponse
         * @deprecated  No se utiliza
         */
        public function addOffice(Request $request, $fileId) {
            request()->validate([
                                    'documentary_office_id' => 'required|numeric',
                                    'documentary_action_id' => 'required|numeric',
                                    'observation'           => 'max:300',
                                ]);

            $file = Expediente::findOrFail($fileId);

            $files = $this->getRecords($request)
                          ->get()
                          ->transform(function ($row) {
                              /** @var Expediente $row */
                              return $row->getCollectionData();
                          });

            return response()->json([
                                        // 'data'    => $office,
                                        'files'   => $files,
                                        'message' => 'Expediente guardada de forma correcta.',
                                        'succes'  => true,
                                    ], 200);
        }

        /**
         * @param \Illuminate\Http\Request $request
         * @param                          $id
         *
         * @return \Illuminate\Http\JsonResponse
         */
        public function update(Request $request, $id) {
            $file = Expediente::findOrFail($id);
            $file = $this->saveExpediente($request, $file);
            return response()->json([
                                        'data'    => $file,
                                        'message' => 'Expediente actualizada de forma correcta.',
                                        'succes'  => true,
                                    ], 200);
        }

        /**
         * @param $id
         *
         * @return \Illuminate\Http\JsonResponse
         */
        public function destroy($id) {
            try {
                $file = Expediente::findOrFail($id);
                $file->delete();

                return response()->json([
                                            'data'    => null,
                                            'message' => 'Expediente eliminada de forma correcta.',
                                            'succes'  => true,
                                        ], 200);
            } catch (Throwable $th) {
                return response()->json([
                                            'success' => false,
                                            'data'    => 'Ocurrió un error al procesar su petición. Detalles: '.$th->getMessage(),
                                        ], 500);
            }
        }

        /**
         * @return \Illuminate\Http\JsonResponse
         */
        public function tables() {
            $documentTypes = DocumentaryDocument::orderBy('name')
                                                ->whereActive(true)
                                                ->get()
                                                ->transform(function ($row) {
                                                    return $row->getCollectionData();
                                                });

            $holiday = $this->holidays;
            $processes = Tramite::orderBy('name')
                                ->whereActive(true)
                                ->get()
                                ->transform(function ($row)use($holiday) {
                                    return $row->getCollectionData($holiday);
                                });

            $actions = DocumentaryAction::orderBy('name')
                                        ->whereActive(true)
                                        ->get()
                                        ->transform(function ($row) {
                                            return $row->getCollectionData();
                                        });

            $customers = Person::with('addresses')
                               ->whereIsEnabled()
                               ->orderBy('name')
                               ->take(20)
                               ->get()
                               ->transform(function ($row) {
                                   return $row->getCollectionData();
                               });

            $offices = Stage::orderBy('id')
                            ->whereActive(true)
                            ->get()
                            ->transform(function ($row) {
                                /** @var Stage $row */
                                return $row->getCollectionData();
                            });

            return response()->json([
                                        'success' => true,
                                        'message' => 'Información procesada de forma correcta',
                                        'data'    => [
                                            'document_types' => $documentTypes,
                                            'processes'      => $processes,
                                            'customers'      => $customers,
                                            'offices'        => $offices,
                                            'actions'        => $actions,
                                        ],
                                    ], 200);
        }

        /**
         * @return \Illuminate\Http\JsonResponse
         */
        public function create() {
            $lastId = Expediente::count();

            return response()->json([
                                        'success' => true,
                                        'message' => 'Información procesada de forma correcta',
                                        'data'    => [
                                            'next_id'      => $lastId + 1,
                                            'current_year' => date('Y'),
                                        ],
                                    ], 200);
        }

        /**
         * @return \Illuminate\Http\JsonResponse
         */
        public function getDocumentNumber() {
            request()->validate([
                                    'document_id' => 'required|numeric',
                                ]);

            $countForDocumentType = Expediente::where('documentary_document_id', request('document_id'))
                                              ->count();

            return response()->json([
                                        'success' => true,
                                        'message' => 'Información procesada de forma correcta',
                                        'data'    => [
                                            'number' => $countForDocumentType + 1,
                                        ],
                                    ], 200);
        }

        public function excel(Request $request) {
            $record = $this->getRecords($request)->get()
                           ->transform(function ($row) {
                               /** @var Expediente $row */
                               return $row->getCollectionData();
                           });

            $excel = new TramiteExport();
            $company = Company::first();
            $establishment = auth()->user()->establishment;
            $excel->setRecords($record)->setCompany($company)->setEstablishment($establishment);
            // return $excel->view();
            return $excel->download('Reporte_Tramites_'.Carbon::now().'.xlsx');

        }


        public function pdf(Request $request) {

            $company = Company::first();
            $establishment = auth()->user()->establishment;
            $records = $this->getRecords($request)->get()
                            ->transform(function ($row) {
                                /** @var Expediente $row */
                                return $row->getCollectionData();
                            });

            return view('documentaryprocedure::exports.report_excel',
                        compact('records', 'company', 'establishment'));

            $pdf = PDF::loadView('documentaryprocedure::exports.report_excel',
                                 compact('records', 'company', 'establishment'));


            $filename = 'Reporte_Tramie_'.date('YmdHis');

            return $pdf->download($filename.'.pdf');
        }


    }
