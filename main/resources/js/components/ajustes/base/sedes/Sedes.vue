<template>
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <h4 class="card-title">Listado de Sedes</h4>
            <div class="row mt-4">
              <div class="col-sm-12 col-md-6">
                <div id="tickets-table_length" class="dataTables_length">
                  <label class="d-inline-flex align-items-center">
                    Mostrar&nbsp;
                    <b-form-select v-model="perPage" size="sm" :options="pageOptions"></b-form-select>&nbsp;items
                  </label>
                </div>
              </div>
              <!-- Search -->
              <div class="col-sm-12 col-md-6">
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
            <div class="table-responsive mb-0">
              <b-table striped hover
                :items="tableData"
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
              <template #cell(actions)="row">
                <b-dropdown text="Acciones" @id="row.index" variant="primary" size="sm" class="m-2">
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
  import { tableData } from "./data";
  export default {
    data() {
      return {
        tableData: tableData,
        totalRows: 1,
        currentPage: 1,
        perPage: 10,
        pageOptions: [10, 25, 50, 100],
        filter: null,
        filterOn: [],
        sortBy: "codigo",
        sortDesc: false,
        fields: [
          { key: "empresa", label:'Empresa', sortable: true },
          { key: "nombre", sortable: true },
          { key: "codigo", sortable: true },
          { key: 'actions', variant: 'acciones' }
        ]
      };
    },
    computed: {
      /**
       * Total no. of records
       */
      rows() {
        return this.tableData.length;
      }
    },
    mounted() {
      // Set the initial number of items
      
    },
    methods: {
      /**
       * Search the table data with search input
       */
      onFiltered(filteredItems) {
        // Trigger pagination to update the number of buttons/pages due to filtering
        this.totalRows = filteredItems.length;
        this.currentPage = 1;
      }
    }
  }
</script>

<style>
td.table-acciones{
  max-width: 100px !important; 
  width: 100px !important; 
  padding: 0 !important;
  text-align: center;
}
</style>
