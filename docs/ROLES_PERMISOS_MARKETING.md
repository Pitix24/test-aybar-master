MODULO
modulo-marketing.ver

PERMISO BASADO EN ROLES:
supervisor-marketing
asesor-marketing

TUTORIAL
tutorial.navegacion
tutorial.lista
tutorial.ver
tutorial.crear
tutorial.editar
tutorial.eliminar
tutorial.exportar-filtro
tutorial.exportar-todo

REGLAMENTO
reglamento.lista
reglamento.crear
reglamento.editar
reglamento.eliminar

AVANCE PROYECTO
avance-proyecto.lista
avance-proyecto.editar

NO USADOS / NO APLICADOS EN CODIGO
reglamento.navegacion
reglamento.ver
reglamento.exportar-filtro
reglamento.exportar-todo
avance-proyecto.crear
avance-proyecto.ver

USO EN CODIGO
routes/erp/marketing.php
app/Livewire/Erp/Marketing/Tutorial/TutorialLista.php
app/Livewire/Erp/Marketing/Tutorial/TutorialCrear.php
app/Livewire/Erp/Marketing/Tutorial/TutorialEditar.php
app/Livewire/Erp/Marketing/Tutorial/TutorialVer.php
app/Livewire/Erp/Marketing/Reglamento/ReglamentoLista.php
app/Livewire/Erp/Marketing/Reglamento/ReglamentoCrear.php
app/Livewire/Erp/Marketing/Reglamento/ReglamentoEditar.php
app/Livewire/Erp/Marketing/Reglamento/ReglamentoVer.php
app/Livewire/Erp/Marketing/AvanceProyecto/AvanceProyectoLista.php
app/Livewire/Erp/Marketing/AvanceProyecto/AvanceProyectoCrear.php
app/Livewire/Erp/Marketing/AvanceProyecto/AvanceProyectoEditar.php
app/Livewire/Erp/Marketing/AvanceProyecto/AvanceProyectoVer.php

NOTA
Tutorial y Reglamento si usan authorize directo dentro de Livewire. Avance Proyecto hoy solo protege lista y edicion en Livewire; las rutas de crear y ver quedan sin middleware de permiso explicito.
