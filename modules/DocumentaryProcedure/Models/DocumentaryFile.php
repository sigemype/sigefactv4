<?php

    namespace Modules\DocumentaryProcedure\Models;

    use App\Models\Tenant\ModelTenant;
    use App\Models\Tenant\Person;
    use Hyn\Tenancy\Traits\UsesTenantConnection;
    use Illuminate\Database\Eloquent\Builder;

    /**
     * Modules\DocumentaryProcedure\Models\DocumentaryFile
     *
     * @property-read mixed                                                                                             $active
     * @property mixed                                                                                                  $sender
     * @property-read \Illuminate\Database\Eloquent\Collection|\Modules\DocumentaryProcedure\Models\DocumentaryOffice[] $offices
     * @property-read int|null                                                                                          $offices_count
     * @method static Builder|DocumentaryFile newModelQuery()
     * @method static Builder|DocumentaryFile newQuery()
     * @method static Builder|DocumentaryFile query()
     * @mixin \Eloquent
     */
    class DocumentaryFile extends ModelTenant {
        use UsesTenantConnection;

        protected $table = 'documentary_files';

        protected $fillable = [
            'documentary_document_id',
            'documentary_process_id',
            'documentary_office_id',
            'number',
            'year',
            'invoice',
            'date_register',
            'time_register',
            'person_id',
            'sender',
            'subject',
            'attached_file',
            'requirements',
        ];

        /**
         * @return int
         */
        public function getDocumentaryOfficeId() {
            return $this->documentary_office_id;
        }

        /**
         * @param int $documentary_office_id
         *
         * @return DocumentaryFile
         */
        public function setDocumentaryOfficeId($documentary_office_id)
        : DocumentaryFile {
            $this->documentary_office_id = $documentary_office_id;
            return $this;
        }

        public function getActiveAttribute($value) {
            return $value ? true : false;
        }

        public function getSenderAttribute($value) {
            return (is_null($value)) ? null : (object)json_decode($value);
        }

        public function setSenderAttribute($value) {
            $this->attributes['sender'] = (is_null($value)) ? null : json_encode($value);
            return $this;
        }

        /**
         * @return \Illuminate\Database\Eloquent\Relations\HasMany
         */
        public function offices() {
            return $this->hasMany(DocumentaryFileOffice::class, 'documentary_file_id');
        }

        /**
         * @return array
         */
        public function getCollectionData($holiday = []) {
            $data = $this->toArray();
            $person = Person::find($this->person_id);
            $documentary_process = DocumentaryProcess::find($this->documentary_process_id);
            $documentary_file_archives = DocumentaryFilesArchives::where('documentary_file_id', $this->id)->get();
            $documentary_file_office = DocumentaryOffice::find($this->documentary_office_id);
            if (empty($documentary_process)) $documentary_process = new DocumentaryProcess();
            if (empty($documentary_file_office)) $documentary_file_office = new DocumentaryOffice();

            $data['guides'] = DocumentaryGuidesNumber::where('doc_file_id', $this->id)->get();
            $data['documentary_process'] = $documentary_process->getCollectionData($holiday);
            $data['documentary_process_id'] = (int)$this->documentary_process_id;
            $data['documentary_office'] = $documentary_file_office->getCollectionData();
            $data['documentary_file_archives'] = $documentary_file_archives->transform(function ($row) {
                /** @var DocumentaryFilesArchives $row */
                return $row->getCollectionData();
            });
            $lastComplete = [];
            $data['observations'] = DocumentaryObservation::where('doc_file_id', $this->id)->get()
                                                          ->transform(function ($row) {
                                                              /** @var DocumentaryObservation $row */
                                                              return $row->getCollectionData();
                                                          });
            $nextStep = $this->documentary_office_id;
            $data['documentary_file_offices'] =
                DocumentaryFileOffice::where('documentary_file_id', $this->id)
                                     ->get()
                                     ->transform(function ($row) use (&$lastComplete, $nextStep) {
                                         /** @var DocumentaryFileOffice $row */
                                         $data = $row->getCollectionData();

                                         if (count($lastComplete) == 0) {
                                             // se guarda el primer proceso
                                             $lastComplete = $data;
                                         }
                                         if($row->documentary_office_id == $nextStep){
                                             $lastComplete = $data;
                                         }

                                         return $data;
                                     });
            $data['person'] = $person->getCollectionData();
            $data['last_complete'] = $lastComplete;
            $requirement_array = [];
            foreach ($this->getRequirements() as $requirement) {
                /* para el-checkbox se requiere el id del elemento => true para estar seleccionado*/
                $requirement_array[$requirement] = true;
            }
            $data['requirements_id'] = $requirement_array;
            return $data;

        }

        /**
         * @return false|int[]
         */
        public function getRequirements()
        : ?array {

            return self::makeArray($this->requirements);
        }

        /**
         * @param string|null $text
         *
         * @return false|int[]
         */
        protected static function makeArray(?string $text = '') {
            return array_map('intval', explode(',', $text));

        }

        /**
         * @return \Illuminate\Database\Eloquent\Collection|\Modules\DocumentaryProcedure\Models\DocumentaryOffice[]
         */
        public function getOffices() {
            return $this->offices;
        }

        /**
         * @param \Illuminate\Database\Eloquent\Collection|\Modules\DocumentaryProcedure\Models\DocumentaryOffice[] $offices
         *
         * @return DocumentaryFile
         */
        public function setOffices($offices) {
            $this->offices = $offices;
            return $this;
        }

        /**
         * @return int|null
         */
        public function getOfficesCount()
        : ?int {
            return $this->offices_count;
        }

        /**
         * @param int|null $offices_count
         *
         * @return DocumentaryFile
         */
        public function setOfficesCount(?int $offices_count)
        : DocumentaryFile {
            $this->offices_count = $offices_count;
            return $this;
        }

        /**
         * @return mixed
         */
        public function getDocumentaryDocumentId() {
            return $this->documentary_document_id;
        }

        /**
         * @param mixed $documentary_document_id
         *
         * @return DocumentaryFile
         */
        public function setDocumentaryDocumentId($documentary_document_id) {
            $this->documentary_document_id = $documentary_document_id;
            return $this;
        }

        /**
         * @return mixed
         */
        public function getDocumentaryProcessId() {
            return $this->documentary_process_id;
        }

        /**
         * @param mixed $documentary_process_id
         *
         * @return DocumentaryFile
         */
        public function setDocumentaryProcessId($documentary_process_id) {
            $this->documentary_process_id = $documentary_process_id;
            return $this;
        }

        /**
         * @return mixed
         */
        public function getNumber() {
            return $this->number;
        }

        /**
         * @param mixed $number
         *
         * @return DocumentaryFile
         */
        public function setNumber($number) {
            $this->number = $number;
            return $this;
        }

        /**
         * @return mixed
         */
        public function getYear() {
            return $this->year;
        }

        /**
         * @param mixed $year
         *
         * @return DocumentaryFile
         */
        public function setYear($year) {
            $this->year = $year;
            return $this;
        }

        /**
         * @return mixed
         */
        public function getInvoice() {
            return $this->invoice;
        }

        /**
         * @param mixed $invoice
         *
         * @return DocumentaryFile
         */
        public function setInvoice($invoice) {
            $this->invoice = $invoice;
            return $this;
        }

        /**
         * @return mixed
         */
        public function getDateRegister() {
            return $this->date_register;
        }

        /**
         * @param mixed $date_register
         *
         * @return DocumentaryFile
         */
        public function setDateRegister($date_register) {
            $this->date_register = $date_register;
            return $this;
        }

        /**
         * @return mixed
         */
        public function getTimeRegister() {
            return $this->time_register;
        }

        /**
         * @param mixed $time_register
         *
         * @return DocumentaryFile
         */
        public function setTimeRegister($time_register) {
            $this->time_register = $time_register;
            return $this;
        }

        /**
         * @return mixed
         */
        public function getPersonId() {
            return $this->person_id;
        }

        /**
         * @param mixed $person_id
         *
         * @return DocumentaryFile
         */
        public function setPersonId($person_id) {
            $this->person_id = $person_id;
            return $this;
        }

        /**
         * @return mixed
         */
        public function getSubject() {
            return $this->subject;
        }

        /**
         * @param mixed $subject
         *
         * @return DocumentaryFile
         */
        public function setSubject($subject) {
            $this->subject = $subject;
            return $this;
        }

        /**
         * @return mixed
         */
        public function getAttachedFile() {
            return $this->attached_file;
        }

        /**
         * @param mixed $attached_file
         *
         * @return DocumentaryFile
         */
        public function setAttachedFile($attached_file) {
            $this->attached_file = $attached_file;
            return $this;
        }

        /**
         * @return mixed
         */
        public function getObservation() {
            return $this->observation;
        }

        /**
         * @param mixed $observation
         *
         * @return DocumentaryFile
         */
        public function setObservation($observation) {
            if(!empty($observation)){
                $this->observation = $observation;
            }
            return $this;
        }

        /**
         * @param array|null $requirements
         *
         * @return DocumentaryFile
         */
        public function setRequirements(?array $requirements = [])
        : DocumentaryFile {
            $this->requirements = self::splitArray($requirements);

            return $this;
        }

        /**
         * @param array|null $array
         *
         * @return string
         */
        protected static function splitArray(?array $array = []) {
            return implode(',', $array);

        }
    }
