<?php


namespace TradeAppOne\Tests;


use Illuminate\Http\Response;
use TradeAppOne\Domain\Enumerators\Operations;
use TradeAppOne\Domain\Enumerators\Options;
use TradeAppOne\Domain\Models\Tables\Service;
use TradeAppOne\Domain\Models\Tables\ServiceOption;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\HierarchyBuilder;
use TradeAppOne\Tests\Helpers\Builders\NetworkBuilder;
use TradeAppOne\Tests\Helpers\Builders\PointOfSaleBuilder;
use TradeAppOne\Tests\Helpers\Builders\ServiceOptionsImportableBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;

class ServiceImportableFeatureTest extends TestCase
{
    use AuthHelper;

    private const FILE_NAME = 'autentica_habilitar';

    private static $endpointPrefix = 'management/enable/service-options';

    /** @test */
    public function should_return_200_with_import_model_service_options(): void
    {
        $user = (new UserBuilder())->build();

        $this
            ->withHeader('Authorization', $this->loginUser($user))
            ->get(self::$endpointPrefix)
            ->assertStatus(Response::HTTP_OK);
    }

    /** @test */
    public function should_return_201_when_success_import_file_to_enable_service_options(): void
    {
        $user    = (new UserBuilder())->build();

        $service = factory(Service::class)->create([
            'sector' => Operations::LINE_ACTIVATION,
            'operator' => Operations::CLARO,
            'operation' => Operations::CLARO_BANDA_LARGA
        ]);

        $network   = (new NetworkBuilder())->withServices($service)->build();
        $hierarchy = (new HierarchyBuilder())->build();

        factory(ServiceOption::class)->create([
            'action' => Options::AUTENTICA_VENDEDOR
        ]);

        $pointOfSale = (new PointOfSaleBuilder())
            ->withHierarchy($hierarchy)
            ->withNetwork($network)
            ->build();

        $pointOfSale->providerIdentifiers = '{"CLARO": "XPTO"}';
        $pointOfSale->save();

        $file = (new ServiceOptionsImportableBuilder())->buildFromArray([
            [
                'Codigo Pdv' => 'XPTO',
                'Opcao' => 'AUTENTICA_VENDEDOR',
                'Valor' => 1
            ]
        ]);

        $this->withHeader('Authorization', $this->loginUser($user))
            ->postJson(self::$endpointPrefix, [
                'file' => $file
            ])->assertStatus(Response::HTTP_CREATED);
    }

    /** @test */
    public function should_return_with_errors_when_point_of_sale_not_exists(): void
    {
        $user    = (new UserBuilder())->build();
        $service = factory(Service::class)->create([
            'sector' => Operations::LINE_ACTIVATION,
            'operator' => Operations::CLARO,
            'operation' => Operations::CLARO_BANDA_LARGA
        ]);

        $network   = (new NetworkBuilder())->withServices($service)->build();
        $hierarchy = (new HierarchyBuilder())->build();

        factory(ServiceOption::class)->create([
            'action' => Options::AUTENTICA_VENDEDOR
        ]);

        (new PointOfSaleBuilder())
            ->withHierarchy($hierarchy)
            ->withNetwork($network)
            ->build();

        $file = (new ServiceOptionsImportableBuilder())
            ->withFileName(self::FILE_NAME)
            ->buildFromArray([
                [
                    'Codigo Pdv' => 'XPTO',
                    'Opcao' => 'AUTENTICA_VENDEDOR',
                    'Valor' => 1
                ]
            ]);

        $this->withHeader('Authorization', $this->loginUser($user))
            ->postJson(self::$endpointPrefix, [
                'file' => $file
            ])->assertStatus(Response::HTTP_OK)
            ->assertSeeText('Erro')
            ->assertSeeText('Ponto de venda inexistente');

        $this->assertFileExists($file->getPath());
    }

    /** @test */
    public function should_return_with_errors_when_network_not_exists(): void
    {
        $user      = (new UserBuilder())->build();

        $network   = (new NetworkBuilder())->build();
        $hierarchy = (new HierarchyBuilder())->build();

        factory(ServiceOption::class)->create([
            'action' => Options::AUTENTICA_VENDEDOR
        ]);

        $pointOfSale = (new PointOfSaleBuilder())
            ->withHierarchy($hierarchy)
            ->withNetwork($network)
            ->build();

        $pointOfSale->providerIdentifiers = '{"CLARO": "XPTO"}';
        $pointOfSale->save();

        $file = (new ServiceOptionsImportableBuilder())
            ->withFileName(self::FILE_NAME)
            ->buildFromArray([
                [
                    'Codigo Pdv' => 'XPTO',
                    'Opcao' => 'AUTENTICA_VENDEDOR',
                    'Valor' => 1
                ]
            ]);

        $this->withHeader('Authorization', $this->loginUser($user))
            ->postJson(self::$endpointPrefix, [
                'file' => $file
            ])->assertStatus(Response::HTTP_OK)
            ->assertSeeText('Erro')
            ->assertSeeText('Não há serviços vinculados ao PDV e REDE para habilitar a opção selecionada.');
    }

    /** @test */
    public function should_return_with_errors_when_service_options_not_found(): void
    {
        $user    = (new UserBuilder())->build();

        $service = factory(Service::class)->create([
            'sector' => Operations::LINE_ACTIVATION,
            'operator' => Operations::CLARO,
            'operation' => Operations::CLARO_BANDA_LARGA
        ]);

        $network   = (new NetworkBuilder())->withServices($service)->build();
        $hierarchy = (new HierarchyBuilder())->build();

        $pointOfSale = (new PointOfSaleBuilder())
            ->withHierarchy($hierarchy)
            ->withNetwork($network)
            ->build();

        $pointOfSale->providerIdentifiers = '{"CLARO": "XPTO"}';
        $pointOfSale->save();

        $file = (new ServiceOptionsImportableBuilder())->buildFromArray([
            [
                'Codigo Pdv' => 'XPTO',
                'Opcao' => 'AUTENTICA_VENDEDOR',
                'Valor' => 1
            ]
        ]);

        $this->withHeader('Authorization', $this->loginUser($user))
            ->postJson(self::$endpointPrefix, [
                'file' => $file
            ])->assertStatus(Response::HTTP_OK)
            ->assertSeeText('Erro')
            ->assertSeeText('Opção de serviço não localizada.');
    }
}
