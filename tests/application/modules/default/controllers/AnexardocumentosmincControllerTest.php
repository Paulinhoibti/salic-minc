<?php
/**
 * Projeto_AnexardocumentosmincControllerTest
 *
 * @package
 * @author isaiassm <isaias1113@outlook.com>
 */
class AnexardocumentosmincControllerTest extends MinC_Test_ControllerActionTestCase
{
    public function setUp()
    {
        parent::setUp();
        $this->idPronac = '209649';

        $this->hashIdPronac = "501eac548e7d4fa987034573abc6e179MjAxNzEzZUA3NWVmUiEzNDUwb3RT";

        $this->autenticar();

        $this->resetRequest()
            ->resetResponse();

        $this->alterarPerfil(Autenticacao_Model_Grupos::COORDENADOR_ACOMPANHAMENTO, Orgaos::ORGAO_GEAR_SACAV);

        $this->resetRequest()
            ->resetResponse();
    }

    public function testindexAction()
    {
        $this->alterarPerfil(Autenticacao_Model_Grupos::COORDENADOR_ANALISE, Orgaos::ORGAO_GEAAP_SUAPI_DIAAPI);
        $this->dispatch('/anexardocumentosminc?idPronac=' . $this->idPronac);
        $this->assertUrl('default', 'anexardocumentosminc', 'index');
    }

    public function testexcluirAction()
    {
        $this->alterarPerfil(Autenticacao_Model_Grupos::COORDENADOR_ANALISE, Orgaos::ORGAO_GEAAP_SUAPI_DIAAPI);
        $this->dispatch('/anexardocumentosminc/excluir?idPronac=' . $this->idPronac);
        $this->assertUrl('default', 'anexardocumentosminc', 'excluir');
    }
}
