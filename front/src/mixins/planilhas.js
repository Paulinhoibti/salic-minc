import numeral from 'numeral';
import 'numeral/locales';

numeral.locale('pt-br');
numeral.defaultFormat('0,0.00');

export default {
    methods: {
        obterValorSolicitadoTotal(table) {
            const soma = numeral();

            Object.entries(table).forEach(([, cell]) => {
                if (cell.vlSolicitado !== undefined) {
                    soma.add(parseFloat(cell.vlSolicitado));
                }
            });

            return soma.format();
        },
        obterValorAprovadoTotal(table) {
            const soma = numeral();
            Object.entries(table).forEach(([, cell]) => {
                if (typeof cell.vlAprovado !== 'undefined') {
                    if (cell.tpAcao && cell.tpAcao === 'E') {
                        return;
                    }
                    soma.add(parseFloat(cell.vlAprovado));
                }
            });

            return soma.format();
        },
        obterValorComprovadoTotal(table) {
            const soma = numeral();
            Object.entries(table).forEach(([, cell]) => {
                if (typeof cell.VlComprovado !== 'undefined') {
                    if (cell.tpAcao && cell.tpAcao === 'E') {
                        return;
                    }
                    soma.add(parseFloat(cell.VlComprovado));
                }
            });

            return soma.format();
        },
        obterValorSugeridoTotal(table) {
            const soma = numeral();

            Object.entries(table).forEach(([, cell]) => {
                if (typeof cell.vlSugerido !== 'undefined') {
                    soma.add(parseFloat(cell.vlSugerido));
                }
            });

            return soma.format();
        },
        converterStringParaClasseCss(text) {
            return text
                .toString()
                .toLowerCase()
                .trim()
                .replace(/&/g, '-and-')
                .replace(/[\s\W-]+/g, '-');
        },
        isObject(el) {
            return typeof el === 'object';
        },
        definirClasseItem(row) {
            return {
                'orange lighten-2': row.stCustoPraticado === true || row.stCustoPraticado === '1' || row.stCustoPraticado === 1,
                'linha-incluida': row.tpAcao === 'I',
                'linha-excluida': row.tpAcao === 'E',
                'linha-atualizada': row.tpAcao === 'A',
            };
        },
    },
    filters: {
        formatarParaReal(value) {
            const parsedValue = parseFloat(value);
            return numeral(parsedValue).format('0,0.00');
        },
    },
};

