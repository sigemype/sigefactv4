<?php

namespace App\Traits;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Builder;


trait ElectronicDocumentTrait
{ 

    /**
     * 
     * Filtro para obtener el registro de actividades de los documentos electronicos individuales
     * 
     * Usado para:
     * Document
     * Dispatch
     * Perception
     * PurchaseSettlement
     * Retention
     *
     * @param  string $table
     * @param  Request $request
     * @return Builder
     */
    public function getQuerySystemActivityLogTransaction($table, $request)
    {
        $query = DB::connection('tenant')
                    ->table($table)
                    ->join('users', 'users.id', '=', "{$table}.user_id")
                    ->join('cat_document_types', 'cat_document_types.id', '=', "{$table}.document_type_id")
                    ->select(DB::raw(
                        "{$table}.id as id, ".
                        "users.id as user_id, ".
                        "users.name as user_name, ".
                        "{$table}.date_of_issue as date_of_issue, ".
                        "{$table}.time_of_issue as time_of_issue,".
                        "cat_document_types.id AS 'document_type_id',". 
                        "cat_document_types.description AS 'document_type_description',". 
                        "{$table}.series as series,".
                        "{$table}.number as number,".
                        "CONCAT({$table}.series, '-', {$table}.number) as number_full,".
                        "{$table}.created_at as created_at, ".
                        "{$table}.updated_at as updated_at "
                    ));

        if($request->value)
        {
            $query->where($request->column, 'like', "%{$request->value}%");
        }

        return $query;
    }

        
    /**
     * 
     * Filtro para obtener el registro de actividades de resumenes y anulaciones
     * 
     * Usado para:
     * Summary
     * Voided
     *
     * @param  string $table
     * @param  string $document_type_id
     * @param  Request $request
     * @param  bool $is_voided
     * @return Builder
     */
    public function getQuerySystemActivityLogTransactionGroup($table, $document_type_id, $request, $is_voided = false)
    {
        // resumen diario
        if($document_type_id === 'RC')
        {
            if($is_voided)
            {
                $query =  $this->getBaseQuerySummaryVoided($table, $document_type_id, $request, 'RESUMEN DIARIO')->whereIn("{$table}.summary_status_type_id", ['1', '2']);
            }
            // resumen de anulacion
            else
            {
                $query = $this->getBaseQuerySummaryVoided('summaries', $document_type_id, $request, 'ANULACIÓN')->where("summaries.summary_status_type_id", '3');
            }

            return $query;
        }

        // comunicaciones de baja
        return $this->getBaseQuerySummaryVoided($table, $document_type_id, $request, 'ANULACIÓN');
    }

    
    /**
     * 
     * Consulta base para resumenes y anulaciones
     *
     * @param  string $table
     * @param  string $document_type_id
     * @param  Request $request
     * @param  string $document_type_description
     * @return Builder
     */
    public function getBaseQuerySummaryVoided($table, $document_type_id, $request, $document_type_description)
    {
        $query = DB::connection('tenant')
                    ->table($table)
                    ->join('users', 'users.id', '=', "{$table}.user_id")
                    ->select(DB::raw(
                        "{$table}.id as id, ".
                        "users.id as user_id, ".
                        "users.name as user_name, ".
                        "{$table}.date_of_issue as date_of_issue, ".
                        "null as time_of_issue,".
                        "'{$document_type_id}' AS 'document_type_id',". 
                        "'{$document_type_description}' AS 'document_type_description',". 
                        "null as series,".
                        "null as number,".
                        "{$table}.identifier as number_full,".
                        "{$table}.created_at as created_at, ".
                        "{$table}.updated_at as updated_at "
                    ));

        if(in_array($request->column, ['date_of_issue']) && $request->value)
        {
            $query->where($request->column, 'like', "%{$request->value}%");
        }

        return $query;
    }


    /**
     * 
     * Filtro para obtener el registro de actividades de los documentos electronicos
     * 
     * Usado para:
     * Document
     * Dispatch
     * Perception
     * PurchaseSettlement
     * Retention
     *
     * @param Builder $query
     * @return Builder
     */  
    // public function scopeFiltersSystemActivityLogTransactions($query)
    // {
    //     return $query->whereFilterWithOutRelations()
    //                 ->select([
    //                     'id',
    //                     'user_id',
    //                     'date_of_issue',
    //                     'time_of_issue',
    //                     'document_type_id',
    //                     'series',
    //                     'number',
    //                     'created_at',
    //                     'updated_at',
    //                 ])
    //                 ->with([
    //                     'user' => function($q){
    //                         return $q->filterOnlyUsername();
    //                     },
    //                     'document_type' => function($q){
    //                         return $q->filterOnlyDescription();
    //                     },
    //                 ]);
    // }
    
}
