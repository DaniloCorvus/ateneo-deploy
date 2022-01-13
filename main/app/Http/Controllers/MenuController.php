<?php

namespace App\Http\Controllers;

// use App\Models\Menu;

use App\Menu;
use App\Models\Usuario;
use App\Traits\ApiResponser;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MenuController extends Controller
{
    use ApiResponser;
    public static $mymenu = [];
    public static $user;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    public function store(Request $request)
    {

        try {

            self::$user = Usuario::where('person_id', $request->get('person_id'))->first();
            $this->getAllPermissions($request->get('filteredMenu'));
            $this->clearPermissions();
            $this->insertNewPermissions();
            self::$user->menu = $request->get('filteredMenu');
            self::$user->save();

            return $this->success('Actualización exitosa');
        } catch (\Throwable $th) {
            return $this->error([$th->getMessage(), $th->getLine()], 400);
        }


        return Response(self::$permissionSelectedPlain);
    }


    /**
     * validate ermissions.
     *
     * @param $mypermissions, $datamenu
     * @return void
     */



    static private $permissionSelectedPlain = [];

    private function  getAllPermissions($menu)
    {
        foreach ($menu as $element) {
            if ($element['child']) {
                $this->getAllPermissions($element['child']);
            }

            if (array_key_exists('permissions', $element)) {

                array_push(self::$permissionSelectedPlain, ...$element['permissions']);
            }
        };
    }

    private function clearPermissions()
    {
        DB::table('menu_permission_usuario')->where('usuario_id', '=',  self::$user->id)->delete();
    }

    private function insertNewPermissions()
    {

        foreach (self::$permissionSelectedPlain as $menu) {
            if ($menu['Activo'] == true) {
                DB::insert(
                    'insert into menu_permission_usuario (menu_permission_id, usuario_id) values (?, ?)',
                    [$menu['menu_permission_id'], self::$user->id]
                );
            }
        }
    }




    /**
     * Display the specified resource.
     *
     * @param  \App\Menu  $menu
     * @return \Illuminate\Http\Response
     */
    public function show(Menu $menu)
    {
        //

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Menu  $menu
     * @return \Illuminate\Http\Response
     */
    public function edit(Menu $menu)
    {
        $menus = Menu::get(['id', 'name']);
        $charge = [];

        foreach ($menus as $item) {
            $query = DB::table('menu_permission AS MP')->select('P.description', 'MP.menu_id', 'MP.id as menu_permission_id', 'MP.permission_id', DB::raw('if(MPU.id,TRUE,FALSE) AS Activo'))
                ->leftJoin('menu_permission_usuario AS MPU', function ($join) {
                    $join->on('MPU.menu_permission_id', 'MP.id')
                        ->where('MPU.usuario_id', 1);
                })->Join('menus AS M', 'M.id', 'MP.menu_id')
                ->Join('permissions AS P', 'P.id', 'MP.permission_id')
                ->where('MP.menu_id', $item->id)->get();

            if (count($query)) {
                $item['permissions'] = $query;
            }
            array_push($charge,    $item);
        }

        return response()->json($charge);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Menu  $menu
     * @return \Illuminate\Http\Response
     */
    public function getByPerson()
    {
        try {

            self::$user = Usuario::where('person_id', Request()->get('person_id'))->first();
            if (!self::$user) throw new Exception("No existe un usuario asociado", 400);
            $menus = Menu::whereNull('parent_id')->get(['id', 'name']);

            foreach ($menus as &$item) {
                $item['child'] = [];
                if (!$item->link) $item['child'] =  $this->getChilds($item);
            }
            return response()->json($menus);
        } catch (\Throwable $th) {
            return response()->error($th->getMessage());
        }
    }

    private function getChilds($item)
    {
        $menus = DB::table('menus AS M')
            ->select(
                'M.*'
            )
            ->where('M.parent_id', $item->id)
            ->get();

        foreach ($menus as &$itemChild) {
            $itemChild->child = [];
            $itemChild->child =  $this->getChilds($itemChild);
        }
        if ($item->link) {
            $query = DB::table('menu_permission AS MP')
                ->select(
                    'MP.menu_id',
                    'MP.permission_id',
                    'MP.id as menu_permission_id',
                    'P.name',
                    'P.public_name',
                    'P.description',
                    DB::raw('if(MPU.id,TRUE,FALSE) AS Activo')
                )
                ->leftJoin('menu_permission_usuario AS MPU', function ($join) {
                    $join->on('MPU.menu_permission_id', 'MP.id')
                        ->where('MPU.usuario_id', self::$user->id);
                })
                ->Join('permissions AS P', 'P.id', 'MP.permission_id')
                ->where('MP.menu_id', $item->id)
                ->get();

            $item->permissions = $query;
        }

        return $menus;
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Menu  $menu
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Menu $menu)
    {
        //
        try {
            //code...
        } catch (\Throwable $th) {
            //throw $th;
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Menu  $menu
     * @return \Illuminate\Http\Response
     */
    public function destroy(Menu $menu)
    {
        //
    }
}
