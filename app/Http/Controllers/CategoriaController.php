<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoriaRequest;
use App\Models\Categoria;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CategoriaController extends Controller
{
    public function index(Request $request): View
    {
        $categorias = Categoria::withCount('productos')
            ->when($request->filled('q'), function ($query) use ($request): void {
                $query->where('nombre', 'like', '%'.trim((string) $request->input('q')).'%');
            })
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('inventario.categorias.index', compact('categorias'));
    }

    public function show(Categoria $categoria): View
    {
        $categoria->loadCount('productos');

        $productos = $categoria->productos()
            ->orderBy('nombre')
            ->paginate(8)
            ->withQueryString();

        return view('inventario.categorias.show', compact('categoria', 'productos'));
    }

    public function create(): View
    {
        return view('inventario.categorias.create');
    }

    public function store(CategoriaRequest $request): RedirectResponse
    {
        Categoria::create($request->validated());

        return redirect()
            ->route('inventario.categorias.index')
            ->with('success', 'Categoría creada correctamente.');
    }

    public function edit(Categoria $categoria): View
    {
        return view('inventario.categorias.edit', compact('categoria'));
    }

    public function update(CategoriaRequest $request, Categoria $categoria): RedirectResponse
    {
        $categoria->update($request->validated());

        return redirect()
            ->route('inventario.categorias.index')
            ->with('success', 'Categoría actualizada correctamente.');
    }

    public function destroy(Categoria $categoria): RedirectResponse
    {
        if ($categoria->productos()->exists()) {
            return back()->with('error', 'No se puede eliminar la categoría porque tiene productos asociados.');
        }

        $categoria->delete();

        return redirect()
            ->route('inventario.categorias.index')
            ->with('success', 'Categoría eliminada correctamente.');
    }
}
