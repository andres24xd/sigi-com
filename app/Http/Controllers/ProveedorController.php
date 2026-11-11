<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProveedorRequest;
use App\Models\Proveedor;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProveedorController extends Controller
{
    public function index(Request $request): View
    {
        $proveedores = Proveedor::withCount('compras')
            ->withSum('compras', 'total')
            ->when($request->filled('q'), function ($query) use ($request): void {
                $query->where('nombre', 'like', '%'.trim((string) $request->input('q')).'%');
            })
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('terceros.proveedores.index', compact('proveedores'));
    }

    public function show(Proveedor $proveedor): View
    {
        $proveedor->loadCount('compras');
        $proveedor->loadSum('compras', 'total');

        $compras = $proveedor->compras()
            ->withCount('detalles')
            ->orderByDesc('fecha')
            ->orderByDesc('id')
            ->paginate(8)
            ->withQueryString();

        return view('terceros.proveedores.show', compact('proveedor', 'compras'));
    }

    public function create(): View
    {
        return view('terceros.proveedores.create');
    }

    public function store(ProveedorRequest $request): RedirectResponse
    {
        Proveedor::create($request->validated());

        return redirect()
            ->route('terceros.proveedores.index')
            ->with('success', 'Proveedor creado correctamente.');
    }

    public function edit(Proveedor $proveedor): View
    {
        return view('terceros.proveedores.edit', compact('proveedor'));
    }

    public function update(ProveedorRequest $request, Proveedor $proveedor): RedirectResponse
    {
        $proveedor->update($request->validated());

        return redirect()
            ->route('terceros.proveedores.index')
            ->with('success', 'Proveedor actualizado correctamente.');
    }

    public function destroy(Proveedor $proveedor): RedirectResponse
    {
        if ($proveedor->compras()->exists()) {
            return back()->with('error', 'No se puede eliminar el proveedor porque tiene compras registradas.');
        }

        $proveedor->delete();

        return redirect()
            ->route('terceros.proveedores.index')
            ->with('success', 'Proveedor eliminado correctamente.');
    }
}
