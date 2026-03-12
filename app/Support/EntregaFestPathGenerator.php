<?php

namespace App\Support;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\Support\PathGenerator\PathGenerator;

class EntregaFestPathGenerator implements PathGenerator
{
    /*
     * Get the path for the given media, relative to the root storage path.
     */
    public function getPath(Media $media): string
    {
        $folder = $this->getBaseFolder($media);
        return $folder . $media->id . '/';
    }

    /*
     * Get the path for conversions of the given media, relative to the root storage path.
     */
    public function getPathForConversions(Media $media): string
    {
        $folder = $this->getBaseFolder($media);
        return $folder . $media->id . '/conversions/';
    }

    /*
     * Get the path for responsive images of the given media, relative to the root storage path.
     */
    public function getPathForResponsiveImages(Media $media): string
    {
        $folder = $this->getBaseFolder($media);
        return $folder . $media->id . '/responsive-images/';
    }

    /*
     * Custom logic to determine the base folder based on the model type
     */
    protected function getBaseFolder(Media $media): string
    {
        // Si el modelo es del módulo Entrega Fest, lo ponemos en su carpeta
        if (str_contains($media->model_type, 'EntregaFest')) {
            return 'entrega-fest/';
        }

        // Por defecto, lo dejamos en la raíz del disco (comportamiento original de Spatie)
        return '';
    }
}
