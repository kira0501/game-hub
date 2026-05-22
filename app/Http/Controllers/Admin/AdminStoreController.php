<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\AdminStoreRequest;
use App\Models\Store;

class AdminStoreController extends Controller
{
    public function index()
    {
        return view('admin.stores.index', [
            'stores' => Store::withCount('prices')->paginate(20),
        ]);
    }

    public function create()
    {
        return view('admin.stores.form', ['store' => new Store()]);
    }

    public function store(AdminStoreRequest $request)
    {
        Store::create($request->validated());

        return redirect()->route('admin.stores.index')->with('status', 'Магазин создан.');
    }

    public function edit(Store $store)
    {
        return view('admin.stores.form', compact('store'));
    }

    public function update(AdminStoreRequest $request, Store $store)
    {
        $store->update($request->validated());

        return redirect()->route('admin.stores.index')->with('status', 'Магазин обновлен.');
    }

    public function destroy(Store $store)
    {
        $store->delete();

        return back()->with('status', 'Магазин удален.');
    }
}
