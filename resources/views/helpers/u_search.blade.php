<script type="text/x-template" id="usearch">
    <form class="input-group" v-on:submit.prevent="buscar()">
        <input type="text" class="form-control" v-model="value.busqueda" placeholder="Buscar..." aria-label="Filtro">
        <span class="input-group-btn">
                <button type="submit" class=" ladda-button btn btn-primary ladda-search" data-style="expand-right">
                    <span class="ladda-label">Buscar</span>
                    <span class="ladda-spinner"></span>
                </button>
            </span>
    </form>
</script>
<script>
    Vue.component('usearch', {
        template: '#usearch',
        props:['url', 'object','value'],
        data: function () {
            return {
                ladabutton:'',
                filtrado:false
            }
        },
        watch: {
            object: function() {
                this.ladabutton.stop();
            },
            'value.busqueda'(val) {
                if(val === '' && this.filtrado){
                    this.limpiarBusqueda();
                }
            }
        },
        methods:{
            buscar(){
                this.filtrado=true;
                this.ladabutton.start();
                this.$parent.$refs.vpaginator.fetchData(this.url);
            },
            limpiarBusqueda(){
                this.value.busqueda = '';
                this.ladabutton.start();
                this.$parent.$refs.vpaginator.fetchData(this.url);
                this.filtrado=false;
            },
        },
        mounted(){
            this.ladabutton = Ladda.create(document.querySelector('.ladda-search'));
        }
    });
</script>