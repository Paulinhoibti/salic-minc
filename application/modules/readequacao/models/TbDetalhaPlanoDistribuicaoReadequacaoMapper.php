<?php

class Readequacao_Model_TbDetalhaPlanoDistribuicaoReadequacaoMapper extends MinC_Db_Mapper
{
    public function __construct()
    {
        parent::setDbTable('Readequacao_Model_DbTable_TbDetalhaPlanoDistribuicaoReadequacao');
    }

    public function save($model)
    {
        return parent::save($model);
    }

    public function obterDetalhamentosParaReadequacao(Projeto_Model_TbProjetos $projeto)
    {
        if (empty($projeto)) {
            throw new Exception("Projeto &eacute; obrigat&oacute;rio");
        }

        $tbDetalhaReadequacao = new Readequacao_Model_DbTable_TbDetalhaPlanoDistribuicaoReadequacao();
        $paramsBuscarDetalhamentos = [
            'idPronac = ?' => $projeto->getIdPRONAC(),
            'stAtivo = ?' => 'S'
        ];

        $detalhamentosReadequacao = $tbDetalhaReadequacao->buscar(
            $paramsBuscarDetalhamentos,
            ['tpSolicitacao DESC', 'dsProduto']
        )->toArray();

        if (count($detalhamentosReadequacao) == 0) {
            $detalhamentosReadequacao = $this->copiarDetalhamentosDaProposta($projeto, $paramsBuscarDetalhamentos);
        }

        $detalhamentosReadequacao = $this->zerarValoresItensExcluidos($detalhamentosReadequacao);

        return $detalhamentosReadequacao;
    }

    public function copiarDetalhamentosDaProposta(Projeto_Model_TbProjetos $projeto, $paramsBuscarDetalhamentos)
    {
        $tbDetalhaPlanoDistribuicao = new Proposta_Model_DbTable_TbDetalhaPlanoDistribuicao();
        $detalhamentosDaProposta = $tbDetalhaPlanoDistribuicao->obterDetalhamentosDaProposta($projeto->getidProjeto());

        if (empty($detalhamentosDaProposta)) {
            return [];
        }

        try {
            $this->beginTransaction();
            foreach ($detalhamentosDaProposta as $key => $detalhamento) {
                unset($detalhamento['idDetalhaPlanoDistribuicao']);
                $detalhamento['idReadequacao'] = null;
                $detalhamento['tpSolicitacao'] = 'N';
                $detalhamento['stAtivo'] = 'S';
                $detalhamento['idPronac'] = $projeto->getIdPRONAC();

                $this->save(new Readequacao_Model_TbDetalhaPlanoDistribuicaoReadequacao($detalhamento));
            }
            $this->commit();

            $tbDetalhaReadequacao = new Readequacao_Model_DbTable_TbDetalhaPlanoDistribuicaoReadequacao();
            return $tbDetalhaReadequacao->buscar($paramsBuscarDetalhamentos, ['tpSolicitacao DESC', 'dsProduto'])->toArray();

        } catch (Exception $e) {
            $this->rollBack();
            throw new $e;
        }
    }

    public function zerarValoresItensExcluidos($detalhamentos)
    {
        if (count($detalhamentos) == 0) {
            return [];
        }

        $novosDetalhamentos = [];
        foreach ($detalhamentos as $detalhamento) {

            if ($detalhamento["tpSolicitacao"] == "E") {
                $detalhamento["qtProduzida"] = 0;
                $detalhamento["qtPatrocinador"] = 0;
                $detalhamento["qtExemplares"] = 0;
                $detalhamento["qtGratuitaDivulgacao"] = 0;
                $detalhamento["qtGratuitaPatrocinador"] = 0;
                $detalhamento["qtGratuitaPopulacao"] = 0;
                $detalhamento["qtPopularIntegral"] = 0;
                $detalhamento["qtPopularParcial"] = 0;
                $detalhamento["vlUnitarioPopularIntegral"] = 0;
                $detalhamento["vlReceitaPopularIntegral"] = 0;
                $detalhamento["vlReceitaPopularParcial"] = 0;
                $detalhamento["qtProponenteIntegral"] = 0;
                $detalhamento["qtProponenteParcial"] = 0;
                $detalhamento["vlUnitarioProponenteIntegral"] = 0;
                $detalhamento["vlReceitaProponenteIntegral"] = 0;
                $detalhamento["vlReceitaProponenteParcial"] = 0;
                $detalhamento["vlReceitaPrevista"] = 0;
            }

            $novosDetalhamentos[] = $detalhamento;
        }

        return $novosDetalhamentos;
    }

    public function salvarDetalhamento($dados, Projeto_Model_TbProjetos $projeto)
    {
        if (empty($projeto)) {
            throw new Exception("Projeto &eacute; obrigat&oacute;rio");
        }

        if (empty($dados['idPlanoDistribuicao'])) {
            throw new Exception("Produto � obrigat�rio");
        }

        $dados['idReadequacao'] = null;
        $dados['stAtivo'] = 'S';
        $dados['idPronac'] = $projeto->getIdPRONAC();

        if (empty($dados['idDetalhaPlanoDistribuicao'])) {
            unset($dados['idDetalhaPlanoDistribuicao']);
            $dados['tpSolicitacao'] = 'I';
        }

        $id = $this->save(new Readequacao_Model_TbDetalhaPlanoDistribuicaoReadequacao($dados));

        if (!empty($id)) {
            $dados['idDetalhaPlanoDistribuicao'] = $id;
        }

        return $dados;
    }

    public function alterarSituacaoDetalhamento($idDetalhamento, $situacao)
    {
        if (empty($idDetalhamento)) {
            throw new Exception("Id do detalhamento &eacute; obrigat&oacute;rio");
        }

        $tbDetalhaReadequacao = new Readequacao_Model_DbTable_TbDetalhaPlanoDistribuicaoReadequacao();

        $where = $tbDetalhaReadequacao->getAdapter()->quoteInto('idDetalhaPlanoDistribuicao = ?', $idDetalhamento);

        $id = $tbDetalhaReadequacao->update(
            ['tpSolicitacao' => $situacao],
            $where
        );

        if (empty($id)) {
            throw new Exception("Erro ao atualizar item");
        }

        $detalhamento = $tbDetalhaReadequacao->buscar([
            'idDetalhaPlanoDistribuicao = ?' => $idDetalhamento
        ])->toArray();

        if ($situacao == 'E') {
            $detalhamento = $this->zerarValoresItensExcluidos($detalhamento);
        }

        $detalhamento = array_map('utf8_encode', reset($detalhamento));

        return $detalhamento;
    }
}