<?php

    namespace Modules\Report\Http\Resources;

    use App\CoreFacturalo\Helpers\Functions\FunctionsHelper;
    use App\Models\Tenant\Document;
    use App\Models\Tenant\DocumentItem;
    use App\Models\Tenant\SaleNote;
    use App\Models\Tenant\SaleNoteItem;
    use App\Models\Tenant\User;
    use Illuminate\Http\Request;
    use Illuminate\Http\Resources\Json\ResourceCollection;
    use Illuminate\Support\Collection;

    class ReportCommissionCollection extends ResourceCollection
    {
        /**
         * Transform the resource collection into an array.
         *
         * @param Request $request
         *
         * @return Collection
         */
        public function toArray($request)
        {
            /**
             * @var Collection $data
             */

            $data = $this->collection->transform(function ($row, $key) use ($request) {
                /**
                 * @var User                                                $row
                 * @var \Illuminate\Database\Eloquent\Collection|SaleNote[] $sale_notes
                 * @var \Illuminate\Database\Eloquent\Collection|Document[] $documents
                 */
                $requestInner = $request->all();
                $establishment_id = $requestInner['establishment_id'];

                $date_start = $requestInner['date_start'];
                $date_end = $requestInner['date_end'];
                FunctionsHelper::setDateInPeriod($requestInner, $date_start, $date_end);

                $documents = Document::
                whereIn('state_type_id', ['01', '03', '05', '07', '13'])
                    ->whereIn('document_type_id', ['01', '03', '08'])
                    ->whereBetween('date_of_issue', [$date_start, $date_end])
                    ->where('seller_id', $row->id)
                    ->WhereEstablishmentId($establishment_id)
                    ->get();
                $sale_notes = SaleNote::
                whereIn('state_type_id', ['01', '03', '05', '07', '13'])
                    ->whereBetween('date_of_issue', [$date_start, $date_end])
                    ->where('seller_id', $row->id)
                    ->WhereEstablishmentId($establishment_id)
                    ->get();


                $total_commision = 0;
                $total_commision_document = 0;
                $total_commision_sale_note = 0;
                $total_transactions_document = $documents->count();
                $total_transactions_sale_note = $sale_notes->count();
                $total_transactions = $total_transactions_document + $total_transactions_sale_note;
                $acum_sales_document = $documents->sum('total');
                $acum_sales_sale_note = $sale_notes->sum('total');
                $acum_sales = $acum_sales_document + $acum_sales_sale_note;

                foreach ($documents as $document) {
                    /**
                     * @var Document     $document
                     * @var DocumentItem $item
                     */
                    // $total_commision_document += $document->items->sum('relation_item.commission_amount');
                    foreach ($document->items as $item) {
                        if ($item->relation_item->commission_amount) {
                            if (!$item->relation_item->commission_type || $item->relation_item->commission_type == 'amount') {
                                $total_commision_document += $item->quantity * $item->relation_item->commission_amount;
                            } else {
                                $total_commision_document += $item->quantity * $item->unit_price * ($item->relation_item->commission_amount / 100);
                            }
                        }
                    }

                }

                foreach ($sale_notes as $sale_note) {
                    /**
                     * @var SaleNote     $sale_note
                     * @var SaleNoteItem $item
                     */
                    // $total_commision_sale_note += $sale_note->items->sum('relation_item.commission_amount');
                    foreach ($sale_note->items as $item) {
                        if ($item->relation_item->commission_amount) {
                            if (!$item->relation_item->commission_type || $item->relation_item->commission_type == 'amount') {
                                $total_commision_sale_note += $item->quantity * $item->relation_item->commission_amount;
                            } else {
                                $total_commision_sale_note += $item->quantity * $item->unit_price * ($item->relation_item->commission_amount / 100);
                            }
                            //     $total_commision_sale_note += ($item->quantity * $item->relation_item->commission_amount);
                        }
                    }
                }

                $total_commision = $total_commision_document + $total_commision_sale_note;

                return [
                    'id' => $row->id,
                    'user_name' => $row->name,
                    'acum_sales' => number_format($acum_sales, 2),
                    'acum_sales_document' => $acum_sales_document,
                    'acum_sales_sale_note' => $acum_sales_sale_note,
                    'total_commision' => number_format($total_commision, 2),
                    'total_commision_sale_note' => $total_commision_sale_note,
                    'total_commision_document' => $total_commision_document,
                    'total_transactions' => $total_transactions,
                    'total_transactions_document' => $total_transactions_document,
                    'total_transactions_sale_note' => $total_transactions_sale_note,
                ];
            });

            return $data;
        }

    }
