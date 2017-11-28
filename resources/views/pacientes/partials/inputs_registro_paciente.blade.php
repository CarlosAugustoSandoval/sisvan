<div class="row">
    <div class="col-sm-6 col-md-3">
        <div class="form-group">
            <label>Tipo identificación</label>
            <select-v2
                    :updatevalue="paciente.tipo_identificacion_id"
                    @change:updatevalue="val => paciente.tipo_identificacion_id = val"
                    :objeto="tiposIdentificacion"
                    optionkey="id"
                    optiontext="nombre"
                    placeholder="Tipo identificación"
                    v-model="paciente.tipo_identificacion_id"
                    data-vv-name="Tipo de identificación"
                    v-validate="'required'">
            </select-v2>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="form-group">
            <label>Identificación</label>
            <input type="text" v-model="paciente.identificacion" class="form-control form-white" placeholder="Identificación" data-vv-name="Identificación" v-validate="'required'">
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="form-group">
            <label>Primer nombre</label>
            <input type="text" v-model="paciente.nombre1" class="form-control form-white" placeholder="Primer nombre" data-vv-name="Primer nombre" v-validate="'required'">
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="form-group">
            <label>Segundo nombre</label>
            <input type="text" v-model="paciente.nombre2" class="form-control form-white" placeholder="Segundo nombre">
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="form-group">
            <label>Primer apellido</label>
            <input type="text" v-model="paciente.apellido1" class="form-control form-white" placeholder="Primer apellido" data-vv-name="Primer apellido" v-validate="'required'">
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="form-group">
            <label>Segundo apellido</label>
            <input type="text" v-model="paciente.apellido2" class="form-control form-white" placeholder="Segundo apellido">
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="form-group">
            <label>Fecha de nacimiento</label>
            <input type="date" v-model="paciente.fecha_nacimiento" :max="maxDateBorn" class="form-control form-white input-md" placeholder="Fecha de nacimiento" data-vv-name="Fecha de nacimiento" v-validate="'required'">
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="form-group">
            <label>Género</label>
            <select-v2
                    :updatevalue="paciente.genero"
                    @change:updatevalue="val => paciente.genero = val"
                    :objeto="generos"
                    placeholder="Género"
                    v-model="paciente.genero"
                    data-vv-name="Género"
                    v-validate="'required'">
            </select-v2>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="form-group">
            <label>Área residencial</label>
            <select-v2
                    :updatevalue="paciente.tipo_area_residencial_id"
                    @change:updatevalue="val => paciente.tipo_area_residencial_id = val"
                    :objeto="tiposAreaResidencial"
                    optionkey="id"
                    optiontext="nombre"
                    placeholder="Área residencial"
                    v-model="paciente.tipo_area_residencial_id"
                    data-vv-name="Área residencial"
                    v-validate="'required'">
            </select-v2>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="form-group">
            <label>Barrio</label>
            <select-v2
                    :updatevalue="paciente.barrio_id"
                    @change:updatevalue="val => paciente.barrio_id = val"
                    :objeto="barrios"
                    optionkey="id"
                    optiontext="nombre"
                    placeholder="Barrio"
                    v-model="paciente.barrio_id"
                    data-vv-name="Barrio"
                    v-validate="'required'">
            </select-v2>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="form-group">
            <label>Dirección</label>
            <input type="text" v-model="paciente.direccion" class="form-control form-white" placeholder="Dirección">
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="form-group">
            <label>Teléfono</label>
            <input type="text" v-model="paciente.telefono" class="form-control form-white" placeholder="Teléfono">
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="form-group">
            <label>Régimen</label>
            <select-v2
                    :updatevalue="paciente.regimen_id"
                    @change:updatevalue="val => paciente.regimen_id = val"
                    :objeto="regimenes"
                    optionkey="id"
                    optiontext="nombre"
                    placeholder="Régimen"
                    v-model="paciente.regimen_id"
                    data-vv-name="Régimen"
                    v-validate="'required'">
            </select-v2>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="form-group">
            <label>Entidad</label>
            <select-v2
                    :updatevalue="paciente.ep_id"
                    @change:updatevalue="val => paciente.ep_id = val"
                    :objeto="epss"
                    optionkey="id"
                    optiontext="nombre"
                    placeholder="Entidad"
                    v-model="paciente.ep_id"
                    data-vv-name="Entidad"
                    v-validate="'required'">
            </select-v2>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="form-group">
            <label>Grupo étnico</label>
            <select-v2
                    :updatevalue="paciente.grupo_etnico_id"
                    @change:updatevalue="val => paciente.grupo_etnico_id = val"
                    :objeto="gruposEtnico"
                    optionkey="id"
                    optiontext="nombre"
                    placeholder="Grupo étnico"
                    v-model="paciente.grupo_etnico_id"
                    data-vv-name="Grupo étnico"
                    v-validate="'required'">
            </select-v2>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="form-group">
            <label>@{{ titleSubgrupoEtnico?titleSubgrupoEtnico:"Subgrupo étnico" }}</label>
            <select-v2
                    :updatevalue="paciente.subgrupo_etnico_id"
                    @change:updatevalue="val => paciente.subgrupo_etnico_id = val"
                    :objeto="subgruposEtnico"
                    :disable="subgrupoDisabled"
                    optionkey="id"
                    optiontext="nombre"
                    placeholder="Subgrupo étnico"
                    v-model="paciente.subgrupo_etnico_id"
                    data-vv-name="Subgrupo étnico"
                    v-validate="'required'">
            </select-v2>
        </div>
    </div>
    <div class="col-sm-6 col-md-3">
        <div class="form-group">
            <label>Grupo poblacional</label>
            <select-v2
                    :updatevalue="paciente.grupo_poblacional_id"
                    @change:updatevalue="val => paciente.grupo_poblacional_id = val"
                    :objeto="gruposPoblacional"
                    optionkey="id"
                    optiontext="nombre"
                    placeholder="Grupo poblacional"
                    v-model="paciente.grupo_poblacional_id"
                    data-vv-name="Grupo poblacional"
                    v-validate="'required'">
            </select-v2>
        </div>
    </div>
    <div class="col-xs-12">
        <div class="panel">
            <div class="panel-header bg-gray-light">
                <h4>Programas sociales o de SAN</h4>
            </div>
            <div class="panel-content">
                <div class="row">
                    <div class="col-md-2 col-sm-3 col-xs-12">
                        <div class="form-group">
                            <label>Es beneficiario</label>
                            <select-v2
                                    :updatevalue="paciente.beneficiario"
                                    @change:updatevalue="val => paciente.beneficiario = val"
                                    :objeto="beneficiarios"
                                    placeholder="Es beneficiario"
                                    v-model="paciente.beneficiario"
                                    data-vv-name="Es beneficiario"
                                    v-validate="'required'">
                            </select-v2>
                        </div>
                    </div>
                    <div class="col-md-10 col-sm-9 col-xs-12">
                        <div class="form-group">
                            <label>Programas</label>
                            <select-v2
                                    :updatevalue="paciente.programa_social"
                                    @change:updatevalue="val => paciente.programa_social = val"
                                    multiple="true"
                                    :disable="programaDisabled"
                                    closeselect="false"
                                    :objeto="programasSocial"
                                    optionkey="id"
                                    optiontext="nombre"
                                    placeholder="Programas"
                                    v-model="paciente.programa_social"
                                    data-vv-name="Programas"
                                    v-validate="'required'">
                            </select-v2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>