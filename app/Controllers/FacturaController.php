<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Models\Lavada;

class FacturaController extends Controller
{
    /** Factura imprimible (POS 80mm) sin layout. */
    public function show(string $id): void
    {
        $lavada = (new Lavada())->porId((int) $id);
        if (!$lavada) {
            http_response_code(404);
            echo 'Lavada no encontrada';
            return;
        }
        $this->view('factura/ticket', [
            'l'     => $lavada,
            'waNum' => WA_NUM,
            'meta'  => LAVADAS_PARA_GRATIS,
        ], ''); // sin layout
    }
}
