<template>
  <div class="row">
      <div class="col-xl-3 col-lg-4">
        <div class="card">
          <div class="card-header bg-transparent border-bottom">
            <h5 class="mb-0">Filtros</h5>
          </div>
          <div class="custom-accordion">
            <div class="card-body border-top">
              <div>
                <h5 class="font-size-14 mb-0">
                  <a v-b-toggle.discount class="text-dark d-block" data-toggle="collapse">
                    Empresa
                    <i class="mdi mdi-minus float-right accor-plus-icon"></i>
                  </a>
                </h5>
                <b-collapse visible id="discount">
                  <div class="mt-4">
                    <div class="custom-control custom-checkbox mt-2">
                      <input
                        type="checkbox"
                        id="productdiscountRadio6"
                        name="productdiscountRadio1"
                        class="custom-control-input"
                      />
                      <label class="custom-control-label" for="productdiscountRadio6">HEMOPLIFE</label>
                    </div>
                    <div class="custom-control custom-checkbox mt-2">
                      <input
                        type="checkbox"
                        id="productdiscountRadio5"
                        name="productdiscountRadio1"
                        class="custom-control-input"
                      />
                      <label class="custom-control-label" for="productdiscountRadio5">MEGSALUD</label>
                    </div>
                    <div class="custom-control custom-checkbox mt-2">
                      <input
                        type="checkbox"
                        id="productdiscountRadio4"
                        name="productdiscountRadio1"
                        class="custom-control-input"
                      />
                      <label class="custom-control-label" for="productdiscountRadio4">ECOMEDIS</label>
                    </div>
                    <div class="custom-control custom-checkbox mt-2">
                      <input
                        type="checkbox"
                        id="productdiscountRadio3"
                        name="productdiscountRadio1"
                        class="custom-control-input"
                      />
                      <label class="custom-control-label" for="productdiscountRadio3">MEDISERRANO</label>
                    </div>
                    <div class="custom-control custom-checkbox mt-2">
                      <input
                        type="checkbox"
                        id="productdiscountRadio2"
                        name="productdiscountRadio1"
                        class="custom-control-input"
                      />
                      <label class="custom-control-label" for="productdiscountRadio2">SALUDVITAL</label>
                    </div>
                    <div class="custom-control custom-checkbox mt-2">
                      <input
                        type="checkbox"
                        id="productdiscountRadio1"
                        name="productdiscountRadio1"
                        class="custom-control-input"
                      />
                      <label class="custom-control-label" for="productdiscountRadio1">VIDASER</label>
                    </div>
                  </div>
                </b-collapse>
              </div>
            </div>

            <div class="card-body border-top">
              <div>
                <h5 class="font-size-14 mb-0">
                  <a v-b-toggle.size class="text-dark d-block" data-toggle="collapse">
                    Estado
                    <i class="mdi mdi-minus float-right accor-plus-icon"></i>
                  </a>
                </h5>
                <b-collapse visible id="size">
                  <div class="mt-4">
                    <div class="custom-control custom-checkbox mt-2">
                      <input
                        type="checkbox"
                        id="productsizeRadio1"
                        name="productsizeRadio"
                        class="custom-control-input"
                      />
                      <label class="custom-control-label" for="productsizeRadio1">Activo</label>
                    </div>
                    <div class="custom-control custom-checkbox mt-2">
                      <input
                        type="checkbox"
                        id="productsizeRadio2"
                        name="productsizeRadio"
                        class="custom-control-input"
                      />
                      <label class="custom-control-label" for="productsizeRadio2">Inactivo</label>
                    </div>
                    <div class="custom-control custom-checkbox mt-2">
                      <input
                        type="checkbox"
                        id="productsizeRadio3"
                        name="productsizeRadio"
                        class="custom-control-input"
                      />
                      <label class="custom-control-label" for="productsizeRadio3">Liquidado</label>
                    </div>
                    <div class="custom-control custom-checkbox mt-2">
                      <input
                        type="checkbox"
                        id="productsizeRadio4"
                        name="productsizeRadio"
                        class="custom-control-input"
                      />
                      <label class="custom-control-label" for="productsizeRadio4">Suspendido</label>
                    </div>
                  </div>
                </b-collapse>
              </div>
            </div>
            <div class="card-body border-top">
              <div>
                <h5 class="font-size-14 mb-0">
                  <a
                    v-b-toggle.customrating
                    class="collapsed text-dark d-block"
                    data-toggle="collapse"
                  >
                    Departamento
                    <i class="mdi mdi-minus float-right accor-plus-icon"></i>
                  </a>
                </h5>
                <b-collapse visible id="customrating">
                  <div class="mt-4">
                    <div class="custom-control custom-checkbox mt-2">
                      <input
                        type="checkbox"
                        id="productratingRadio1"
                        name="productratingRadio1"
                        class="custom-control-input"
                      />
                      <label class="custom-control-label" for="productratingRadio1">
                        Administrativo
                      </label>
                    </div>
                    <div class="custom-control custom-checkbox mt-2">
                      <input
                        type="checkbox"
                        id="productratingRadio2"
                        name="productratingRadio1"
                        class="custom-control-input"
                      />
                      <label class="custom-control-label" for="productratingRadio2">
                        Asistencial
                      </label>
                    </div>
                    <div class="custom-control custom-checkbox mt-2">
                      <input
                        type="checkbox"
                        id="productratingRadio3"
                        name="productratingRadio1"
                        class="custom-control-input"
                      />
                      <label class="custom-control-label" for="productratingRadio3">
                        Operativo
                      </label>
                    </div>
                    <div class="custom-control custom-checkbox mt-2">
                      <input
                        type="checkbox"
                        id="productratingRadio4"
                        name="productratingRadio1"
                        class="custom-control-input"
                      />
                      <label class="custom-control-label" for="productratingRadio4">
                        Apoyo
                      </label>
                    </div>
                  </div>
                </b-collapse>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-xl-9 col-lg-8">
        <div class="card">
          <div class="card-body">
            <div>
              <div class="row mb-2">
                <div class="col-md-6">
                  <div>
                    <h5>Funcionarios</h5>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="form-inline float-md-right">
                    <div class="search-box ml-2">
                      <div class="position-relative">
                        <input type="text" class="form-control rounded" placeholder="Buscar..." />
                        <i class="mdi mdi-magnify search-icon"></i>
                      </div>
                    </div>
                  </div>
                </div>
              </div>


              <div class="row no-gutters">
                <div class="col-xl-3 col-sm-4">
                  <div class="product-box">
                    <div class="product-img">
                      
                      <img
                        src="https://i2.wp.com/anuario.misionesonline.net/wp-content/uploads/2018/01/300.png?w=720&ssl=1"
                        alt
                        class="img-fluid mx-auto d-block"
                      />
                    </div>

                    <div class="text-center">
                      <p class="font-size-12 mb-1">HEMOPLIFE</p>
                      <h5 class="font-size-15">
                        <a href="#" class="text-dark">Augusto Carrillo</a>
                      </h5>
                    </div>
                  </div>
                </div>

                <div class="col-xl-3 col-sm-4">
                  <div class="product-box">
                    <div class="product-img">
                      
                      <img
                        src="https://i2.wp.com/anuario.misionesonline.net/wp-content/uploads/2018/01/300.png?w=720&ssl=1"
                        alt
                        class="img-fluid mx-auto d-block"
                      />
                    </div>

                    <div class="text-center">
                      <p class="font-size-12 mb-1">MEGSALUD</p>
                      <h5 class="font-size-15">
                        <a href="#" class="text-dark">Geiny Reyes</a>
                      </h5>
                    </div>
                  </div>
                </div>
                <div class="col-xl-3 col-sm-4">
                  <div class="product-box">
                    <div class="product-img">
                      
                      <img
                        src="https://i2.wp.com/anuario.misionesonline.net/wp-content/uploads/2018/01/300.png?w=720&ssl=1"
                        alt
                        class="img-fluid mx-auto d-block"
                      />
                    </div>

                    <div class="text-center">
                      <p class="font-size-12 mb-1">HEMOPLIFE</p>
                      <h5 class="font-size-15">
                        <a href="#" class="text-dark">Xiomara Torres</a>
                      </h5>
                    </div>
                  </div>
                </div>
                <div class="col-xl-3 col-sm-4">
                  <div class="product-box">
                    <div class="product-img">
                      
                      <img
                        src="https://i2.wp.com/anuario.misionesonline.net/wp-content/uploads/2018/01/300.png?w=720&ssl=1"
                        alt
                        class="img-fluid mx-auto d-block"
                      />
                    </div>

                    <div class="text-center">
                      <p class="font-size-12 mb-1">MEGSALUD</p>
                      <h5 class="font-size-15">
                        <a href="#" class="text-dark">Ciro González</a>
                      </h5>
                    </div>
                  </div>
                </div>

                <div class="col-xl-3 col-sm-4">
                  <div class="product-box">
                    <div class="product-img">
                      
                      <img
                        src="https://i2.wp.com/anuario.misionesonline.net/wp-content/uploads/2018/01/300.png?w=720&ssl=1"
                        alt
                        class="img-fluid mx-auto d-block"
                      />
                    </div>

                    <div class="text-center">
                      <p class="font-size-12 mb-1">HEMOPLIFE</p>
                      <h5 class="font-size-15">
                        <a href="#" class="text-dark">Luz Amanda Morales</a>
                      </h5>
                    </div>
                  </div>
                </div>
                <div class="col-xl-3 col-sm-4">
                  <div class="product-box">
                    <div class="product-img">
                      
                      <img
                        src="https://i2.wp.com/anuario.misionesonline.net/wp-content/uploads/2018/01/300.png?w=720&ssl=1"
                        alt
                        class="img-fluid mx-auto d-block"
                      />
                    </div>

                    <div class="text-center">
                      <p class="font-size-12 mb-1">MEGSALUD</p>
                      <h5 class="font-size-15">
                        <a href="#" class="text-dark">Mauricio Molina</a>
                      </h5>
                    </div>
                  </div>
                </div>
              </div>

              <div class="row mt-4">
                <div class="col-sm-6">
                  <div>
                    <p class="mb-sm-0 mt-2">
                      Página
                      <span class="font-weight-bold">1</span> de
                      <span class="font-weight-bold">1</span>
                    </p>
                  </div>
                </div>
                <div class="col-sm-6">
                  <div class="float-sm-right">
                    <ul class="pagination pagination-rounded mb-sm-0">
                      <li class="page-item disabled">
                        <a href="#" class="page-link">
                          <i class="mdi mdi-chevron-left"></i>
                        </a>
                      </li>
                      <li class="page-item active">
                        <a href="#" class="page-link">1</a>
                      </li>
                      <li class="page-item ">
                        <a href="#" class="page-link">2</a>
                      </li>
                      <li class="page-item">
                        <a href="#" class="page-link">3</a>
                      </li>
                      <li class="page-item">
                        <a href="#" class="page-link">4</a>
                      </li>
                      <li class="page-item">
                        <a href="#" class="page-link">5</a>
                      </li>
                      <li class="page-item">
                        <a href="#" class="page-link">
                          <i class="mdi mdi-chevron-right"></i>
                        </a>
                      </li>
                    </ul>
                  </div>
                </div>
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
      title: "Products",
      items: [
        {
          text: "Ecommerce"
        },
        {
          text: "Products",
          active: true
        }
      ],
    };
  }
}
</script>

<style>
.product-box{
  margin:5px !important;
}
</style>



