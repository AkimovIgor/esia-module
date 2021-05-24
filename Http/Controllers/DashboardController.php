<?php


namespace Modules\Esia\Http\Controllers;


use App\Http\Controllers\Controller;
use App\Role;
use Illuminate\Http\Request;
use Jackiedo\DotenvEditor\Facades\DotenvEditor;
use Modules\Esia\Entities\EsiaRequest;

class DashboardController extends Controller
{
    public function index()
    {
        $roles = Role::all();
        $requests = EsiaRequest::all();
        $config = config('esia');
        return view('esia::dashboard', compact('config', 'requests', 'roles'));
    }

    public function saveConfig(Request $request)
    {
        $data = $request->all();
        foreach ($data as $param => $value) {
            DotenvEditor::setKey(strtoupper($param), str_replace('\\\\', '\\', $value));
        }
        DotenvEditor::save();
        return redirect()->back();
    }
}
