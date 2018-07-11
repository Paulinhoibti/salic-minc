Vue.component('avaliacao-amostragem', {
    props: [
        'idpronac',
    ],
    template: `
        <div>
            <sl-planilha-produtos :produtos="produtos" :idpronac="idpronac"></sl-planilha-produtos>
        </div>
    `,
    mounted: function () {
        let vue = this;
        $3.ajax({
            url: "/prestacao-contas/prestacao-contas/comprovantes-amostragem/idPronac/" + this.idpronac
        }).done(function( data ) {
            vue.$data.produtos = data;
        });
    },
    data: function () {
        return {
            produtos: []
        };
    },
    created() {
        this.testando();
    },
    methods: {
        iniciarCollapsible: function () {
            $3('.collapsible').each(function() {
                $3(this).collapsible();
            });
        },
        testando: function() {
            console.log('asdasdadasdasdasdasd');
        },
    }
})
