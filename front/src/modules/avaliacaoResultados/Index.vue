<template>
    <div id="app">
        <v-app>
            <SlNav>
            </SlNav>
            <v-container>
              <v-layout>
                <router-view></router-view>
              </v-layout>
            </v-container>
            <v-footer
                height="auto"
                clase="pa-3"
                color="green lighten-1"
            >
                <v-layout
                    justify-center
                    row
                    wrap
                >
                  <v-flex xs3 text-xs-center>
                    <a class="orange-text text-lighten-3 tooltipped" data-position="top" data-delay="50" data-tooltip="Ir para o portal de notícias, tutoriais e legislações da Lei Rouanet" target="_blank" href="http://rouanet.cultura.gov.br/" data-tooltip-id="11c539d7-9913-16c0-3c16-ce4e6364817f">LEI ROUANET</a>
                  </v-flex>
                  <v-flex xs3 text-xs-center>
                    Desenvolvimento | SQL Server
                  </v-flex>
                  <v-flex xs3 text-xs-center>
                    Versão: <a class="tooltipped" data-position="top" data-delay="50" data-tooltip="Branch: f/prestacao-contas/dev" target="_blank" href="https://github.com/culturagovbr/salic-minc/releases" data-tooltip-id="ebb0a15a-2763-9484-464d-f10ca3d73a27">v5.0.7</a>
                  </v-flex>
                  <v-flex xs3 text-xs-center>
                    <a class="orange-text text-lighten-3 tooltipped" data-position="top" data-delay="50" data-tooltip="Ir para o Site: Desenvolvimento Salic MinC" target="_blank" href="http://culturagovbr.github.io/salic-minc" data-tooltip-id="14f518c8-eb5f-3b9a-c172-cf75020f54f5">Desenvolvimento</a>
                  </v-flex>
             </v-layout>
            </v-footer>
      </v-app>
    </div>
</template>

<script>
    import SlNav from './components/SlNav';

    export default {
        name: 'Index',
        components: { SlNav },
    };
</script>
