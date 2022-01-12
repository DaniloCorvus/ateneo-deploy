<template>
    <div class="row">
      <div class="col-4">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title mb-4">Paciente x Régimen</h4>
            <!-- Donut Chart -->
            <apexchart
              class="apex-charts"
              height="320"
              type="donut"
              dir="ltr"
              :series="donutChart.series"
              :options="donutChart.chartOptions"
            ></apexchart>
          </div>
        </div>
        <div class="card">
          <div class="card-body">
            <h4 class="card-title mb-4">Paciente x Dpto.</h4>
            <!-- Bar Chart -->
            <apexchart
              class="apex-charts"
              height="350"
              type="bar"
              dir="ltr"
              :series="barChart.series"
              :options="barChart.chartOptions"
            ></apexchart>
          </div>
        </div>

      </div>

      <div class="col-8">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Pacientes Registrados</h4>
            <div class="row mt-4">
            </div>
            <div class="row mt-4">
              <div class="col-sm-12 col-md-3">
                <div id="tickets-table_length" class="dataTables_length">
                  <label class="d-inline-flex align-items-center">
                    Mostrar&nbsp;
                    <b-form-select v-model="perPage" size="sm" :options="pageOptions"></b-form-select>&nbsp;items
                  </label>
                </div>
              </div>
              
              <div class="col-sm-12 col-md-5">
                <button class="btn btn-sm btn-info float-right"><i class="fa fa-plus"></i> Agergar Paciente</button>
                <button class="btn btn-sm btn-info float-right mr-1"><i class="fa fa-upload"></i> Importar </button>
              </div>
              <div class="col-sm-12 col-md-4">
                <div id="tickets-table_filter" class="dataTables_filter text-md-right">
                  <label class="d-inline-flex align-items-center">
                    Buscar:
                    <b-form-input
                      v-model="filter"
                      type="search"
                      placeholder="Buscar..."
                      class="form-control form-control-sm ml-2"
                    ></b-form-input>
                  </label>
                </div>
              </div>
              <!-- End search -->
            </div>
            <!-- Table -->
            <div class="table-responsive text-10">
              <b-table striped hover small
                :items="Pacientes"
                :fields="fields"
                responsive="sm"
                :per-page="perPage"
                :current-page="currentPage"
                :sort-by.sync="sortBy"
                :sort-desc.sync="sortDesc"
                :filter="filter"
                :filter-included-fields="filterOn"
                @filtered="onFiltered"
              >
              <template #cell(Paciente)="row">
                {{ row.item.Nombres }} {{ row.item.Apellidos }}
              </template>
              <template #cell(actions)="row">
                <b-dropdown text="Acciones" @id="row.index" variant="primary" size="sm" class="m-1 text-10">
                    <template slot="button-content">
                      Acciones
                      <i class="mdi mdi-chevron-down"></i>
                    </template>
                    <b-dropdown-item><i class="fa fa-search"></i> Ver</b-dropdown-item>
                    <b-dropdown-item><i class="fa fa-pencil-alt"></i> Editar</b-dropdown-item>
                    <b-dropdown-item><i class="fa fa-times-circle"></i> Suspender</b-dropdown-item>
                </b-dropdown>
              </template>
              </b-table>
            </div>
            <div class="row">
              <div class="col">
                <div class="dataTables_paginate paging_simple_numbers float-right">
                  <ul class="pagination pagination-rounded mb-0">
                    <!-- pagination -->
                    <b-pagination v-model="currentPage" :total-rows="rows" :per-page="perPage"></b-pagination>
                  </ul>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
</template>
<script>
  import {  barChart, donutChart  } from "./data-apex";
  export default {
    data() {
      return {
        donutChart: donutChart,
        barChart: barChart,


        Pacientes:[],
        Total:0,
        tableData: this.Pacientes,
        totalRows: 1,
        currentPage: 1,
        perPage: 20,
        pageOptions: [20, 50, 100, 500],
        filter: null,
        filterOn: [],
        sortBy: "nombre",
        sortDesc: false,
        fields: [
          { key: "Identificacion", label:'Identif.', sortable: true },
          { key: "Paciente", sortable: true },
          { key: "EPS", sortable: true },
          { key: "Regimen", sortable: true },
          { key: "Estado", sortable: true },
          { key: 'actions', variant: 'acciones' }
        ]
      };
    },
    computed: {
      /**
       * Total no. of records
       */
      rows() {
        return this.Total;
      }
    },
    mounted() {
      this.getAllDatos();
      // Set the initial number of items
      
    },
    methods: {
      /**
       * Search the table data with search input
       */
      getAllDatos(){
          axios
              .all([
              this.getPacientes(),
              ])
              .then(
                axios.spread( 
                    (
                    PacientesDatos,
                    ) => {
                    this.Total=PacientesDatos.data.pacientes.total;
                    this.Pacientes = PacientesDatos.data.pacientes.data;
                    }
                )
              );
      },
      getPacientes(){
          return axios.get(
              `/api/pacientes/listapacientes`
          );
      },
      onFiltered(filteredItems) {
        // Trigger pagination to update the number of buttons/pages due to filtering
        this.totalRows = filteredItems.length;
        this.currentPage = 1;
      }
    }
  }
</script>

<style>
.text-10{
  font-size:10px !important;
}
.text-10 button{
  font-size:10px !important;
  line-height: 10px !important;
}
td.table-acciones{
  max-width: 100px !important; 
  width: 100px !important; 
  padding: 0 !important;
  text-align: center;
}
</style>


<th>Identif.</th>
<th>Paciente</th>
<th>Telefono</th>
<th>Correo</th>
<th>EPS</th>
<th>Nivel</th>
<th>Regimen</th>
<th style="width: 80px;">Estado</th>
<th>Acciones</th>


