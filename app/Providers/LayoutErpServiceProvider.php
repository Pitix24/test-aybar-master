<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class LayoutErpServiceProvider extends ServiceProvider
{
    public function boot()
    {
        View::composer('layouts.erp.menu-erp', function ($view) {

            $json_menu = file_get_contents(public_path('erp-menu-principal.json'));
            $menuPrincipal = collect(json_decode($json_menu, true));

            $userRoles = auth()->check()
                ? auth()->user()->getRoleNames()
                : collect([]);

            /**
             * Filtrar menú recursivamente por roles
             */
            $filtrarMenu = function ($items) use (&$filtrarMenu, $userRoles) {
                $resultado = [];

                foreach ($items as $item) {

                    $submenusFiltrados = $filtrarMenu($item['submenus']);

                    $tienePermiso = empty($item['roles'])
                        ? true  // si el arreglo está vacío, es público
                        : collect($item['roles'])->intersect($userRoles)->isNotEmpty();

                    if ($tienePermiso || !empty($submenusFiltrados)) {
                        $item['submenus'] = $submenusFiltrados;
                        $resultado[] = $item;
                    }
                }

                return $resultado;
            };

            $menuPrincipal = collect($filtrarMenu($menuPrincipal->toArray()));

            // RUTA ACTUAL
            $currentRoute = parse_url(url()->current(), PHP_URL_PATH);

            $seleccion = [
                'nivel1' => null,
                'nivel2' => null,
                'nivel3' => null,
                'nivel4' => null,
            ];

            // RECORRER MENÚ PARA DETERMINAR SELECCIÓN
            foreach ($menuPrincipal as $n1) {
                if ($n1['url'] === $currentRoute) {
                    $seleccion['nivel1'] = $n1['id'];
                }

                foreach ($n1['submenus'] as $n2) {
                    if ($n2['url'] === $currentRoute) {
                        $seleccion['nivel1'] = $n1['id'];
                        $seleccion['nivel2'] = $n2['id'];
                    }

                    foreach ($n2['submenus'] as $n3) {
                        if ($n3['url'] === $currentRoute) {
                            $seleccion['nivel1'] = $n1['id'];
                            $seleccion['nivel2'] = $n2['id'];
                            $seleccion['nivel3'] = $n3['id'];
                        }

                        foreach ($n3['submenus'] as $n4) {
                            if ($n4['url'] === $currentRoute) {
                                $seleccion['nivel1'] = $n1['id'];
                                $seleccion['nivel2'] = $n2['id'];
                                $seleccion['nivel3'] = $n3['id'];
                                $seleccion['nivel4'] = $n4['id'];
                            }
                        }
                    }
                }
            }

            $view->with([
                'menuPrincipal' => $menuPrincipal,
                'seleccionadoNivel_1' => $seleccion['nivel1'],
                'seleccionadoNivel_2' => $seleccion['nivel2'],
                'seleccionadoNivel_3' => $seleccion['nivel3'],
                'seleccionadoNivel_4' => $seleccion['nivel4'],
            ]);
        });
    }
}
