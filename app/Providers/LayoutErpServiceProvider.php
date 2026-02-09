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

            // RECORRER MENÚ PARA DETERMINAR SELECCIÓN
            foreach ($menuPrincipal as $n1) {
                if (
                    ($n1->ruta && $n1->ruta === $currentRoute) ||
                    (!$n1->ruta && $n1->url && url($n1->url) === $currentUrl)
                ) {
                    $seleccion['nivel1'] = $n1->id;
                }

                foreach ($n1->submenus as $n2) {
                    if (
                        ($n2->ruta && $n2->ruta === $currentRoute) ||
                        (!$n2->ruta && $n2->url && url($n2->url) === $currentUrl)
                    ) {
                        $seleccion['nivel1'] = $n1->id;
                        $seleccion['nivel2'] = $n2->id;
                    }

                    foreach ($n2->submenus as $n3) {
                        if (
                            ($n3->ruta && $n3->ruta === $currentRoute) ||
                            (!$n3->ruta && $n3->url && url($n3->url) === $currentUrl)
                        ) {
                            $seleccion['nivel1'] = $n1->id;
                            $seleccion['nivel2'] = $n2->id;
                            $seleccion['nivel3'] = $n3->id;
                        }

                        foreach ($n3->submenus as $n4) {
                            if (
                                ($n4->ruta && $n4->ruta === $currentRoute) ||
                                (!$n4->ruta && $n4->url && url($n4->url) === $currentUrl)
                            ) {
                                $seleccion['nivel1'] = $n1->id;
                                $seleccion['nivel2'] = $n2->id;
                                $seleccion['nivel3'] = $n3->id;
                                $seleccion['nivel4'] = $n4->id;
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
