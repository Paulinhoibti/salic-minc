<template>
    <div class="center-align padding10">
        <h6 v-html="text"></h6>
        <div class="preloader-wrapper small active">
            <div class="spinner-layer spinner-green-only">
                <div class="circle-clipper left">
                    <div class="circle"></div>
                </div>
                <div class="gap-patch">
                    <div class="circle"></div>
                </div>
                <div class="circle-clipper right">
                    <div class="circle"></div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        name: 'Carregando',
        props: {
            text: {
                type: String,
                default: '',
            },
        },
    };
</script>
