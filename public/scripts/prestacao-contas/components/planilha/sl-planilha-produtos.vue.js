Vue.component('sl-planilha-produtos', {
    props: ['produtos', 'idpronac'],
    template: `<ul class="collapsible no-margin" data-collapsible="expandable" id="produtos-collapsible">
                        <li class="active" v-for="produto in produtos">
                            <div class="collapsible-header green-text active">
                                <i class="material-icons">perm_media</i>
                                {{ produto.produto }}
                            </div>
                            <div class="collapsible-body no-padding">
                                <ul class="collapsible no-margin no-border" data-collapsible="expandable">
                                    <sl-planilha-etapas
                                            :etapa="etapa"
                                            v-for="(etapa, index) in produto.etapa"
                                            :idpronac="idpronac"
                                            :cdProduto="produto.cdProduto"
                                            :key="index"
                                    ></sl-planilha-etapas>
                                </ul>
                            </div>
                        </li>
                    </ul>`,
    mounted: function () {
        this.iniciarCollapsible();
    },
    methods: {
        iniciarCollapsible: function () {
            $3('#produtos-collapsible.collapsible').each(function() {
                $3(this).collapsible();
            });
        }
    }
})