<div class="modal fade" id="modal-excel-consulta" data-keyboard="false" data-backdrop="static" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-sm" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" v-text="modal.title"></h4>
            </div>
            <div class="modal-body">
                <form id="form-excel_consulta" data-vv-scope="excel_consulta">
                    <div class="form-group">
                        <label>Semana epidemiológica</label>
                        <input type="week"
                               :max="laSemana.maxWeek"
                               class="form-control form-white"
                               placeholder="Semana epidemiológica"
                               v-model="laSemana.excel_semana"
                               data-vv-name="Semana epidemiológica"
                               v-validate="'required'"
                               step="0.1">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal" >Cerrar</button>
                {{--<a :href="laRutaSemana" class="btn btn-outline-secondary" >Cerrar</a>--}}
                <buttonsave id="submit_excel_consulta" class_btn="btn-primary" form="form-excel_consulta" @response="exportado" url="/pacientes/excel-consulta" :object="laSemana" v_scope="excel_consulta" icon_class="fa fa-file-excel-o" label=" Exportar"></buttonsave>
            </div>
        </div>
    </div>
</div>