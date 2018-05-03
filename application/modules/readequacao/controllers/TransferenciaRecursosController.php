<?php

class Readequacao_TransferenciaRecursosController extends MinC_Controller_Action_Abstract
{
    public function init()
    {
        parent::init();
        
        $idPronac = $this->_request->getParam('idPronac');
        if (strlen($idPronac) > 7) {
            $idPronac = Seguranca::dencrypt($idPronac);
        }
        $this->idPronac = $idPronac;
        $this->view->idPronac = $idPronac;
    }

    public function indexAction()
    {
        $this->view->projeto = $this->projeto;
    }

    public function listarProjetosRecebedoresAction()
    {
        $this->_helper->layout->disableLayout();
        
        $this->view->arrResult = [];        
    }

    public function incluirProjetoRecebedorAction()
    {
        
    }
    
    public function excluirProjetoRecebedorAction()
    {
        
    }
    
    public function finalizarSolicitacaoTransferenciaRecursosAction()
    {
        try {
            $tbReadequacao = new Readequacao_Model_tbReadequacao();

            // inclui
            
            $this->_helper->json(
                [
                    'readequacao' => $readequacao,
                    'resposta' => true
                ]
            );
        } catch (Exception $objException) {
            $this->_helper->json(
                [
                    'error ' => $objException->getMessage(),
                    'resposta' => false
                ]
            );
        }
    }
}