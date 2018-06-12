<?php

namespace MinC\Assinatura\Servico;

class Assinatura implements IServico
{
    public $post;
    public $identidadeUsuarioLogado;
    public $isMovimentarProjetoPorOrdemAssinatura = true;
    /**
     * @var \Assinatura_Model_DbTable_TbAssinatura $dbTableTbAssinatura
     */
    private $dbTableTbAssinatura;
    protected $idTipoDoAtoAdministrativo;

    function __construct(
        $post,
        $identidadeUsuarioLogado,
        $idTipoDoAtoAdministrativo = null
    )
    {
        $this->post = $post;
        $this->identidadeUsuarioLogado = $identidadeUsuarioLogado;
        $this->idTipoDoAtoAdministrativo = $idTipoDoAtoAdministrativo;
        $this->dbTableTbAssinatura = new \Assinatura_Model_DbTable_TbAssinatura();
    }

    public function assinarProjeto(\MinC\Assinatura\Model\Assinatura $modelAssinatura)
    {

        if (empty(trim($modelAssinatura->getDsManifestacao()))) {
            throw new \Exception ("Campo \"De acordo do Assinante\" &eacute; de preenchimento obrigat&oacute;rio.");
        }

        if (empty(trim($modelAssinatura->getIdPronac()))) {
            throw new \Exception ("O n&uacute;mero do projeto &eacute; obrigat&oacute;rio.");
        }

        if (empty(trim($modelAssinatura->getIdTipoDoAtoAdministrativo()))) {
            throw new \Exception ("O Tipo do Ato Administrativo &eacute; obrigat&oacute;rio.");
        }

        $servicoAutenticacao = new \MinC\Assinatura\Servico\Autenticacao(
            $this->post,
            $this->identidadeUsuarioLogado
        );
        $metodoAutenticacao = $servicoAutenticacao->obterMetodoAutenticacao();

        if(!$metodoAutenticacao->autenticar()) {
            throw new \Exception ("Os dados utilizados para autentica&ccedil;&atilde;o s&atilde;o inv&aacute;lidos.");
        }

        $usuario = $metodoAutenticacao->obterInformacoesAssinante();
        $objTbAtoAdministrativo = new \Assinatura_Model_DbTable_TbAtoAdministrativo();
        $dadosAtoAdministrativoAtual = $objTbAtoAdministrativo->obterAtoAdministrativoAtual(
            $modelAssinatura->getIdTipoDoAtoAdministrativo(),
            $modelAssinatura->getCodGrupo(),
            $modelAssinatura->getCodOrgao()
        );

        if (!$dadosAtoAdministrativoAtual) {
            throw new \Exception ("Usu&aacute;rio sem autoriza&ccedil;&atilde;o para assinar o documento.");
        }


        $modelAssinatura->setIdAssinante($usuario['usu_codigo']);
        $modelAssinatura->setIdOrdemDaAssinatura($dadosAtoAdministrativoAtual['idOrdemDaAssinatura']);
        $modelAssinatura->setIdAtoAdministrativo($dadosAtoAdministrativoAtual['idAtoAdministrativo']);

        $this->dbTableTbAssinatura->definirModeloAssinatura([
            'idAssinante' => $modelAssinatura->getIdAssinante(),
            'idPronac' => $modelAssinatura->getIdPronac(),
            'idAtoAdministrativo' => $dadosAtoAdministrativoAtual['idAtoAdministrativo'],
            'idDocumentoAssinatura' => $modelAssinatura->getIdDocumentoAssinatura()
        ]);
        if($this->dbTableTbAssinatura->isProjetoAssinado()) {
            throw new \Exception ("O documento j&aacute; foi assinado pelo usu&aacute;rio logado nesta fase atual.");
        }

        $dadosInclusaoAssinatura = array(
            'idPronac' => $modelAssinatura->getIdPronac(),
            'idAtoAdministrativo' => $modelAssinatura->getIdAtoAdministrativo(),
            'dtAssinatura' => $objTbAtoAdministrativo->getExpressionDate(),
            'idAssinante' => $modelAssinatura->getIdAssinante(),
            'dsManifestacao' => $modelAssinatura->getDsManifestacao(),
            'idDocumentoAssinatura' => $modelAssinatura->getIdDocumentoAssinatura()
        );


        $this->dbTableTbAssinatura->inserir($dadosInclusaoAssinatura);
        $codigoOrgaoDestino = $objTbAtoAdministrativo->obterProximoOrgaoDeDestino(
            $modelAssinatura->getIdTipoDoAtoAdministrativo(),
            $modelAssinatura->getIdOrdemDaAssinatura(),
            $modelAssinatura->getIdOrgaoSuperiorDoAssinante()
        );

        if($this->isMovimentarProjetoPorOrdemAssinatura && $codigoOrgaoDestino) {
            $this->movimentarProjetoAssinadoPorOrdemDeAssinatura($modelAssinatura);
        }
    }

    public function movimentarProjetoAssinadoPorOrdemDeAssinatura(\MinC\Assinatura\Model\Assinatura $modelAssinatura)
    {
        if (!$modelAssinatura->getIdOrdemDaAssinatura()) {
            throw new \Exception("A fase atual do projeto n&atilde;o permite movimentar o projeto.");
        }


        if(!$this->dbTableTbAssinatura->isProjetoAssinado($modelAssinatura)) {
            throw new \Exception ("O documento precisa ser assinado para que consiga ser movimentado.");
        }

        $objTbAtoAdministrativo = new \Assinatura_Model_DbTable_TbAtoAdministrativo();
        $codigoOrgaoDestino = $objTbAtoAdministrativo->obterProximoOrgaoDeDestino(
            $modelAssinatura->getIdTipoDoAtoAdministrativo(),
            $modelAssinatura->getIdOrdemDaAssinatura(),
            $modelAssinatura->getIdOrgaoSuperiorDoAssinante()
        );
        if (!$codigoOrgaoDestino) {
            throw new \Exception("A fase atual do projeto n&atilde;o permite movimentar o projeto.");
        }

        $objTbProjetos = new \Projeto_Model_DbTable_Projetos();
        $objTbProjetos->alterarOrgao($codigoOrgaoDestino, $modelAssinatura->getIdPronac());
    }

}