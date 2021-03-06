<?php

class Projeto_HomologacaoController extends Projeto_GenericController
{

    private $arrBreadCrumb = [];
    private $situacaoParaHomologacao = Projeto_Model_Situacao::PROJETO_APRECIADO_PELA_CNIC;

    public function init()
    {
        $this->validarPerfis();
        $GrupoAtivo = new Zend_Session_Namespace('GrupoAtivo');
        $this->codOrgao = $GrupoAtivo->codOrgao;
        $this->codGrupo = $GrupoAtivo->codGrupo;

        $this->arrBreadCrumb[] = array('url' => '/principal', 'title' => 'In&iacute;cio', 'description' => 'Ir para in&iacute;cio');
        parent::init();
    }

    private function validarPerfis()
    {
        $auth = Zend_Auth::getInstance();

        $PermissoesGrupo = [];
        $PermissoesGrupo[] = Autenticacao_Model_Grupos::COORDENADOR_ANALISE;
        $PermissoesGrupo[] = Autenticacao_Model_Grupos::DIRETOR_DEPARTAMENTO;
        $PermissoesGrupo[] = Autenticacao_Model_Grupos::PRESIDENTE_VINCULADA_SUBSTITUTO;

        isset($auth->getIdentity()->usu_codigo) ? parent::perfil(1, $PermissoesGrupo) : parent::perfil(4, $PermissoesGrupo);
    }

    public function indexAction()
    {
        $this->arrBreadCrumb[] = array('url' => '', 'title' => 'Homologacao de Projetos', 'description' => 'Tela atual');
        $this->view->arrBreadCrumb = $this->arrBreadCrumb;
    }

    public function listarAction()
    {
        $start = $this->getRequest()->getParam('start');
        $length = $this->getRequest()->getParam('length');
        $draw = (int)$this->getRequest()->getParam('draw');
        $search = $this->getRequest()->getParam('search');
        $order = $this->getRequest()->getParam('order');
        $columns = $this->getRequest()->getParam('columns');

        $order = ($order[0]['dir'] != 1) ? array($columns[$order[0]['column']]['name'] . ' ' . $order[0]['dir']) : ["Pronac desc"];

        $filtro = $columns[0]['search']['value'];
        
        switch ($filtro) {
            case '':
                $where['a.Situacao = ?'] = 'D50';
                $where['NOT EXISTS(SELECT TOP 1 * FROM SAC.dbo.tbDiligencia WHERE idPronac = a.IdPRONAC AND idTipoDiligencia = 181 AND DtSolicitacao IS NOT NULL AND DtResposta IS NULL AND stEstado = 0 AND stEnviado = \'S\')'] = '';
                break;
            case 'diligenciados':
                $where['a.Situacao = ?'] = 'D25';
                $where['EXISTS(SELECT TOP 1 * FROM SAC.dbo.tbDiligencia WHERE idPronac = a.IdPRONAC AND idTipoDiligencia = 181 AND DtSolicitacao IS NOT NULL AND DtResposta IS NULL AND stEstado = 0 AND stEnviado = \'S\')'] = '';
                break;
            case 'respondidos':
                $where['a.Situacao = ?'] = 'D50';
                $where['EXISTS(SELECT TOP 1 * FROM SAC.dbo.tbDiligencia WHERE idPronac = a.IdPRONAC AND idTipoDiligencia = 181 AND DtSolicitacao IS NOT NULL AND DtResposta IS NOT NULL AND stEstado = 0)'] = '';
                break;
        }

        $where['a.Orgao = ?'] = $this->codOrgao;

        $dbTableEnquadramento = new Projeto_Model_DbTable_Enquadramento();
        $projetos = $dbTableEnquadramento->obterProjetosApreciadosCnic(
            $where,
            $order,
            $start,
            $length,
            $search
        );

        if (count($projetos) > 0) {
            foreach ($projetos as $key => $item) {
                foreach ($item as $coluna => $value) {
                    $projetosApreciados[$key][$coluna] = utf8_encode($value);
                }
            }

            $recordsTotal = $dbTableEnquadramento->obterProjetosApreciadosCnic(
                $where,
                null,
                null,
                null,
                null
            );
            $recordsTotal = count($recordsTotal);
            $recordsFiltered = $dbTableEnquadramento->obterProjetosApreciadosCnic(
                $where,
                null,
                null,
                null,
                $search);
            $recordsFiltered = count($recordsFiltered);
        }

        $this->_helper->json(
            [
                "data" => !empty($projetosApreciados) ? $projetosApreciados : 0,
                'recordsTotal' => $recordsTotal ? $recordsTotal : 0,
                'draw' => $draw,
                'recordsFiltered' => $recordsFiltered ? $recordsFiltered : 0,
            ]
        );
    }

    public function homologarParecerAction()
    {
        $this->_helper->layout->disableLayout();
        $this->prepareData($this->getRequest()->getParam('id'));
    }

    /**
     * @todo confirmar se setIdAtoDeGestao e o IdEnquadramento.
     */
    public function encaminharAction()
    {
        $this->_helper->layout->disableLayout();
        if ($this->getRequest()->isPost()) {
            $this->_helper->viewRenderer->setNoRender(true);
            $mapper = new Projeto_Model_TbHomologacaoMapper();
            $arrPost = $this->getRequest()->getPost();
            $this->_helper->json([
                'status' => $mapper->encaminhar($arrPost),
                'msg' => $mapper->getMessages(),
                'close' => 1
            ]);
        } else {
            $this->prepareData($this->getRequest()->getParam('id'));
            $this->view->urlAction = '/projeto/homologacao/encaminhar';
        }
    }

    public function homologarAction()
    {
        $this->_helper->layout->disableLayout();

        if ($this->getRequest()->isPost()) {
            $this->_helper->viewRenderer->setNoRender(true);
            $mapper = new Projeto_Model_TbHomologacaoMapper();
            $arrPost = $this->getRequest()->getPost();
            $arrPost['stDecisao'] = (isset($arrPost['stDecisao'])) ? 2 : 1;
            $arrPost['tpHomologacao'] = 1;
            $this->_helper->json([
                'status' => $mapper->save($arrPost),
                'msg' => $mapper->getMessages(),
                'close' => 1
            ]);
        }
    }

    /**
     * Metodo responsavel por preparar o formulario conforme cada acao.
     */
    private function prepareData($intIdPronac)
    {
        $dbTableParecer = new Parecer();
        $dbTableAcaoProjeto = new tbAcaoAlcanceProjeto();
        $dbTableHomologacao = new Projeto_Model_DbTable_TbHomologacao();
        $dbTableEnquadramento = new Projeto_Model_DbTable_Enquadramento();
        $dadosEnquadramento = $dbTableEnquadramento->obterProjetosApreciadosCnic([
            'a.IdPRONAC = ?' => $intIdPronac
        ])->current();

        $arrValue = [];
        if(!is_null($dadosEnquadramento)) {
            $arrValue = $dadosEnquadramento->toArray();
        }

        $arrValue['enquadramentoProjeto'] = $dbTableEnquadramento->obterProjetoAreaSegmento(
            [
                'a.IdPRONAC = ?' => $intIdPronac,
                'a.Situacao = ?' => $this->situacaoParaHomologacao
            ]
        )->current();

        $arrValue['parecer'] = $dbTableParecer->findBy([
            'TipoParecer' => '1',
            'idTipoAgente' => '1',
            'IdPRONAC' => $intIdPronac
        ]);
        $arrValue['acaoProjeto'] = $dbTableAcaoProjeto->findBy([
            'tpAnalise' => '1',
            'idPronac' => $intIdPronac
        ]);
        $arrValue['aparicaoComissario'] = $dbTableParecer->findBy([
            'TipoParecer' => '1',
            'idTipoAgente' => '6',
            'IdPRONAC' => $intIdPronac
        ]);
        $arrValue['parecerHomologacao'] = $dbTableHomologacao->getBy([
            'idPronac' => $intIdPronac,
            'tpHomologacao' => '1'
        ]);

        if (isset($arrValue['IdPRONAC'])) {
            $arrValue['idPronac'] = $arrValue['IdPRONAC'];
        }

        $this->view->arrValue = $arrValue;
        return $arrValue;
    }

    function gerarDocumentoAssinatura($intIdPronac)
    {
        $view = new Zend_View();
        $view->setScriptPath(__DIR__ . DIRECTORY_SEPARATOR . '../views/scripts/');
        $view->arrValue = $this->prepareData($intIdPronac);
        return $view->render('homologacao/partials/documento-assinatura.phtml');
    }

    public function iniciarFluxoAssinaturaAction()
    {
        if (!filter_input(INPUT_GET, 'idPronac')) {
            throw new Exception(
                "Identificador do projeto &eacute; necess&amp;aacute;rio para acessar essa funcionalidade."
            );
        }
        $get = Zend_Registry::get('get');
        $idPronac = $get->idPronac;

        $dbTableParecer = new Parecer();
        $parecer = $dbTableParecer->findBy([
            'TipoParecer' => '1',
            'idTipoAgente' => '1',
            'IdPRONAC' => $idPronac
        ]);

        if (count($parecer) < 1 || empty($parecer['IdParecer'])) {
            throw new Exception(
                "&Eacute; necess&amp;aacute;rio ao menos um parecer para iniciar o fluxo de assinatura."
            );
        }

        $objDbTableDocumentoAssinatura = new \Assinatura_Model_DbTable_TbDocumentoAssinatura();
        $documentoAssinatura = $objDbTableDocumentoAssinatura->obterProjetoDisponivelParaAssinatura(
            $idPronac,
            Assinatura_Model_DbTable_TbAssinatura::TIPO_ATO_HOMOLOGAR_PROJETO
        );

        $mensagem = "Opera&ccedil;&atilde;o realizada com sucesso!";
        if (count($documentoAssinatura) < 1) {
            $servicoDocumentoAssinatura = new \Application\Modules\Projeto\Service\Assinatura\DocumentoAssinatura(
                $idPronac,
                Assinatura_Model_DbTable_TbAssinatura::TIPO_ATO_HOMOLOGAR_PROJETO,
                $parecer['IdParecer']
            );
            $idDocumentoAssinatura = $servicoDocumentoAssinatura->iniciarFluxo();
        } else {
            $idDocumentoAssinatura = $documentoAssinatura['idDocumentoAssinatura'];
        }


        parent::message(
            $mensagem,
            "/assinatura/index/visualizar-projeto?idDocumentoAssinatura={$idDocumentoAssinatura}",
            "CONFIRM"
        );
    }
}
