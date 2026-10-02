<?php

namespace Database\Seeders;

use Database\Seeders\Concerns\DisablesForeignKeys;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use DisablesForeignKeys;

    public function run(): void
    {
        $this->disableForeignKeys();

        $this->call([
            AdminSeeder::class,
            RolesPermisosSeeder::class,
            PermisosExtendidosSeeder::class,
            PermisosOperativosSeeder::class,
            CompromisosCobroPermisosSeeder::class,
            ComisionesPermisosSeeder::class,
            LeadsPermisosSeeder::class,

            WebConfiguracionSitioSeeder::class,
            WebRedesSocialesSeeder::class,
            WebBannerSeeder::class,
            WebExpedidoSeeder::class,
            WebGradoAcademicoSeeder::class,
            WebProfesionSeeder::class,
            WebMedioPagoSeeder::class,
            WebCategoriaProgramaSeeder::class,
            WebAreaSeeder::class,
            WebEtiquetaSeeder::class,
            CategoriaArticuloSeeder::class,
            BlogArticuloSeeder::class,
            WebCifraInstitucionalSeeder::class,
            WebGaleriaCategoriaSeeder::class,
            WebGaleriaVideoSeeder::class,
            WebDescargableSeeder::class,
            WebPopupSeeder::class,
            WebFaqSeeder::class,
            WebHitoInstitucionalSeeder::class,
            WebMenuSeeder::class,

            CategoriasNoticiaSeeder::class,
            TiposNormaSeeder::class,
            TiposDocumentoTransparenciaSeeder::class,
            TiposEventoSeeder::class,
            SecretariasSeeder::class,
            AutoridadesSeeder::class,
            WebAcreditacionSeeder::class,
            WebTestimonioSeeder::class,
            HistoriaInstitucionalSeeder::class,

            TipoUniversidadSeeder::class,
            CiudadSeeder::class,
            TipoProgramaSeeder::class,
            TipoPagoSeeder::class,

            UsuarioLegacySeeder::class,

            PlanAcademicoSeeder::class,
            ConvenioSeeder::class,
            GrupoAcademicoSeeder::class,
            DocentePerfilSeeder::class,
            SueldoDocenteSeeder::class,

            RequisitoAcademicoSeeder::class,
            FechaDocSeeder::class,

            CartaModeloSeeder::class,
            CartaSeeder::class,
            CartaGeneradaSeeder::class,

            CategoriaCampoSeeder::class,

            TesisSeeder::class,
            MonografiaSeeder::class,
            RevistaSeeder::class,
            RevistaCientificaSeeder::class,

            BoletinSeeder::class,

            WebEventoSeeder::class,

            FotoSeeder::class,

            WebFormularioSeeder::class,
            DescuentoPromocionSeeder::class,
            ResenaProgramaSeeder::class,

            DocumentoAcademicoSeeder::class,

            CertPlantillaSeeder::class,
            CertificadoDemoSeeder::class,
            CertVerificacionSeeder::class,
            DatosPruebaCertificadosSeeder::class,

            WebReglamentoProgramaSeeder::class,

            SpeechVentasSeeder::class,

            VendedoresSeeder::class,
            VendedorPermisosSeeder::class,
            EfectosEspecialesSeeder::class,

            CategoriaGastoSeeder::class,
            GastoSeeder::class,
            GastoRecurrenteSeeder::class,
            EmpleadoSeeder::class,
            PlanillaSeeder::class,
            ConfigHonorarioProgramaSeeder::class,

            CursoMigradoSeeder::class,

            IntentSeeder::class,

            TriviaSeeder::class,

        ]);

        $this->enableForeignKeys();
    }
}
