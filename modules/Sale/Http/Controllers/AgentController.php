<?php

namespace Modules\Sale\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Modules\Sale\Http\Resources\AgentCollection;
use Modules\Sale\Http\Resources\AgentResource;
use Illuminate\Support\Facades\DB;
use Modules\Sale\Http\Requests\AgentRequest;
use Exception;
use Modules\Sale\Models\Agent;
use App\Models\Tenant\User;

class AgentController extends Controller
{

    public function index()
    {
        return view('sale::agents.index');
    }

 
    public function columns()
    {
        return [
            'name' => 'Nombre',
            'internal_id' => 'Código',
        ];
    }
 

    public function records(Request $request)
    {
        $records = Agent::where($request->column, 'like', "%{$request->value}%");

        return new AgentCollection($records->paginate(config('tenant.items_per_page')));
    }


    public function record($id)
    {
        return new AgentResource(Agent::findOrFail($id));
    }
 

    public function store(AgentRequest $request) 
    {
        $id = $request->input('id');
        $record = Agent::firstOrNew(['id' => $id]);
        $record->fill($request->all());
        $record->save();

        return [
            'success' => true,
            'message' => ($id)?'Agente editado con éxito':'Agente registrado con éxito'
        ];
    }
 
  
    public function destroy($id)
    {
        $record = Agent::findOrFail($id);
        $record->delete();

        return [
            'success' => true,
            'message' => 'Agente eliminado con éxito'
        ];
    }
    

    /**
     *
     * @param  Request $request
     * @return array
     */
    public function searchAgents(Request $request)
    {
        $agents = Agent::query();
        
        if($request->has('input'))
        {
            $agents->where('name', 'like', "%{$request->input}%")
                    ->orWhere('internal_id', 'like', "%{$request->input}%");
        }
        else
        {
            $agents->take(10);
        }

        return $agents->get()->transform(function($row){
            return $row->getRowSearch();
        });
    }

}
