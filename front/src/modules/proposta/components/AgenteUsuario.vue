<template>
    <div class="dados-usuario">
        <div class="card" v-if="usuario">
            <div class="card-content">
                <h5>Usu&aacute;rio do sistema</h5>
                <div class="row">
                    <div class="col s12 l3 m3">
                        <b>CPF</b><br>
                        {{ usuario.cpf}}
                    </div>
                    <div class="col s12 l3 m3">
                        <b>Nome</b><br>
                        {{ usuario.nome}}
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default {
    name: 'AgenteUsuario',
    data() {
        return {
            usuario: [],
        };
    },
    props: ['idusuario'],
    mounted() {
        if (typeof this.idusuario !== 'undefined') {
            this.fetch(this.idusuario);
        }
    },
    watch: {
        idusuario(value) {
            this.fetch(value);
        },
    },
    methods: {
        fetch(id) {
            if (id) {
                const self = this;
                /* eslint-disable */
                $3.ajax({
                    url: '/autenticacao/index/obter-dados-usuario/idUsuario/' + id
                }).done(function (response) {
                    self.usuario = response.data;
                });
            }
        },
    },
}
</script>