<?php

namespace Application\Modules\PrestacaoContas\Service\Assinatura\Acao;

use MinC\Assinatura\Acao\IAcaoEncaminhar;

class Encaminhar implements IAcaoEncaminhar
{
    /**
     * @var \MinC\Assinatura\Model\Assinatura $assinatura
     */
    private $assinatura;

    public function executar(\MinC\Assinatura\Model\Assinatura $assinatura)
    {
        $this->assinatura = $assinatura;

        $tbPrestacaoContasXParecerDbTable = new \PrestacaoContas_Model_DbTable_TbPrestacaoContasXParecer();
        $tbPrestacaoContasXParecer = $tbPrestacaoContasXParecerDbTable->findBy([
            'idParecer' => $assinatura->modeloTbDocumentoAssinatura->getIdAtoDeGestao()
        ]);

        $idTipoDoAtoAdministrativo = $assinatura->modeloTbDocumentoAssinatura->getIdTipoDoAtoAdministrativo();
        if ((int)$idTipoDoAtoAdministrativo == (int)\Assinatura_Model_DbTable_TbAssinatura::TIPO_ATO_PARECER_TECNICO_PrestacaoContas_VINCULADAS) {
            $this->tratarEncaminhamentoVinculadas($tbPrestacaoContasXParecer['idPrestacaoContas']);
        } elseif ((int)$idTipoDoAtoAdministrativo == (int)\Assinatura_Model_DbTable_TbAssinatura::TIPO_ATO_PARECER_TECNICO_AJUSTE_DE_PROJETO) {
            $this->tratarEncaminhamentoDeAjustesDoProjeto($tbPrestacaoContasXParecer['idPrestacaoContas']);
        }
    }

    private function tratarEncaminhamentoVinculadas($idPrestacaoContas)
    {
        $atoAdministrativo = $this->assinatura->modeloTbAtoAdministrativo;
        $objTbProjetos = new Projeto_Model_DbTable_Projetos();
        $objOrgaos = new \Orgaos();
        $dadosProjeto = $objTbProjetos->findBy(array(
            'IdPRONAC' => $this->assinatura->modeloTbAssinatura->getIdPronac()
        ));
        $dadosOrgaoSuperior = $objOrgaos->obterOrgaoSuperior($dadosProjeto['Orgao']);

        switch ($atoAdministrativo->getIdPerfilDoAssinante()) {
            case \Autenticacao_Model_Grupos::PARECERISTA:
                $siEncaminhamento = \PrestacaoContas_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_DEVOLVIDO_ANALISE_TECNICA;
                break;
            case \Autenticacao_Model_Grupos::COORDENADOR_DE_PARECER:
                $siEncaminhamento = \PrestacaoContas_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_SOLICITACAO_ENCAMINHADA_AO_PRESIDENTE_DA_VINCULADA;
                break;
            case \Autenticacao_Model_Grupos::DIRETOR_DEPARTAMENTO:
                $siEncaminhamento = \PrestacaoContas_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_SOLICITACAO_ENCAMINHADA_AO_SECRETARIO;

                $orgaoDestino = \Orgaos::ORGAO_SUPERIOR_SEFIC;
                if ($dadosOrgaoSuperior['Codigo'] == \Orgaos::ORGAO_SUPERIOR_SAV) {
                    $orgaoDestino = \Orgaos::ORGAO_SUPERIOR_SAV;
                }
                break;
            case \Autenticacao_Model_Grupos::PRESIDENTE_DE_VINCULADA:
                $siEncaminhamento = \PrestacaoContas_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_SOLICITACAO_ENCAMINHADA_AO_DIRETOR;

                $orgaoDestino = \Orgaos::ORGAO_SEFIC_DIC;
                if ($dadosOrgaoSuperior['Codigo'] == \Orgaos::ORGAO_SUPERIOR_SAV) {
                    $orgaoDestino = 682;
                }

                break;
        }

        if (isset($orgaoDestino)) {
            $objTbProjetos->alterarOrgao($orgaoDestino, $this->assinatura->modeloTbAssinatura->getIdPronac());
        }

        if ($siEncaminhamento) {
            $dados = ['siEncaminhamento' => $siEncaminhamento];
            $where = "idPrestacaoContas = {$idPrestacaoContas}";
            $tbPrestacaoContas = new \PrestacaoContas_Model_DbTable_TbPrestacaoContas();
            $tbPrestacaoContas->update($dados, $where);
        }

    }

    private function tratarEncaminhamentoDeAjustesDoProjeto($idPrestacaoContas)
    {
        $dados = [];
        $atoAdministrativo = $this->assinatura->modeloTbAtoAdministrativo;

        switch ($atoAdministrativo->getIdPerfilDoAssinante()) {
            case \Autenticacao_Model_Grupos::TECNICO_ACOMPANHAMENTO:
                $siEncaminhamento = \PrestacaoContas_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_DEVOLVIDA_COORDENADOR_TECNICO;
                break;
            case \Autenticacao_Model_Grupos::COORDENADOR_ACOMPANHAMENTO:
                $siEncaminhamento = \PrestacaoContas_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_SOLICITACAO_ENCAMINHADA_AO_COORDENADOR_GERAL;

                $objPrestacaoContas_ReadequacoesController = new \PrestacaoContas_ReadequacoesController();
                $objPrestacaoContas_ReadequacoesController->encaminharOuFinalizarPrestacaoContasChecklist(
                    $idPrestacaoContas
                );
                break;
            case \Autenticacao_Model_Grupos::COORDENADOR_GERAL_ACOMPANHAMENTO:
                $siEncaminhamento = \PrestacaoContas_Model_tbTipoEncaminhamento::SI_ENCAMINHAMENTO_FINALIZADA_SEM_PORTARIA;
                $dados['stEstado'] = \PrestacaoContas_Model_DbTable_TbPrestacaoContas::ST_ESTADO_FINALIZADO;
                break;
        }

        $dados['siEncaminhamento'] = $siEncaminhamento;
        $where = "idPrestacaoContas = {$idPrestacaoContas}";

        $tbPrestacaoContas = new \PrestacaoContas_Model_DbTable_TbPrestacaoContas();
        $tbPrestacaoContas->update($dados, $where);
    }
}