<?php

declare(strict_types=1);

namespace App\Domain\Doctors\Admin;

use App\Domain\Doctors\Enums\Specialty;

/**
 * Registra el grupo de campos ACF con la información profesional del médico.
 */
class AcfDoctorFieldGroup
{
    public function register(): void
    {
        add_action('acf/init', [$this, 'registerFieldGroup']);
    }

    public function registerFieldGroup(): void
    {
        if (!function_exists('acf_add_local_field_group')) {
            return;
        }

        acf_add_local_field_group([
            'key' => 'group_doctor_metadata',
            'title' => 'Información Profesional del Médico',
            'fields' => $this->buildFields(),
            'location' => [
                [
                    [
                        'param' => 'post_type',
                        'operator' => '==',
                        'value' => 'doctor',
                    ],
                ],
            ],
            'menu_order' => 0,
            'position' => 'normal',
            'style' => 'default',
            'label_placement' => 'top',
            'instruction_placement' => 'label',
        ]);
    }

    private function buildFields(): array
    {
        return [
            $this->buildSpecialtyField(),
            $this->buildLocationField(),
            $this->buildAvailabilityField(),
            $this->buildRatingField(),
            $this->buildAssignedUserField(),
        ];
    }

    private function buildSpecialtyField(): array
    {
        $choices = array_reduce(Specialty::cases(), function (array $carry, Specialty $case): array {
            $carry[$case->value] = $case->value;
            return $carry;
        }, []);

        return [
            'key' => 'field_medical_specialty',
            'label' => 'Especialidad Médica',
            'name' => 'medical_specialty',
            'type' => 'select',
            'choices' => $choices,
            'multiple' => 1,
            'ui' => 1,
            'required' => 1,
        ];
    }

    private function buildLocationField(): array
    {
        return [
            'key' => 'field_medical_location',
            'label' => 'Ubicación / Consultorio',
            'name' => 'medical_location',
            'type' => 'text',
            'required' => 1,
            'placeholder' => 'Ej: Planta 2, Consultorio 204 o Madrid, Centro',
        ];
    }

    private function buildAvailabilityField(): array
    {
        return [
            'key' => 'field_medical_availability',
            'label' => 'Disponibilidad',
            'name' => 'medical_availability',
            'type' => 'select',
            'choices' => [
                'Inmediata' => 'Inmediata',
                'Esta semana' => 'Esta semana',
                'Bajo consulta' => 'Bajo consulta',
            ],
            'default_value' => 'Bajo consulta',
            'required' => 1,
        ];
    }

    private function buildRatingField(): array
    {
        return [
            'key' => 'field_medical_rating',
            'label' => 'Puntuación Inicial (Rating)',
            'name' => 'medical_rating',
            'type' => 'number',
            'default_value' => '5.0',
            'min' => '1',
            'max' => '5',
            'step' => '0.1',
            'required' => 1,
        ];
    }

    private function buildAssignedUserField(): array
    {
        return [
            'key' => 'field_assigned_user_id',
            'label' => 'Usuario de WordPress Vinculado',
            'name' => '_assigned_user_id',
            'type' => 'user',
            'instructions' => 'Asigna el usuario de acceso para que este médico pueda gestionar su perfil.',
            'role' => ['medical_professional', 'administrator'],
            'allow_null' => 1,
            'multiple' => 0,
            'return_format' => 'id',
        ];
    }
}
