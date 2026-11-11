<?php

declare(strict_types=1);

namespace App\Models;

final class HomeModel
{
    /**
     * @return array<string, mixed>
     */
    public function welcome(): array
    {
        return [
            'brand' => 'Clínica',
            'title' => 'Atención médica clara, cercana y oportuna.',
            'message' => 'Conoce nuestros servicios, encuentra la información que necesitas '
                . 'y solicita tu cita por WhatsApp.',
            'environment' => getenv('APP_ENV') ?: 'production',
            'whatsapp' => '523320705080',
            'phone' => '33 2070 5080',
            'address' => 'Calle Tala 227, colonia Jalisco, Tonalá, Jalisco',
            'services' => [
                [
                    'number' => '01',
                    'title' => 'Consulta médica',
                    'description' => 'Recibe orientación para identificar el tipo de atención que necesitas '
                        . 'y los siguientes pasos.',
                    'image' => 'conulta_medica.png',
                ],
                [
                    'number' => '02',
                    'title' => 'Seguimiento',
                    'description' => 'Da continuidad a tu atención con información clara '
                        . 'y comunicación cercana con la clínica.',
                    'image' => 'Medicos_ayudando.png',
                ],
                [
                    'number' => '03',
                    'title' => 'Agenda por WhatsApp',
                    'description' => 'Solicita información sobre disponibilidad, requisitos '
                        . 'y confirmación de tu cita.',
                    'image' => 'recepcionista.png',
                ],
            ],
            // No se muestran horarios ni descripciones clínicas hasta confirmar vigencia y aprobación.
            'specialists' => [
                [
                    'name' => 'Elizabeth Rocha Dueñas',
                    'organization' => 'Nutrybel',
                    'area' => 'Nutrición',
                    'status' => 'Horario y descripción por confirmar',
                    'image' => 'plato_buen_comer.png',
                ],
                [
                    'name' => 'Paulina Lizeth de Anda',
                    'organization' => '',
                    'area' => 'Psicología',
                    'status' => 'Horario y presentación profesional por confirmar',
                    'image' => 'conulta_medica.png',
                ],
                [
                    'name' => 'Aydee Ramos',
                    'organization' => '',
                    'area' => 'Pediatría',
                    'status' => 'Disponibilidad y presentación por confirmar',
                    'image' => 'pediatra.png',
                ],
                [
                    'name' => 'Beatriz Ortiz',
                    'organization' => '',
                    'area' => 'Sueros multivitamínicos',
                    'status' => 'Servicio y descripción por confirmar',
                    'image' => 'Medicos_ayudando.png',
                ],
            ],
            'pendingAreas' => [
                [
                    'title' => 'Podología',
                    'status' => 'Profesional y disponibilidad por confirmar',
                    'image' => 'podologo.png',
                ],
            ],
        ];
    }
}
