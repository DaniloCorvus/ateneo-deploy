<template>
  <div>
      <div class="row">
        <div class="col-lg-6">
          <div class="card">
            <div class="card-body">
              <h4 class="card-title mb-4">Pacientes por Edades y Sexo</h4>
              <piramide :datosuno="PacientesEdadSexo"></piramide>
            </div>
          </div>
        </div>

        <div class="col-lg-6">
          <div class="card">
            <div class="card-body">
              <h4 class="card-title mb-4">Pacientes por Patologia y Sexo (Top 5)</h4>
              <radial :datosuno="PacientesPatologiaSexo"></radial>
            </div>
          </div>
        </div>


      </div>
      
  </div>
</template>

<script>
import Piramide from './Piramide'
import Radial from './Radial'

export default {
  components: {
    Piramide,
    Radial
  },
  data() {
      return {
        PacientesEdadSexo: [],
        PacientesPatologiaSexo: [],
      }
  },
  created() {
        this.getAllDatos();
  },
  methods:{
        getAllDatos(){
            axios
                .all([
                this.getPacientesEdadSexo(),
                this.getPacientesPatologiaSexo(),
                ])
                .then(
                axios.spread( 
                    (
                    PacientesEdadSexoDatos,
                    PacientesPatologiaSexoDatos,
                    ) => {
                    this.PacientesEdadSexo = PacientesEdadSexoDatos.data;
                    this.PacientesPatologiaSexo = PacientesPatologiaSexoDatos.data;
                    }
                )
                );
        },
        getPacientesEdadSexo(){
            return axios.get(
                `/api/caracterizacion/pacientesedadsexo`
            );
        },
        getPacientesPatologiaSexo(){
            return axios.get(
                `/api/caracterizacion/pacientespatologiasexo`
            );
        }
    }


}
</script>

<style>
  
</style>