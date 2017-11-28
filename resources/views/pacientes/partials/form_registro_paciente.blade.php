<div class="modal fade" id="modal-registro-paciente" data-keyboard="false" data-backdrop="static" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-full" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" v-text="modal.title"></h4>
            </div>
            <div class="modal-body">
                <form id="form-paciente">
                    @include('pacientes.partials.inputs_registro_paciente')
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal" >Cancelar</button>
                <buttonsave id="submit_paciente" class_btn="btn-primary" form="form-paciente" url="/pacientes/guardar-pacientes" :object="paciente" @response="guardarPaciente"></buttonsave>
            </div>
        </div>
    </div>
</div>