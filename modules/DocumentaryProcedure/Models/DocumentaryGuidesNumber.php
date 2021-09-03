<?php


    namespace Modules\DocumentaryProcedure\Models;


    use App\Models\Tenant\ModelTenant;
    use Carbon\Carbon;
    use Hyn\Tenancy\Traits\UsesTenantConnection;

    /**
     * Class DocumentaryGuidesNumber
     *
     * @property int                  $id
     * @property int|null             $doc_file_id
     * @property int|null             $doc_office_id
     * @property string|null          $guide
     * @property Carbon|null          $created_at
     * @property Carbon|null          $updated_at
     *
     * @property DocumentaryFile|null $doc_file
     *
     * @package App\Models
     */
    class DocumentaryGuidesNumber extends ModelTenant {
        use UsesTenantConnection;

        protected $table = 'documentary_guides_number';
        protected $perPage = 25;

        protected $casts = [
            'doc_file_id'   => 'int',
            'doc_office_id' => 'int',
        ];

        protected $fillable = [
            'doc_file_id',
            'doc_office_id',
            'guide',
            'origin',
        ];

        /**
         * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
         */
        public function doc_file() {
            return $this->belongsTo(DocumentaryFile::class, 'doc_file_id');
        }

        /**
         * @return string|null
         */
        public function getGuide()
        : ?string {
            return $this->guide;
        }

        /**
         * @param string|null $guide
         *
         * @return DocumentaryGuidesNumber
         */
        public function setGuide(?string $guide)
        : DocumentaryGuidesNumber {
            $this->guide = $guide;
            return $this;
        }

        /**
         * @return string|null
         */
        public function getOrigin()
        : ?string {
            return $this->origin;
        }

        /**
         * @param string|null $origin
         *
         * @return DocumentaryGuidesNumber
         */
        public function setOrigin(?string $origin)
        : DocumentaryGuidesNumber {
            $this->origin = $origin;
            return $this;
        }

    }
