php artisan make:model UnidadNegocio -mfsc
php artisan make:livewire erp.unidad-negocio.unidad-negocio-lista --class
php artisan make:livewire erp.unidad-negocio.unidad-negocio-crear --class
php artisan make:livewire erp.unidad-negocio.unidad-negocio-editar --class

php artisan make:model GrupoProyecto -mfsc
php artisan make:livewire erp.grupo-proyecto.grupo-proyecto-lista --class
php artisan make:livewire erp.grupo-proyecto.grupo-proyecto-crear --class
php artisan make:livewire erp.grupo-proyecto.grupo-proyecto-editar --class

php artisan make:model Proyecto -mfsc
php artisan make:livewire erp.proyecto.proyecto-lista --class
php artisan make:livewire erp.proyecto.proyecto-crear --class
php artisan make:livewire erp.proyecto.proyecto-editar --class

php artisan make:model Pais -mfs
php artisan make:model Region -mfs
php artisan make:model Provincia -mfs
php artisan make:model Distrito -mfs

php artisan make:model Cliente -mfsc
php artisan make:livewire erp.cliente.cliente-lista --class
php artisan make:livewire erp.cliente.cliente-crear --class
php artisan make:livewire erp.cliente.cliente-editar --class

php artisan make:model Direccion -mfsc
php artisan make:livewire erp.direccion.direccion-lista --class
php artisan make:livewire erp.direccion.direccion-crear --class
php artisan make:livewire erp.direccion.direccion-editar --class
