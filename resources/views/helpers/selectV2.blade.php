<script type="text/x-template" id="select-v2">
    <select :id="id" class="form-control">
        <option value=''></option>
    </select>
</script>
<script>
    Vue.component('select-v2', {
        template: '#select-v2',
        props:['placeholder','maxfilter','disable', 'closeselect', 'minsearch','multiple','allowclear', 'settings','minimuminputlength','url','delay','dropdownparent', 'updatevalue','optionkey','optiontext', 'objeto'],
        data: function () {
            return {
                cantidadRegistros:0,
                items:[],
                options:{},
                updatevalueObject:'',
                salida:'',
                id:'selectV2'+ (new Array(12).join().replace(/(.|$)/g, function(){return ((Math.random()*36)|0).toString(36)[Math.random()<.5?"toString":"toUpperCase"]();}))
            }
        },
        watch: {
            disable:function(){
                var app = this;
                var is = app.disable;
                $('#'+app.id).prop("disabled", is);
            },
            objeto: function() {
                var app = this;
                if(app.objeto){
                    $.each( app.objeto, function( key, updatevalue ) {
                        app.items.push({id:updatevalue==key?updatevalue:updatevalue[app.optionkey],text:updatevalue==key?updatevalue:updatevalue[app.optiontext], aux:updatevalue==key?updatevalue:updatevalue[app.optionaux]?updatevalue[app.optionaux]:null, entidad:updatevalue});
                    });
                    app.options.data = app.items;
                    $('#'+app.id).select2(app.options);
                }
            },
            updatevalue:function(){
                var app = this;
                console.log('cambio valor');
                console.log(app.updatevalue);
                if(app.multiple){
                    $('#'+app.id).val(app.updatevalue).trigger('change');
                }
                else{
                    if(app.updatevalueObject=='object'){
                        app.updatevalue.id = typeof app.updatevalue.id=='undefined'?null:app.updatevalue.id;
                        if(app.updatevalue.id==null){
                            $('#'+app.id).html('<option value=""></option>');
                        }else{
                            if(app.updatevalue){
                                if ($('#'+app.id).find("option[value='" + app.updatevalue[app.optionkey] + "']").length) {
                                    $('#'+app.id).val(app.updatevalue[app.optionkey]).trigger('change');
                                } else {
                                    $('#'+app.id).append('<option value="'+app.updatevalue[app.optionkey]+'" selected="selected">'+app.updatevalue[app.optiontext]+'</option>');
                                }
                            }
                        }
                        $('#'+app.id).val(app.updatevalue[app.optionkey]).trigger('change');
                    }else{
                        $('#'+app.id).val(app.updatevalue).trigger('change');
                    }
                }
                if(app.options.url){
                    console.log('entra si es url');
                    if((app.updatevalueObject == 'object'&&(app.updatevalue.id=='' || app.updatevalue.id==null)||(app.updatevalue=='' || app.updatevalue==null))){
                        console.log('entra lanzar');
                        app.options.minimumInputLength = 0;
                        app.options.delay = 0;
                        app.lanzarSelectFilter();
                    }else{
                        console.log('entra no lanzar');
                        app.options.delay = app.delay?app.delay:250;
                        app.options.minimumInputLength = app.minimuminputlength?app.minimuminputlength:0;
                    }
                    $('#'+app.id).select2(app.options);
                }
            }
        },
        methods:{
            emitir(e,target){
                var app = this;
                app.$emit("change:updatevalue", app.updatevalueObject=="object"?e.params.data.entidad:app.updatevalueObject=="array"?$(target).val()!=null?$(target).val():[]:e.params.data.id);
                app.$emit("change2");
            },
            lanzarSelectFilter(){
                var app = this;
                app.$http.post(app.url,{'maxfilter':app.options.maxfilter, 'settings':app.options.settings}).then(
                    (response)=>{
                        if(response.body!=null){
                            if(app.options.maxfilter > response.body.totalItems){
                                app.items = [];
                                $.each( response.body.items, function( key, updatevalue ) {
                                    if(app.updatevalueObject=='object') {
                                        app.items.push({
                                            id: updatevalue == key ? updatevalue : updatevalue.entidad[app.optionkey],
                                            text: updatevalue == key ? updatevalue : eval('updatevalue.entidad.'+ app.optiontext),
                                            aux: updatevalue.aux,
                                            entidad: updatevalue.entidad
                                        });
                                    }else{
                                        app.items.push({id:updatevalue==key?updatevalue:updatevalue[app.optionkey],text:updatevalue==key?updatevalue:eval('updatevalue.entidad.'+ app.optiontext), aux:updatevalue.aux, entidad:updatevalue.entidad});
                                    }
                                });
                                app.options.data = app.items;
                                app.options.ajax = null;
                                app.options.matcher = function (params, data) {
                                    if ($.trim(params.term) === '') {
                                        return data;
                                    }
                                    if (typeof data.text === 'undefined') {
                                        return null;
                                    }
                                    if ((typeof data.text ==='undefined')==false && (data.text.toUpperCase().indexOf(params.term.toUpperCase()) >=0)) {
                                        var modifiedData = $.extend({}, data, true);
                                        modifiedData.text += ' (matched)';
                                        return data;
                                    }

                                    if ((typeof data.aux ==='undefined')==false && (data.aux.toUpperCase().indexOf(params.term.toUpperCase()) >=0)) {
                                        var modifiedData = $.extend({}, data, true);
                                        modifiedData.text += ' (matched)';
                                        return data;
                                    }
                                    return null;
                                };
                            }else{
                                app.options.cache = true;
                                app.options.ajax = {
                                    headers: {
                                        'X-CSRF-TOKEN': $('meta[name$="csrf-token"]').attr('value')
                                    },
                                    type:'POST',
                                    url: app.options.url,
                                    dataType: 'json',
                                    delay: app.options.delay,
                                    data: function (params) {
                                        var query = {
                                            search: params.term,
                                            settings:app.options.settings,
                                            page:params.page || 1
                                        }
                                        return query;
                                    },
                                    processResults: function (data) {
                                        var items = !(typeof data.items === "undefined");
                                        return {
                                            results: items?data.items:'',
                                            pagination: {
                                                more: items
                                            }
                                        };
                                    }
                                };
                            }
                            $('#'+app.id).select2(app.options);
                        }
                    },(error)=>{
                        toastr.error(error.status + ' '+error.statusText+' ('+error.url+')');
                    }
                );
            }
        },
        mounted(){
            var app = this;
            app.updatevalueObject = ({}).toString.call(app.updatevalue).match(/\s([a-z|A-Z]+)/)[1].toLowerCase();
            app.options.dropdownParent = app.dropdownparent?$(app.dropdownparent):$('#'+app.id).closest('div');
            app.options.minimumResultsForSearch = app.minsearch?app.minsearch:10;
            app.options.allowClear=app.allowclear?app.allowclear:false;
            app.options.minimumInputLength = app.minimuminputlength?app.minimuminputlength:0;
            app.options.placeholder = app.placeholder?app.placeholder:'Seleccione una opción';
            app.options.closeOnSelect =  app.closeselect?app.closeselect=='true'?true:false:true;
            $.each( app.objeto, function( key, updatevalue ) {
                app.items.push({id:updatevalue==key?updatevalue:updatevalue[app.optionkey],text:updatevalue==key?updatevalue:updatevalue[app.optiontext], aux:updatevalue==key?updatevalue:updatevalue[app.optionaux]?updatevalue[app.optionaux]:null, entidad:updatevalue});
            });
            app.options.data = app.items;
            app.options.settings = app.settings?app.settings:null;
            app.options.maxfilter = app.maxfilter?app.maxfilter:200;
            app.options.url = app.url?app.url:null;
            if(app.options.url){
                app.options.escapeMarkup= function (markup) { return markup; };
                app.options.templateResult= function(repo) {
                    if (repo.loading) {
                        return repo.text;
                    }
                    var markup = '<a href="#" class="list-group-item list-group-item-action media active">'+
                        '<div class="media-body">'+
                        '<h5 class="list-group-item-heading">'+repo.text+'</h5>';
                    if (repo.aux) {
                        markup += '<p class="list-group-item-text">'+repo.aux+'</p>';
                    }
                    markup +='</div>'+
                        '</a>';
                    return markup;
                };
                app.options.templateSelection = function(repo) {
                    return repo[app.optiontext] || repo.text;
                };
                app.options.delay = 0;
                app.options.minimumInputLength = 0;
                app.lanzarSelectFilter();
            }else{
                $('#'+app.id).select2(app.options);
            }

            if(app.multiple=='true'){
                $('#'+app.id).prop('multiple',true);
                if(app.updatevalueObject=='array'){
                    $("#"+app.id+" option[value='']").remove();
                }
            }

            $('#'+app.id).on('select2:select',function (e) {
                app.emitir(e,this);
            }).on('select2:unselect',function (e) {
                app.updatevalueObject=="object"?e.params.data.entidad = {}:app.updatevalueObject=="array"?$(this).val()!=null?$(this).val():[]:e.params.data.id = '';
                app.emitir(e,this);
            });
        }
    });
</script>