<script type="text/x-template" id="buttonsave">
    <button type="submit" :id="id?id:'ladda-button'" :form="form" class="btn ladda-button" :class="class_btn?class_btn:'btn-success'" data-spinner-size="30" data-style="expand-right">
        <span v-if="icon_class"><i :class="icon_class"></i></span>
        <span class="ladda-label" v-text="label?label:'Guardar'"></span>
        <span class="ladda-spinner"></span>
    </button>
</script>
<script>
    Vue.component('buttonsave', {
        template: '#buttonsave',
        props:['form','label', 'id', 'class_btn', 'url', 'object', 'nav_target','v_scope', 'icon_class'],
        inject: {
            $validator: '$validator'
        },
        mounted(){
            var app = this;
            var targetbutton = '#'+(app.id?app.id:'ladda-button');
            ladda = Ladda.create(document.querySelector(targetbutton));
            var $form = app.form?$('#'+app.form):$(targetbutton).closest('form');

            $form.on('submit', function (e) {
                e.preventDefault();
                var validador = app.v_scope?app.$validator.validateAll(app.v_scope):app.$validator.validateAll();
                validador.then((result) => {
                    if (result) {
                        ladda.start();
                        app.$http.post(app.url,app.object).then((response)=>{
                            if(response.body.estado=='ok'){
                                toastr["success"](response.body.message);
                                app.$emit("response", response.body);

                            }else if(response.body.estado == 'validador'){
                                jQuery.each(response.body.errors,function (i,value) {
                                    toastr["warning"](i.toUpperCase()+": "+value);
                                });
                            }else{
                                toastr["warning"](response.body.error);
                            }
                            ladda.stop();
                        },(error)=>{
                            toastr.error('Error en servidor:: '+error.status + ' '+error.statusText+' ('+error.url+')');
                            ladda.stop();
                        });
                        return;
                    }
                    var error = app.errors.items[0];

                    if($('.form-control[data-vv-name$="'+error.field+'"]').hasClass('form-control')){
                        $(app.nav_target).find('li:nth-child('+(($('.form-control[data-vv-name$="'+error.field+'"]').closest(".tab-pane").index())+1)+')').find('a').click();
                        setTimeout(function(){ $('.form-control[data-vv-name$="'+error.field+'"]').focus(); }, 500);
                    }else{
                        $(app.nav_target).find('li:nth-child('+(($('.dropdown[data-vv-name$="'+error.field+'"]').closest(".tab-pane").index())+1)+')').find('a').click();
                        setTimeout(function(){ $('select[data-vv-name$="'+error.field+'"]').closest('div').find('.select2-selection').focus(); }, 500);
                    }
                    toastr["warning"](error.field.toUpperCase()+": "+error.msg);
                });
            });
        }
    });
</script>