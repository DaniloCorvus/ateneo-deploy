export const menuItems = [
    {
        id: 1,
        label: 'Tablero',
        icon: 'ri-dashboard-line',
        link: '/'
    },
    {
        id: 2,
        label: 'Agendamiento',
        icon: 'ri-calendar-2-line',
        subItems: [
            {
                id: 3,
                label: 'Abrir Agendas',
                link: '/abriragendas'
            },
            {
                id: 4,
                label: 'Asignacion de Citas',
                link: '/asignacioncitas'
            },
            {
                id: 5,
                label: 'Lista de Espera',
                link: '/listaespera'
            },
            {
                id: 6,
                label: 'Lista de Trabajo',
                link: '/listatrabajo'
            },
            {
                id: 7,
                label: 'Indicadores Gestión',
                link: '/indicadoresagendamiento'
            },
            {
                id: 8,
                label: 'Reportes',
                link: '/reportesagendamiento'
            }
        ]
    },
    {
        id: 9,
        label: 'Gestión del Riesgo',
        icon: 'ri-bar-chart-2-line',
        subItems: [
            {
                id: 10,
                label: 'Caraterizacion',
                link: '/caracterizacion'
            },
            {
                id: 11,
                label: 'Kardex por Patologia',
                link: '/kardexpatologia'
            },
            {
                id: 32,
                label: 'Historias Clínicas',
                link: '/historiasclinicas'
            }
        ]
    },
    {
        id: 12,
        label: 'SST',
        icon: 'ri-heart-2-line',
        subItems: [
            {
                id: 13,
                label: 'Documentos de Gestión',
                link: '/documentos'
            }
        ]
    },
    {
        id: 14,
        label: 'Ajustes',
        icon: 'ri-settings-5-line',
        subItems: [
            {
                id: 15,
                label: 'Información Base',
                subItems: [
                    {
                        id: 16,
                        label: 'Empresas',
                        link: '/empresas'
                    },
                    {
                        id: 17,
                        label: 'Sedes',
                        link: '/sedes'
                    },
                    {
                        id: 18,
                        label: 'Funcionarios',
                        link: '/funcionarios'
                    },
                    {
                        id: 19,
                        label: 'Profesionales',
                        link: '/profesionales'
                    },
                    {
                        id: 20,
                        label: 'Pacientes',
                        link: '/pacientes'
                    },
                    {
                        id: 21,
                        label: 'Especialidades',
                        link: '/especialidades'
                    },
                    {
                        id: 22,
                        label: 'CUPS',
                        link: '/cups'
                    },
                    {
                        id: 23,
                        label: 'Regímenes y Niveles',
                        link: '/regimenes'
                    },
                    {
                        id: 24,
                        label: 'Aseguradoras',
                        link: '/aseguradoras'
                    }
                ]
            },
            {
                id: 25,
                label: 'Tipos',
                subItems: [
                    {
                        id: 26,
                        label: 'Tipos de Agenda',
                        link: '/tiposagenda'
                    },
                    {
                        id: 27,
                        label: 'Tipos de Consulta',
                        link: '/tiposconsulta'
                    },
                ]
            },
            {
                id: 28,
                label: 'Parámetros',
                subItems: [
                    {
                        id: 29,
                        label: 'Perfiles',
                        link: '/perfiles'
                    },
                    {
                        id: 30,
                        label: 'PGP',
                        link: '/pgp'
                    },
                    {
                        id: 31,
                        label: 'Notas Tecnicas',
                        link: '/notastecnicas'
                    },
                ]
            }
        ]
    }
]
