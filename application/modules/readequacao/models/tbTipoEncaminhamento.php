<?php

/**
 * Tabela tbTipoReadequacao
 * @author fernao.lara@cultura.gov.br
 * @since 14/03/2018
 */
class Readequacao_Model_tbTipoEncaminhamento extends MinC_Db_Table_Abstract
{
    protected $_schema = "sac";
    protected $_name = "tbTipoEncaminhamento";

    const SI_ENCAMINHAMENTO_ENVIADO_MINC = 1;
    const SI_ENCAMINHAMENTO_SOLICITACAO_INDEFERIDA = 2;
    const SI_ENCAMINHAMENTO_ENVIADO_UNIDADE_ANALISE = 3;
    const SI_ENCAMINHAMENTO_ENVIADO_ANALISE_TECNICA = 4;
    const SI_ENCAMINHAMENTO_DEVOLVIDO_ANALISE_TECNICA = 5;
    const SI_ENCAMINHAMENTO_DEVOLVIDA_AO_MINC = 6;
    const SI_ENCAMINHAMENTO_ENVIADO_CNIC = 7;
    const SI_ENCAMINHAMENTO_ENVIADO_PLENARIA = 8;
    const SI_ENCAMINHAMENTO_CHECKLIST_PUBLICACAO = 9;
    const SI_ENCAMINHAMENTO_DEVOLVIDA_COORDENADOR_TECNICO = 10;
    const SI_ENCAMINHAMENTO_NAO_ENVIA_MINC = 11;
    const SI_ENCAMINHAMENTO_CADASTRADA_PROPONENTE = 12;
    const SI_ENCAMINHAMENTO_FINALIZADA_SEM_PORTARIA = 15;
    const SI_ENCAMINHAMENTO_SOLICITACAO_ENCAMINHADA_AO_COORDENADOR_GERAL = 18;
    const SI_ENCAMINHAMENTO_SOLICITACAO_ENCAMINHADA_AO_DIRETOR = 19;
    const SI_ENCAMINHAMENTO_SOLICITACAO_ENCAMINHADA_AO_SECRETARIO = 20;
    const SI_ENCAMINHAMENTO_SOLICITACAO_DEVOLVIDA_AO_COORDENADOR_PELO_COORDENADOR_GERAL = 21;
    const SI_ENCAMINHAMENTO_SOLICITACAO_DEVOLVIDA_AO_COORDENADOR_PELO_DIRETOR = 22;
    const SI_ENCAMINHAMENTO_SOLICITACAO_DEVOLVIDA_AO_COORDENADOR_PELO_SECRETARIO = 23;
    const SI_ENCAMINHAMENTO_SOLICITACAO_ENCAMINHADA_AO_PRESIDENTE_DA_VINCULADA = 24;
    const SI_ENCAMINHAMENTO_SOLICITACAO_DEVOLVIDA_AO_COORDENADOR_DE_PARECER_PELO_PRESIDENTE = 25;

}