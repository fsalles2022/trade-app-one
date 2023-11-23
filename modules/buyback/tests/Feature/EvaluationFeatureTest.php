<?php

namespace Buyback\Tests\Feature;

use Buyback\Tests\Helpers\Builders\DeviceBuilder;
use Buyback\Tests\Helpers\Builders\EvaluationBuilder;
use Illuminate\Http\Response;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use TradeAppOne\Domain\Components\Helpers\CsvHelper;
use TradeAppOne\Domain\Importables\EvaluationImportable;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Tests\Helpers\AuthHelper;
use TradeAppOne\Tests\Helpers\Builders\HierarchyBuilder;
use TradeAppOne\Tests\Helpers\Builders\NetworkBuilder;
use TradeAppOne\Tests\Helpers\Builders\UserBuilder;
use League\Csv\Writer;
use TradeAppOne\Tests\TestCase;

class EvaluationFeatureTest extends TestCase
{
    use AuthHelper;
    protected $endpointPrefix = '/buyback/';

    public function setUp()
    {
        parent::setUp();
        Storage::fake('s3');
    }
    /** @test */
    public function post_import_should_return_201_CREATED_when_the_file_is_processed_correctly()
    {
        $network    = (new NetworkBuilder())->build();
        $user       = (new UserBuilder())->withNetwork($network)->build();
        $evaluation = (new EvaluationBuilder())->withNetwork($network)->build();

        $evaluationImportable = resolve(EvaluationImportable::class);
        $columns              = array_values($evaluationImportable->getColumns());
        $lines                = $evaluationImportable->getExample($network->slug, $evaluation);
        $file                 = CsvHelper::arrayToCsv([$columns, $lines]);

        $filePath = '/tmp/evaluation.csv';
        file_put_contents($filePath, $file->getContent());

        $response = $this
            ->withHeader('Authorization', $this->loginUser($user))
            ->postJson($this->endpointPrefix . 'import/evaluation', [
                'file' => new UploadedFile($filePath, 'evaluation.csv', null, null, null, true)
            ]);
        $response->assertStatus(Response::HTTP_CREATED);
    }

    /** @test */
    public function post_import_should_return_200_OK_when_the_file_is_partially_processed()
    {
        $user = (new UserBuilder())->build();

        $evaluationImportable = resolve(EvaluationImportable::class);
        $columns              = array_values($evaluationImportable->getColumns());
        $lines                = $evaluationImportable->getExample();
        $file                 = CsvHelper::arrayToCsv([$columns, $lines]);

        $filePath = '/tmp/evaluation.csv';
        file_put_contents($filePath, $file->getContent());

        $response = $this
            ->withHeader('Authorization', $this->loginUser($user))
            ->postJson($this->endpointPrefix . 'import/evaluation', [
                'file' => new UploadedFile($filePath, 'evaluation.csv', null, null, null, true)
            ]);

        $response->assertStatus(Response::HTTP_OK);
    }

    /** @test */
    public function post_import_should_return_422_UNPROCESSABLE_ENTITY_when_csv_file_is_valid_and_dont_have_content()
    {
        $user = (new UserBuilder())->build();

        $filePath = '/tmp/evaluation.csv';
        file_put_contents($filePath, "");

        $response = $this
            ->withHeader('Authorization', $this->loginUser($user))
            ->postJson($this->endpointPrefix . 'import/evaluation', [
                'file' => new UploadedFile($filePath, 'evaluation.csv', null, null, null, true)
            ]);
        $response->assertStatus(Response::HTTP_UNPROCESSABLE_ENTITY);
    }

    /** @test */
    public function post_import_should_return_400_BAD_REQUEST_when_csv_file_is_valid_and_has_an_invalid_structure()
    {
        $user = (new UserBuilder())->build();

        $columns = ['rede'];
        $lines   = ['Rede Exemplo'];
        $file    = CsvHelper::arrayToCsv([$columns, $lines]);

        $filePath = '/tmp/evaluation.csv';
        file_put_contents($filePath, $file->getContent());

        $response = $this
            ->withHeader('Authorization', $this->loginUser($user))
            ->postJson($this->endpointPrefix . 'import/evaluation', [
                'file' => new UploadedFile($filePath, 'evaluation.csv', null, null, null, true)
            ]);
        $response->assertStatus(Response::HTTP_BAD_REQUEST);
    }

    /** @test */
    public function get_import_should_return_200_with_csv_file()
    {
        $user = (new UserBuilder())->build();

        $response = $this
            ->withHeader('Authorization', $this->loginUser($user))
            ->json('GET', $this->endpointPrefix . 'import/evaluation');

        $response->assertStatus(Response::HTTP_OK);
        self::assertInstanceOf(Writer::class, $response->getOriginalContent());
    }

    /** @test */
    public function get_should_return_200_and_evaluation_devices()
    {
        $network = (new NetworkBuilder())->build();
        (new EvaluationBuilder())->withNetwork($network)->build();
        $user = (new UserBuilder())->withNetwork($network)->build();

        $response = $this
            ->withHeader('Authorization', $this->loginUser($user))
            ->get($this->endpointPrefix . 'devices-evaluations');

        $response->assertStatus(200);
        $response->assertJsonStructure(['data' => [
            '*' => [
                'id', 'goodValue', 'averageValue', 'defectValue', 'devicesNetwork' => [
                    'network' => ['label'],
                    'device' => ['label']
                ]]]]);
    }

    /** @test */
    public function get_should_return_user_network_devices_evaluation()
    {
        $network = (new NetworkBuilder())->build();
        (new EvaluationBuilder())->withNetwork($network)->build();
        $user = (new UserBuilder())->withNetwork($network)->build();

        (new EvaluationBuilder())->withNetwork($network)->build();
        (new EvaluationBuilder())->build();

        $response = $this
            ->withHeader('Authorization', $this->loginUser($user))
            ->get($this->endpointPrefix . 'devices-evaluations');

        $response->assertJsonCount(2, 'data');
    }

    /** @test */
    public function get_should_return_evaluation_belongs_to_user_network_filtered()
    {
        $network = factory(Network::class)->create([
            'label' => 'tradeup-group'
        ]);

        $user      = (new UserBuilder())->withNetwork($network)->build();
        $hierarchy = (new HierarchyBuilder())->withUser($user)->withNetwork($network)->build();
        (new EvaluationBuilder())->withNetwork($network)->build();


        $otherNetwork = factory(Network::class)->create([
            'label' => 'otherNetwork'
        ]);
        (new HierarchyBuilder())->withParent($hierarchy)->withNetwork($otherNetwork)->build();
        (new EvaluationBuilder())->withNetwork($otherNetwork)->build();

        $payload = [
          'network' => ['tradeup-group']
        ];

        $response = $this
            ->withHeader('Authorization', $this->loginUser($user))
            ->json('GET', $this->endpointPrefix . 'devices-evaluations', $payload);

        $response->assertJsonFragment(['label' => 'tradeup-group']);
    }

    /** @test */
    public function get_devices_evaluation_return_200_with_file()
    {
        $network     = (new NetworkBuilder())->build();
        $user        = (new UserBuilder())->withNetwork($network)->build();
        $device      = (new DeviceBuilder())->withNetwork($network)->build();
        $evaluations = (new EvaluationBuilder())->withNetwork($network)->withDevice($device)->build();

        $header = ['ID','Aparelho','Excelente','Bom','Regular','Rede'];
        $lines  = [
            $evaluations->id,
            $device->label,
            $evaluations->goodValue,
            $evaluations->averageValue,
            $evaluations->defectValue,
            $network->label
        ];

        $csvArray   = [$header, $lines];
        $fileExport = CsvHelper::arrayToCsv($csvArray)->getContent();

        $response = $this->authAs($user)
            ->get('/buyback/evaluations-export')
            ->assertStatus(Response::HTTP_OK);

        $this->assertContains($fileExport, $response->content());
    }
}
