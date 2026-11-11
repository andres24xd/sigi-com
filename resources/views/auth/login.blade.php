<!DOCTYPE html>
<html lang="es" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar sesión · SIGI-COM</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Instrument+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-full font-sans">
    @include('partials.icons')

    <main class="relative flex min-h-screen items-center justify-center overflow-hidden bg-slate-950 px-4 py-10">
        <div class="pointer-events-none absolute -left-24 -top-24 h-96 w-96 rounded-full bg-brand-500/20 blur-3xl"></div>
        <div class="pointer-events-none absolute -bottom-32 -right-24 h-[28rem] w-[28rem] rounded-full bg-sky-500/10 blur-3xl"></div>

        <div class="relative grid w-full max-w-5xl overflow-hidden rounded-3xl border border-white/10 bg-white shadow-2xl shadow-black/40 lg:grid-cols-2">
            {{-- Panel de marca --}}
            <section class="hidden flex-col justify-between bg-slate-950 p-10 text-white lg:flex">
                <div>
                    <div class="flex items-center gap-3">
                        <span class="grid h-11 w-11 place-items-center rounded-2xl bg-brand-500 text-base font-bold text-white shadow-lg shadow-brand-500/30">S</span>
                        <div>
                            <p class="text-base font-bold tracking-tight">SIGI-COM</p>
                            <p class="text-xs text-slate-400">Inventario, compras y ventas</p>
                        </div>
                    </div>

                    <h2 class="mt-12 text-3xl font-bold leading-tight tracking-tight">
                        Todo el negocio bajo control desde un solo lugar.
                    </h2>
                    <p class="mt-4 text-sm leading-relaxed text-slate-400">
                        Controla el inventario, registra compras y ventas, y consulta el historial
                        con trazabilidad por usuario.
                    </p>

                    <ul class="mt-8 space-y-3 text-sm text-slate-300">
                        <li class="flex items-start gap-3">
                            <x-icon name="check" class="mt-0.5 h-5 w-5 shrink-0 text-brand-400" />
                            Control automático de stock en cada movimiento.
                        </li>
                        <li class="flex items-start gap-3">
                            <x-icon name="check" class="mt-0.5 h-5 w-5 shrink-0 text-brand-400" />
                            Reportes por fecha, cliente, proveedor y cajero.
                        </li>
                        <li class="flex items-start gap-3">
                            <x-icon name="check" class="mt-0.5 h-5 w-5 shrink-0 text-brand-400" />
                            Permisos diferenciados para administrador y cajero.
                        </li>
                    </ul>
                </div>

                <div class="border-t border-white/10 pt-6">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.16em] text-slate-500">Equipo de desarrollo</p>
                    <p class="mt-2 text-xs leading-relaxed text-slate-400">
                        Andrés Felipe Sánchez Acuña · Luis José Villadiego Pineda · Carlos Andrés Ortega Cadrazco
                    </p>
                </div>
            </section>

            {{-- Formulario --}}
            <section class="p-8 sm:p-10">
                <div class="mb-8 flex items-center gap-3 lg:hidden">
                    <span class="grid h-11 w-11 place-items-center rounded-2xl bg-brand-500 text-base font-bold text-white">S</span>
                    <div>
                        <p class="text-base font-bold tracking-tight text-slate-900">SIGI-COM</p>
                        <p class="text-xs text-slate-500">Inventario, compras y ventas</p>
                    </div>
                </div>

                <h1 class="text-2xl font-bold tracking-tight text-slate-900">Iniciar sesión</h1>
                <p class="mt-1 text-sm text-slate-500">Ingresa con la cuenta que te asignó el administrador.</p>

                @if (session('success'))
                    <div class="mt-6 flex items-start gap-3 rounded-2xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                        <x-icon name="check" class="mt-0.5 h-5 w-5 shrink-0 text-emerald-600" />
                        <p>{{ session('success') }}</p>
                    </div>
                @endif

                @if ($errors->any())
                    <div class="mt-6 flex items-start gap-3 rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                        <x-icon name="alert" class="mt-0.5 h-5 w-5 shrink-0 text-rose-600" />
                        <p>No pudimos iniciar sesión. Revisa los datos e inténtalo de nuevo.</p>
                    </div>
                @endif

                <form method="POST" action="{{ route('login.store') }}" class="mt-6 space-y-5">
                    @csrf

                    <x-input
                        name="email"
                        label="Correo electrónico"
                        type="email"
                        placeholder="usuario@sigicom.com"
                        autocomplete="username"
                        required
                        autofocus
                    />

                    <div>
                        <x-input
                            name="password"
                            label="Contraseña"
                            type="password"
                            placeholder="Tu contraseña"
                            autocomplete="current-password"
                            required
                        />
                    </div>

                    <label class="flex items-center gap-2 text-sm text-slate-600">
                        <input type="checkbox" name="remember" value="1" class="h-4 w-4 rounded border-slate-300 text-brand-600 focus:ring-brand-500/30">
                        Mantener sesión iniciada
                    </label>

                    <button type="submit" class="btn btn-primary w-full">
                        Entrar al sistema
                        <x-icon name="arrow-right" class="h-4 w-4" />
                    </button>
                </form>

                <div class="mt-8 rounded-2xl border border-slate-200 bg-slate-50 p-4">
                    <p class="text-[11px] font-semibold uppercase tracking-[0.12em] text-slate-500">Cuentas de demostración</p>
                    <div class="mt-3 space-y-2 text-sm">
                        <div class="flex items-center justify-between gap-3">
                            <span class="font-medium text-slate-700">Administrador</span>
                            <code class="rounded-md bg-white px-2 py-1 text-xs text-slate-600 ring-1 ring-slate-200">admin@sigicom.com / admin123</code>
                        </div>
                        <div class="flex items-center justify-between gap-3">
                            <span class="font-medium text-slate-700">Cajero</span>
                            <code class="rounded-md bg-white px-2 py-1 text-xs text-slate-600 ring-1 ring-slate-200">cajero@sigicom.com / cajero123</code>
                        </div>
                    </div>
                </div>
            </section>
        </div>
    </main>
</body>
</html>
