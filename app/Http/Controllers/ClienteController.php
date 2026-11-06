<?php

namespace App\Http\Controllers;

use App\Http\Requests\ClienteRequest;
use App\Models\Cliente;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ClienteController extends Controller
{
    public function index(Request $request): View
    {
        $clientes = Cliente::withCount('ventas')
            ->withSum('ventas', 'total')
            ->when($request->filled('q'), function ($query) use ($request): void {
                $query->where('nombre', 'like', '%'.trim((string) $request->input('q')).'%');
            })
            ->orderBy('nombre')
            ->paginate(10)
            ->withQueryString();

        return view('terceros.clientes.index', compact('clientes'));
    }

    public function create(): View
    {
        return view('terceros.clientes.create');
    }

    public function store(ClienteRequest $request): RedirectResponse
    {
        Cliente::create($request->validated());

        return redirect()
            ->route('terceros.clientes.index')
            ->with('success', 'Cliente creado correctamente.');
    }

    public function edit(Cliente $cliente): View
    {
        return view('terceros.clientes.edit', compact('cliente'));
    }

    public function update(ClienteRequest $request, Cliente $cliente): RedirectResponse
    {
        $cliente->update($request->validated());

        return redirect()
            ->route('terceros.clientes.index')
            ->with('success', 'Cliente actualizado correctamente.');
    }

    public function destroy(Cliente $cliente): RedirectResponse
    {
        $cliente->delete();

        return redirect()
            ->route('terceros.clientes.index')
            ->with('success', 'Cliente eliminado. Sus ventas quedaron registradas como consumidor final.');
    }
}
