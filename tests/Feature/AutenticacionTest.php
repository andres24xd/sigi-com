<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Venta;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AutenticacionTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();
    }

    private function administrador(): User
    {
        return User::where('rol', 'Administrador')->firstOrFail();
    }

    private function cajero(): User
    {
        return User::where('rol', 'Cajero')->firstOrFail();
    }

    public function test_un_invitado_no_puede_entrar_al_panel(): void
    {
        $this->get(route('dashboard'))->assertRedirect(route('login'));
        $this->get(route('operaciones.ventas.index'))->assertRedirect(route('login'));
        $this->get(route('reportes.ventas'))->assertRedirect(route('login'));
    }

    public function test_la_pantalla_de_login_carga(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Iniciar sesión')
            ->assertSee('admin@sigicom.com');
    }

    public function test_un_administrador_inicia_sesion_con_sus_credenciales(): void
    {
        $this->post(route('login.store'), [
            'email' => 'admin@sigicom.com',
            'password' => 'admin123',
        ])->assertRedirect(route('dashboard'))
            ->assertSessionHas('success');

        $this->assertAuthenticated();
        $this->assertSame('Administrador', auth()->user()->rol);
    }

    public function test_un_cajero_inicia_sesion_y_ve_su_panel(): void
    {
        $this->post(route('login.store'), [
            'email' => 'cajero@sigicom.com',
            'password' => 'cajero123',
        ])->assertRedirect(route('dashboard'));

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee('Panel de cajero');
    }

    public function test_las_credenciales_invalidas_muestran_error_y_conservan_el_correo(): void
    {
        $this->from(route('login'))
            ->post(route('login.store'), [
                'email' => 'admin@sigicom.com',
                'password' => 'contraseña-incorrecta',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email')
            ->assertSessionHasInput('email', 'admin@sigicom.com');

        $this->assertGuest();
    }

    public function test_el_cajero_no_entra_a_las_secciones_de_administrador(): void
    {
        $cajero = $this->cajero();

        $rutas = [
            route('operaciones.compras.index'),
            route('terceros.proveedores.index'),
            route('inventario.productos.create'),
            route('inventario.categorias.create'),
            route('reportes.compras'),
            route('reportes.inventario'),
        ];

        foreach ($rutas as $ruta) {
            $this->actingAs($cajero)->get($ruta)->assertForbidden();
        }

        $this->actingAs($cajero)
            ->get(route('operaciones.compras.index'))
            ->assertSee('Acceso restringido');
    }

    public function test_el_cajero_puede_vender_y_consultar_sus_operaciones(): void
    {
        $cajero = $this->cajero();

        $this->actingAs($cajero)->get(route('operaciones.ventas.create'))->assertOk();
        $this->get(route('inventario.productos.index'))->assertOk();
        $this->get(route('terceros.clientes.index'))->assertOk();
        $this->get(route('terceros.clientes.create'))->assertOk();
    }

    public function test_el_administrador_entra_a_todos_los_modulos(): void
    {
        $admin = $this->administrador();

        $rutas = [
            route('operaciones.compras.index'),
            route('terceros.proveedores.index'),
            route('inventario.productos.create'),
            route('inventario.categorias.create'),
            route('reportes.index'),
            route('reportes.compras'),
            route('reportes.inventario'),
        ];

        foreach ($rutas as $ruta) {
            $this->actingAs($admin)->get($ruta)->assertOk();
        }
    }

    public function test_el_cajero_solo_ve_sus_propias_ventas_en_el_reporte(): void
    {
        $cajero = $this->cajero();
        $admin = $this->administrador();

        Venta::factory()->create([
            'usuario_id' => $cajero->id,
            'cliente_id' => null,
            'fecha' => now(),
            'total' => 111000,
        ]);

        Venta::factory()->create([
            'usuario_id' => $admin->id,
            'cliente_id' => null,
            'fecha' => now(),
            'total' => 222000,
        ]);

        $esperado = Venta::where('usuario_id', $cajero->id)
            ->whereDate('fecha', '>=', now()->startOfMonth()->toDateString())
            ->whereDate('fecha', '<=', now()->toDateString())
            ->count();

        $response = $this->actingAs($cajero)->get(route('reportes.ventas'));

        $response->assertOk();
        $this->assertSame($esperado, $response->viewData('resumen')['cantidad']);
    }

    public function test_el_resumen_de_reportes_redirige_al_cajero_a_sus_ventas(): void
    {
        $this->actingAs($this->cajero())
            ->get(route('reportes.index'))
            ->assertRedirect(route('reportes.ventas'));
    }

    public function test_cerrar_sesion_invalida_la_sesion(): void
    {
        $this->actingAs($this->cajero())
            ->post(route('logout'))
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }
}
