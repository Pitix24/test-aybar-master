<?php

namespace Tests;

use Illuminate\Database\Events\ConnectionEstablished;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Support\Facades\Event;

abstract class TestCase extends BaseTestCase
{
    /**
     * Sin este registro, cualquier test que dispara RefreshDatabase falla al migrar contra
     * sqlite ("no such collation sequence: utf8mb4_bin"): las migraciones de
     * prospecto_entrega_fests y prospecto_historicos (pensadas para MySQL) declaran columnas
     * con collation('utf8mb4_bin'), que sqlite no reconoce de forma nativa. El mismo defecto ya
     * está diagnosticado y resuelto así en aybar-front-platform (tests/TestCase.php).
     */
    public function createApplication()
    {
        $app = parent::createApplication();

        Event::listen(ConnectionEstablished::class, function (ConnectionEstablished $event) {
            if ($event->connection->getDriverName() !== 'sqlite') {
                return;
            }

            $pdo = $event->connection->getPdo();

            if (method_exists($pdo, 'sqliteCreateCollation')) {
                $pdo->sqliteCreateCollation('utf8mb4_bin', 'strcmp');
            }
        });

        return $app;
    }
}
