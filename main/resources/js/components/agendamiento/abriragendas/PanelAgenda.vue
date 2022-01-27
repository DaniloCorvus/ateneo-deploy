<template>
  <div>
    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body"></div>

          <div class="panel-body">
            <div class="row p-3">
              <div class="col-md-4 my-2">
                <label>Tipo de Agenda</label>
                <model-select
                  :options="type_appointments"
                  v-model="appointment"
                  placeholder="Seleccione"
                ></model-select>
              </div>

              <div class="col-md-4 my-2" v-if="appointment.value != ''">
                <label>Tipo de Consulta</label>
                <model-select
                  :options="type_subappointments"
                  v-model="subappointment"
                  placeholder="Seleccione"
                ></model-select>
              </div>

              <div class="col-md-4 my-2" v-if="appointment.brand && subappointment.value != '' ">
                <label>Ips</label>
                <model-select :options="ipss" v-model="ips" placeholder="Seleccione"></model-select>
              </div>

              <div class="col-md-4 my-2" v-if="ips">
                <label>Sede</label>
                <model-select :options="sedes" v-model="sede" placeholder="Seleccione"></model-select>
              </div>

              <div class="col-md-4 my-2" v-if="sede">
                <label>Especialidad</label>
                <model-select :options="specialties" v-model="speciality" placeholder="Seleccione"></model-select>
              </div>

              <div class="col-md-4 my-2" v-if="speciality">
                <label>Profesional</label>
                <model-select
                  :options="profesionals"
                  v-model="profesional"
                  placeholder="Seleccione"
                ></model-select>
              </div>

              <div class="col-md-3 my-2" v-if="profesional">
                <label>Inicio de Agendamiento</label>
                <input
                  class="form-control"
                  type="date"
                  name="date"
                  value="2018-01-01"
                  min="2017-01-01"
                  v-model="fechaInicio"
                />
              </div>

              <div class="col-md-3 my-2" v-if="profesional">
                <label>Fin de Agendamiento</label>
                <input
                  class="form-control"
                  type="date"
                  name="date"
                  value="2018-01-01"
                  min="2017-01-01"
                  v-model="fechaFin"
                />
              </div>

              <div class="col-md-3 my-2" v-if="profesional">
                <label>Hora Inicio</label>
                <input
                  class="form-control"
                  v-model="horaInicio"
                  type="time"
                  name="time"
                  value="08:00"
                />
              </div>

              <div class="col-md-3 my-2" v-if="profesional">
                <label>Hora Fin</label>
                <input class="form-control" v-model="horaFin" type="time" name="time" value="18:00" />
              </div>

              <div class="col-md-3 my-2" v-if="profesional">
                <label>Duracion Por consulta</label>
                <model-select
                  :options="optionesTime"
                  v-model="timeDuration"
                  placeholder="Seleccione"
                ></model-select>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-12">
        <div class="card">
          <div class="card-body">
            <div class="app-calendar">
              <abrir-agenda></abrir-agenda>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { required } from "vuelidate/lib/validators";
import AbrirAgenda from "./AbrirAgendas";
import { ModelSelect } from "vue-search-select";

/**
 * Calendar component
 */
export default {
  page: {
    title: "Calendar",
    meta: [{ name: "description" }]
  },
  components: { AbrirAgenda, ModelSelect },
  data() {
    return {
      appointment: {
        value: "",
        text: "",
        brand: ""
      },
      subappointment: {
        value: "",
        text: "",
        company_owner: "",
        procedure: ""
      },
      ips: {
        value: "",
        text: ""
      },
      sede: {
        value: "",
        text: ""
      },
      speciality: {
        value: "",
        text: ""
      },
      profesional: {
        value: "",
        text: ""
      },

      timeDuration: "",
      type_appointments: [],
      type_subappointments: [],
      ipss: [],
      sedes: [],
      specialties: [],
      profesionals: [],
      optionesTime: [{ value: 5, text: "5 Minutos" }]
    };
  },

  validations: {
    event: {
      title: { required },
      category: { required }
    }
  },

  methods: {
    reset() {
      this.ips = {
        value: "",
        text: ""
      };
      this.sede = {
        value: "",
        text: ""
      };
      this.speciality = {
        value: "",
        text: ""
      };
      this.profesional = {
        value: "",
        text: ""
      };
      this.subappointment = {
        value: "",
        text: ""
      };
    },

    getTypeAppointment() {
      axios
        .get(`api/get-type_appointments/${this.appointment.text}`)
        .then(resp => {
          this.type_appointments = resp.data.data;
        });
    },

    getSubTypeAppointment() {
      axios
        .get(`api/get-type_subappointments/${this.appointment.value}`)
        .then(resp => {
          this.type_subappointments = resp.data.data;
        });
    },

    getIps() {
      axios
        .get(`api/get-ips/${this.subappointment.company_owner}`)
        .then(resp => {
          this.ipss = resp.data.data;
        });
    },

    getSedes() {
      axios
        .get(`api/get-sedes/${this.ips.value}/${this.subappointment.procedure}`)
        .then(resp => {
          this.sedes = resp.data.data;
        });
    },

    getSpecialties() {
      axios
        .get(
          `api/get-specialties/${this.sede.value}/${this.subappointment.procedure}`
        )
        .then(resp => {
          this.specialties = resp.data.data;
        });
    },
    getProfesionals() {
      axios
        .get(`api/get-profesionals/${this.ips.value}/${this.speciality.value}`)
        .then(resp => {
          this.profesionals = resp.data.data;
        });
    }
  },

  mounted() {
    this.getTypeAppointment();
  },

  watch: {
    appointment(val) {
      this.reset();
      if (val) this.getSubTypeAppointment();
    },
    subappointment(val) {
      if (val) this.getIps();
    },
    ips(val) {
      if (val) this.getSedes();
    },
    sede(val) {
      if (val) this.getSpecialties();
    },
    speciality(val) {
      if (val) this.getProfesionals();
    }
  }
};
</script>
