
<template>
  <div>
    <div>
      <div class="container-fluid p-0">
        <div class="row no-gutters">
          <div class="col-lg-4">
            <div class="authentication-page-content p-4 d-flex align-items-center min-vh-100">
              <div class="w-100">
                <div class="row justify-content-center">
                  <div class="col-lg-9">
                    <div>
                      <div class="text-center">
                        <div>
                          <a href="/" class="logo">
                            <img src="@/assets/images/logo-dark.png" height="45" alt="logo" />
                          </a>
                        </div>

                        <h4 class="font-size-18 mt-4">Bienvenido </h4>
                        <p class="text-muted">Inicia sesión para continuar</p>
                      </div>

                      <div class="p-2 mt-5">
                        <form class="form-horizontal" @submit.prevent="validarAntesDeEnviar">
                          <div class="form-group auth-form-group-custom mb-4">
                            <i class="ri-account-box-line auti-custom-input-icon"></i>
                            <label for="usuario">Usuario</label>
                            <input
                              type="text"
                              v-model="formulario.usuario"
                              v-validate="'required|integer'"
                              class="form-control"
                              id="usuario"
                              name="usuario"
                              placeholder="Ingrese Identificación"
                              :class="{ 'is-invalid': (errors.first('usuario')) }"
                            /> 
                            <div class="invalid-feedback">
                              <span>{{ errors.first("usuario") }}</span>
                            </div>
                          </div>

                          <div class="form-group auth-form-group-custom mb-4">
                            <i class="ri-lock-2-line auti-custom-input-icon"></i>
                            <label for="password">Contraseña</label>
                            <input
                              v-model="formulario.password"
                              v-validate="'required|min:6'"
                              type="password"
                              class="form-control"
                              id="password"
                              name="password"
                              placeholder="Ingrese Contraseña"
                              :class="{ 'is-invalid': (errors.first('password')) }"
                            />
                            <div
                              class="invalid-feedback"
                            >{{ errors.first("password") }}</div>
                          </div>

                          <div class="custom-control custom-checkbox">
                            <input
                              type="checkbox"
                              class="custom-control-input"
                              id="customControlInline"
                            />
                            <label
                              class="custom-control-label"
                              for="customControlInline"
                            >Recordarme</label>
                          </div>

                          <div class="mt-4 text-center">
                            <button
                              class="btn btn-primary w-md waves-effect waves-light"
                              type="submit"
                            >Iniciar Sesión</button>
                          </div>

                          <div class="mt-4 text-center">
                            <router-link tag="a" to="/forgot-password" class="text-muted">
                              <i class="mdi mdi-lock mr-1"></i> Recuperar Contraseña
                            </router-link>
                          </div>
                        </form>
                      </div>
                      <notifications
                        group="notificaciones"
                        position="top left"
                        :width="500"
                        />

                      <div class="mt-5 text-center">
                        <p>
                          © 2021 ATENEO ERP
                        </p>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-lg-8">
            <div class="authentication-bg" >
              <div class="bg-overlay"></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>

export default {
  data() {
    return {
      formulario: {
        usuario: "",
        password: ""
      },
      submitted: false
    };
  },
  computed: {
    notification() {
      return this.$store ? this.$store.state.notification : null;
    }
  },
  created() {
    document.body.classList.add("auth-body-bg");
  },
  methods: {
    async validarAntesDeEnviar() {

      let validado = await this.$validator.validateAll();
      if (validado) {
        this.iniciarSesion();
        return;
      }
      
      this.$notify({
        group: "notificaciones",
        title: "Error",
        text: "Debe corregir los errores antes de iniciar sesión",
        type: "error"
      });
    },
    iniciarSesion() {
      axios
        .post("/api/auth/login", this.$data.formulario)
        .then(datos => {
          localStorage.setItem("token", datos.data.token);
          localStorage.setItem("usuario", JSON.stringify(datos.data.User));
          eventEmitter.$emit("autenticado");
          this.$router.push("/tablero");
        })
        .catch(error => {
          if (error.response.status == 401) {
            this.$notify({
              group: "notificaciones",
              title: "Error",
              text: (error.response.data.status) ? (error.response.data.status) : "Credenciales inválidas, intente nuevamente",
              type: "error"
            });
          }
        });
    },
    
    tryToLogIn() {
      this.submitted = true;
      console.log("entre aca", this.$v)
      
      this.$v.$touch();

      if (this.$v.$invalid) {
        return;
      } else {
        if (process.env.VUE_APP_DEFAULT_AUTH === "firebase") {
          this.tryingToLogIn = true;
          // Reset the authError if it existed.
          this.authError = null;
          return (
            this.logIn({
              email: this.email,
              password: this.password
            })
              // eslint-disable-next-line no-unused-vars
              .then(token => {
                this.tryingToLogIn = false;
                this.isAuthError = false;
                // Redirect to the originally requested page, or to the home page
                this.$router.push(
                  this.$route.query.redirectFrom || { name: "home" }
                );
              })
              .catch(error => {
                this.tryingToLogIn = false;
                this.authError = error ? error : "";
                this.isAuthError = true;
              })
          );
        } else {
          const { email, password } = this;
          if (email && password) {
            this.login({ email, password });
          }
        }
      }
    },
  }
};
</script>

<style>

</style>
