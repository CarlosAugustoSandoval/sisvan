
<div class="modal fade" id="modal-registro-consulta" data-keyboard="false" data-backdrop="static" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-full" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary">
                <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" v-text="modal.title"></h4>
            </div>
            <div class="modal-body">
                <form id="form-consulta">
                    <ul class="nav nav-tabs nav-primary nav-tabs-consulta">
                        <li class="active"><a href="#tab2_1" data-toggle="tab"><i :class="paciente.genero=='Masculino'?'icon-user':'icon-user-female'"></i> Datos del paciente</a></li>
                        <li class=""><a href="#tab2_2" data-toggle="tab"><i class="glyphicon glyphicon-list-alt"></i> Datos de la consulta</a></li>
                    </ul>
                    <div class="tab-content">
                        <div class="tab-pane fade active in" id="tab2_1">
                            @include('pacientes.partials.inputs_registro_paciente')
                        </div>
                        <div class="tab-pane fade" id="tab2_2">
                            <div class="row">
                                <div class="col-md-2 col-sm-4 col-xs-6" v-for="(variable,index) in paciente.rango_edad.variable">
                                    <div class="form-group">
                                        <label>@{{ variable.nombre }}</label>
                                        <input v-if="variable.tipo_input=='number'" class="form-control form-white" type="number"  :placeholder="variable.nombre" v-model.number="consulta.detalle_consulta[index].valor" />
                                        <select-v2 v-if="variable.tipo_input=='select'"
                                                :updatevalue="consulta.detalle_consulta[index].valor"
                                                @change:updatevalue="val => consulta.detalle_consulta[index].valor = val"
                                                optionkey="valor"
                                                optiontext="valor"
                                                :objeto="variable.valor"
                                                :placeholder="variable.nombre"
                                                v-model="consulta.detalle_consulta[index].valor"
                                                :data-vv-name="variable.nombre"
                                                v-validate="'required'">
                                        </select-v2>
                                        {{--<switch-bs v-if="variable.tipo_input=='radio'" :id="variable.tipo_input+index" :value="paciente.rango_edad.variable[index].valor" @update:value="val => paciente.rango_edad.variable[index].valor = val"></switch-bs>--}}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal" >Cancelar</button>
                <buttonsave id="submit_consulta" class_btn="btn-primary" form="form-consulta" url="/pacientes/guardar-consultas" :object="paciente" @response="guardarPaciente"></buttonsave>
            </div>
        </div>
    </div>
</div>