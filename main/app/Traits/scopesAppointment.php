<?php

namespace App\Traits;


trait scopesAppointment
{
    public function scopeRealtions($query)
    {
        return $query->with(
            [
                'callIn' => function ($q) {
                    $q->select('*');
                },
                'callIn.patient' => function ($q) {
                    $q->select('*');
                },
                'callIn.patient.company' => function ($q) {
                    $q->select('*');
                },
                'callIn.patient.eps' => function ($q) {
                    $q->select('*');
                },

                'callIn.formality' => function ($q) {
                    $q->select('*');
                },

                'space' => function ($q) {
                    $q->select('*');
                },

                'space.agendamiento' => function ($q) {
                    $q->select('*');
                },

                'space.agendamiento.person' => function ($q) {
                    $q->select('*');
                },

                'space.agendamiento.speciality' => function ($q) {
                    $q->select('*');
                },
                'space.agendamiento.subTypeAppointment' => function ($q) {
                    $q->select('*');
                },
                'space.agendamiento.typeAppointment' => function ($q) {
                    $q->select('*');
                },
                'space.agendamiento.company' => function ($q) {
                    $q->select('*');
                },
                'space.agendamiento.location' => function ($q) {
                    $q->select('*');
                }
            ]
        );
    }
}
