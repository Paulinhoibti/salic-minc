<?php

class Projeto_ConvenioController extends Projeto_GenericController
{
    private $idPronac;

    public function init()
    {
        parent::init();
        $this->validarPerfis();

        $this->idPronac = $this->_request->getParam("idPronac");
        if (strlen($this->idPronac) > 7) {
            $this->idPronac = Seguranca::dencrypt($this->idPronac);
        }

        if (empty($this->idPronac)) {
            throw new Exception("idPronac não informado");
        }

        $modelProjeto = new Projeto_Model_Menu();
        $this->view->menu = json_encode($modelProjeto->obterMenu($this->idPronac));
    }

    private function validarPerfis()
    {
        $auth = Zend_Auth::getInstance();

        $PermissoesGrupo = array();
        $PermissoesGrupo[] = 147;
        $PermissoesGrupo[] = 148;
        $PermissoesGrupo[] = 149;
        $PermissoesGrupo[] = 150;
        $PermissoesGrupo[] = 151;
        $PermissoesGrupo[] = 152;

//         isset($auth->getIdentity()->usu_codigo) ? parent::perfil(1, $PermissoesGrupo) : parent::perfil(4, $PermissoesGrupo);
    }

    public function indexAction()
    {
        $this->redirect("/projeto/index/listar");
    }

    public function visualizarNovoAction()
    {
       $vwDadosProjeto = new Projeto_Model_DbTable_VwConsultarDadosDoProjetoFNC();
       $this->view->dados = $vwDadosProjeto->obterDadosFnc($this->idPronac);
    }

    public function visualizarAction()
    {
        $vwDadosProjeto = new Projeto_Model_DbTable_VwConsultarDadosDoProjetoFNC();
        $projeto = $vwDadosProjeto->obterDadosFnc($this->idPronac);
        $this->view->dados = $projeto;
        $dbTableInabilitado = new Inabilitado();
        $proponenteInabilitado = $dbTableInabilitado->BuscarInabilitado($projeto['CNPJ_CPF']);
        $this->view->ProponenteInabilitado = ($proponenteInabilitado->Habilitado == 'I');
    }

    public function obterMenuAction() {

    }
}
