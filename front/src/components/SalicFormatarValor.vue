<template>
    <div v-if="valorFormatado && valorFormatado.length > 0" v-html="valorFormatado"></div>
    <div v-else>0</div>
</template>

<script>
    export default {
        /* eslint-disable */
        name: "SalicFormatarValor",
        props: ["valor"],
        computed: {
            valorFormatado: function () {
                return this.converterParaMoeda(this.valor);
            }
        },
        methods: {
            converterParaMoeda: function (num) {
                // funcao salic de conversao pontuada - trazida de moeda.js
                var x = 0;

                if (num < 0) {
                    num = Math.abs(num);
                    x = 1;
                }

                if (isNaN(num)) {
                    num = "0";
                }
                var cents = Math.floor((num * 100 + 0.5) % 100);
                num = Math.floor((num * 100 + 0.5) / 100).toString();

                if (cents < 10) {
                    cents = "0" + cents;
                }
                for (var i = 0; i < Math.floor((num.length - (1 + i)) / 3); i++)
                    num =
                        num.substring(0, num.length - (4 * i + 3)) +
                        "." +
                        num.substring(num.length - (4 * i + 3));

                var ret = num + "," + cents;
                if (x == 1) {
                    ret = " - " + ret;
                }

                return ret;
            },
        },
    };
</script>
