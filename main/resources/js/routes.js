import Login                    from './components/Login'
import TableroPrincipal         from './components/tableros/TableroPrincipal'

/** AGENDAMIENTO  - Se importan los componentes de este enlace */
import PanelAgenda             from './components/agendamiento/abriragendas/PanelAgenda'
// import AbrirAgendas             from './components/agendamiento/abriragendas/AbrirAgendas'
import AsignacionCitas          from './components/agendamiento/asignacioncitas/AsignacionCitas'
import ListaEspera              from './components/agendamiento/listaespera/ListaEspera'
import ListaTrabajo             from './components/agendamiento/listatrabajo/ListaTrabajo'
import IndicadoresAgendamiento  from './components/agendamiento/indicadoresagendamiento/IndicadoresAgendamiento'
import ReportesAgendamiento     from './components/agendamiento/reportesagendamiento/ReportesAgendamiento'

/** GESTION DEL RIESGO  - Se importan los componentes de este enlace */
import Caracterizacion          from './components/gestionriesgo/caracterizacion/Caracterizacion'
import KardexPatologia          from './components/gestionriesgo/kardexpatologia/KardexPatologia'
import HistoriasClinicas        from './components/gestionriesgo/historiasclinicas/HistoriasClinicas'

/** SST  - Se importan los componentes de este enlace */
import Documentos               from './components/sst/documentos/Documentos'

/** AJUSTES / BASE - Se importan los componentes de este enlace */
import Empresas                 from './components/ajustes/base/empresas/Empresas'
import Sedes                    from './components/ajustes/base/sedes/Sedes'
import Funcionarios             from './components/ajustes/base/funcionarios/Funcionarios'
import Profesionales            from './components/ajustes/base/profesionales/Profesionales'
import Pacientes                from './components/ajustes/base/pacientes/Pacientes'
import Especialidades           from './components/ajustes/base/especialidades/Especialidades'
import Cups                     from './components/ajustes/base/cups/Cups'
import Regimenes                from './components/ajustes/base/regimenes/Regimenes'
import Aseguradoras             from './components/ajustes/base/aseguradoras/Aseguradoras'

/** AJUSTES / TIPOS - Se importan los componentes de este enlace */
import TiposAgenda              from './components/ajustes/tipos/tiposagenda/TiposAgenda'
import TiposConsulta            from './components/ajustes/tipos/tiposconsulta/TiposConsulta'

/** AJUSTES / TIPOS - Se importan los componentes de este enlace */
import Pgp                      from './components/ajustes/parametros/pgp/Pgp'
import Perfiles                 from './components/ajustes/parametros/perfiles/Perfiles'
import NotasTecnicas            from './components/ajustes/parametros/notasTecnicas/NotasTecnicas'  



export default {
  mode: 'history',
  routes: [
    {
      path: '/',
      redirect: '/tablero',
    },
    {
      path: '/login',
      component: Login,
      name: 'Login', 
    },
    {
      path: '/tablero',
      component: TableroPrincipal,
      name: 'TableroPrincipal',
    },
    /** AGENDAMIENTO - Se definen las rutas de este menu */
    {
      path: '/abriragendas',
      component: PanelAgenda,
      name: 'Abrir Agendas',
    },
    {
      path: '/asignacioncitas',
      component: AsignacionCitas,
      name: 'Asignacion citas',
    },
    {
      path: '/listaespera',
      component: ListaEspera,
      name: 'Lista Espera',
    },
    {
      path: '/listatrabajo',
      component: ListaTrabajo,
      name: 'Lista Trabajo',
    },
    {
      path: '/indicadoresagendamiento',
      component: IndicadoresAgendamiento,
      name: 'Indicadores Agendamiento',
    },
    {
      path: '/reportesagendamiento',
      component: ReportesAgendamiento,
      name: 'Reportes Agendamiento',
    },
    /** GESTION DEL RIESGO - Se definen las rutas de este menu */
    {
      path: '/caracterizacion',
      component: Caracterizacion,
      name: 'Caracterizacion',
    },
    {
      path: '/kardexpatologia',
      component: KardexPatologia,
      name: 'KardexPatologia',
    },
    {
      path: '/historiasclinicas',
      component: HistoriasClinicas,
      name: 'HistoriasClinicas',
    },
    /** SST - Se definen las rutas de este menu */
    {
      path: '/documentos',
      component: Documentos,
      name: 'Documentos',
    },
    /** AJUSTES / BASE - Se definen las rutas de este menu */
    {
      path: '/empresas',
      component: Empresas,
      name: 'Empresas',
    },
    {
      path: '/sedes',
      component: Sedes,
      name: 'Sedes',
    },
    {
      path: '/funcionarios',
      component: Funcionarios,
      name: 'Funcionarios',
    },
    {
      path: '/profesionales',
      component: Profesionales,
      name: 'Profesionales',
    },
    {
      path: '/pacientes',
      component: Pacientes,
      name: 'Pacientes',
    },
    {
      path: '/especialidades',
      component: Especialidades,
      name: 'Especialidades',
    },
    {
      path: '/cups',
      component: Cups,
      name: 'Cups',
    },
    {
      path: '/regimenes',
      component: Regimenes,
      name: 'Regimenes',
    },
    {
      path: '/aseguradoras',
      component: Aseguradoras,
      name: 'Aseguradoras',
    },
    /** AJUSTES / TIPOS - Se definen las rutas de este menu */
    {
      path: '/tiposagenda',
      component: TiposAgenda,
      name: 'Tipos Agenda',
    },
    {
      path: '/tiposconsulta',
      component: TiposConsulta,
      name: 'Tipos Consulta',
    },
    /** AJUSTES / PARAMETROS - Se definen las rutas de este menu */
    {
      path: '/notastecnicas',
      component: NotasTecnicas,
      name: 'Notas Tecnicas',
    },
    {
      path: '/pgp',
      component: Pgp,
      name: 'PGP',
    },
    {
      path: '/perfiles',
      component: Perfiles,
      name: 'Perfiles',
    },
    
    


    { path: '*', redirect: { name: 'TableroPrincipal' } },
  ],
}
