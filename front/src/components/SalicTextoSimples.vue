<template>
    <div v-if="texto && texto.length > 0" v-html="texto"></div>
    <div v-else>N&atilde;o informado(a)</div>
</template>

<script>
    export default {
        name: 'SalicTextoSimples',
        props: ['texto'],
    };
</script>
