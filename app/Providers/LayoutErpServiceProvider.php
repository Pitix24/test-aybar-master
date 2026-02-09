<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class LayoutErpServiceProvider extends ServiceProvider
{
    public function boot()
    {
        View::composer('layouts.erp.menu-erp', function ($view) {
            $user = auth()->user();
            if (!$user)
                return;

            // Obtenemos todos los menús activos con sus submenús
            $menuItems = \App\Models\Menu::whereNull('parent_id')
                ->where('activo', true)
                ->with([
                    'submenus' => function ($query) {
                        $query->where('activo', true)->with([
                            'submenus' => function ($q) {
                                $q->where('activo', true)->with('submenus');
                            }
                        ]);
                    }
                ])
                ->orderBy('orden')
                ->get();

            /**
             * Filtrar menú recursivamente por roles y permisos (Spatie)
             */
            $filtrarMenu = function ($items) use (&$filtrarMenu, $user) {
                $resultado = [];

                foreach ($items as $item) {
                    // Si tiene submenús, los filtramos primero
                    $submenusFiltrados = $filtrarMenu($item->submenus);

                    // Verificación de Permiso Único
                    $tienePermiso = !$item->permiso || $user->can($item->permiso);

                    // El item se muestra si:
                    // Tiene permiso directo
                    if ($tienePermiso) {
                        $item->setRelation('submenus', collect($submenusFiltrados));

                        // Si es un item con ruta '#' o vacío y no tiene submenús visibles, lo ocultamos
                        if (($item->ruta === '#' || empty($item->ruta)) && empty($submenusFiltrados)) {
                            continue;
                        }

                        $resultado[] = $item;
                    }
                }

                return $resultado;
            };

            $menuPrincipal = collect($filtrarMenu($menuItems));

            // RUTA Y URL ACTUAL
            $currentRoute = \Illuminate\Support\Facades\Route::currentRouteName();
            $currentUrl = url()->current();

            $seleccion = [
                'nivel1' => null,
                'nivel2' => null,
                'nivel3' => null,
                'nivel4' => null,
            ];

            // Sistema de selección inteligente
            $mejorPuntaje = 0; // 0: nada, 1: prefijo, 2: exacto

            // Función evaluadora
            $evaluarRuta = function ($menuRuta, $menuUrl) use ($currentRoute, $currentUrl) {
                if ($menuRuta && $menuRuta === $currentRoute)
                    return 2;
                if (!$menuRuta && $menuUrl && url($menuUrl) === $currentUrl)
                    return 2;

                if ($menuRuta && $currentRoute) {
                    $partesMenu = explode('.', $menuRuta);
                    $partesActual = explode('.', $currentRoute);
                    if (count($partesMenu) >= 3 && count($partesActual) >= 3) {
                        if (
                            $partesMenu[0] === $partesActual[0] &&
                            $partesMenu[1] === $partesActual[1] &&
                            $partesMenu[2] === $partesActual[2]
                        ) {
                            return 1;
                        }
                    }
                }
                return 0;
            };

            // RECORRER MENÚ PARA DETERMINAR SELECCIÓN
            foreach ($menuPrincipal as $n1) {
                $p1 = $evaluarRuta($n1->ruta, $n1->url);
                if ($p1 > $mejorPuntaje) {
                    $mejorPuntaje = $p1;
                    $seleccion = ['nivel1' => $n1->id, 'nivel2' => null, 'nivel3' => null, 'nivel4' => null];
                }

                foreach ($n1->submenus as $n2) {
                    $p2 = $evaluarRuta($n2->ruta, $n2->url);
                    if ($p2 > $mejorPuntaje) {
                        $mejorPuntaje = $p2;
                        $seleccion = ['nivel1' => $n1->id, 'nivel2' => $n2->id, 'nivel3' => null, 'nivel4' => null];
                    }

                    foreach ($n2->submenus as $n3) {
                        $p3 = $evaluarRuta($n3->ruta, $n3->url);
                        if ($p3 > $mejorPuntaje) {
                            $mejorPuntaje = $p3;
                            $seleccion = ['nivel1' => $n1->id, 'nivel2' => $n2->id, 'nivel3' => $n3->id, 'nivel4' => null];
                        }

                        foreach ($n3->submenus as $n4) {
                            $p4 = $evaluarRuta($n4->ruta, $n4->url);
                            if ($p4 > $mejorPuntaje) {
                                $mejorPuntaje = $p4;
                                $seleccion = ['nivel1' => $n1->id, 'nivel2' => $n2->id, 'nivel3' => $n3->id, 'nivel4' => $n4->id];
                            }
                        }
                    }
                }
                if ($mejorPuntaje === 2)
                    break;
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
