<template>
  <div class="documentos">
        <file-manager v-bind:settings="settings"></file-manager>
    </div>
</template>

<script>
export default {
    data(){
        return{
            settings: {
                // axios headers
                /*headers: {
                'X-Requested-With': 'XMLHttpRequest',
                Authorization: `Bearer ${window.localStorage.getItem('user-token')}`,
                },*/
                baseUrl: '/file-manager/',   // overwrite base url Axios
                windowsConfig: 1,                           // overwrite config
                lang: 'es',                                 // set language
                
            }
        }
    },
    
}
</script>

<style scoped>
.documentos{
    min-height: 700px;
    height:700px;
}
</style>