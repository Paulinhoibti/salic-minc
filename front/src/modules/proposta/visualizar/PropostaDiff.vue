<template>
    <div class="proposta">
            <div v-if="Object.keys(dadosHistorico).length > 2">
                <PropostaAlteracoes :idpreprojeto="idpreprojeto" :dadosAtuais="dadosAtuais"
                                      :dadosHistorico="dadosHistorico"></PropostaAlteracoes>
            </div>
            <div v-else-if="Object.keys(dadosAtuais).length > 2">
                <div class="card padding20">
                    <div class="nao-existe-versao-proposta">
                        <h4><i class="material-icons small left">report</i>N�o existe versionamento de altera��es para o projeto informado...</h4>
                        <p style="margin-left: 44px">O proponente n�o fez altera��es no projeto no prazo estabelecido.</p>
                    </div>
                </div>
                <Proposta :idpreprojeto="idpreprojeto" :proposta="dadosAtuais"></Proposta>
            </div>
            <div v-else>
                <div class="card padding20">
                    <div class="center-align padding20">
                        <h5>Carregando ...</h5>
                    </div>
                </div>
            </div>
    </div>
</template>
<script>
import PropostaAlteracoes from './components/PropostaAlteracoes';
import Proposta from './Proposta';

export default {
    name: 'PropostaDiff',
    data: function () {
        return {
            dadosAtuais: {
                type: Object,
                default: function () {
                    return {}
                }
            },
            dadosHistorico: {
                type: Object,
                default: function () {
                    return {}
                }
            }
        };
    },
    props: ['idpreprojeto', 'tipo'],
    components: {
        PropostaAlteracoes,
        Proposta,
    },
    mounted: function () {
        if (typeof this.idpreprojeto !== 'undefined') {
            this.buscar_dados();
        }
    },
    methods: {
        buscar_dados: function () {
            const self = this;
            /* eslint-disable */
            $3.ajax({
                url: '/proposta/visualizar/obter-proposta-cultural-versionamento/idPreProjeto/' + self.idpreprojeto +
                '/tipo/' + self.tipo
            }).done(function (response) {
                self.dadosAtuais = response.data.atual;
                self.dadosHistorico = response.data.historico;
            });
        }
    }
};
</script>