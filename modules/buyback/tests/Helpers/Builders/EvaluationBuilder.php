<?php

namespace Buyback\Tests\Helpers\Builders;

use TradeAppOne\Domain\Models\Tables\Device;
use Buyback\Models\Evaluation;
use Buyback\Models\Quiz;
use Illuminate\Support\Facades\DB;
use TradeAppOne\Domain\Models\Tables\Network;
use TradeAppOne\Tests\Helpers\Builders\NetworkBuilder;

class EvaluationBuilder
{

    protected $network;
    protected $quiz;
    protected $device;

    public function withNetwork(Network $network): EvaluationBuilder
    {
        $this->network = $network;
        return $this;
    }

    public function withQuiz(Quiz $quiz): EvaluationBuilder
    {
        $this->quiz = $quiz;
        return $this;
    }

    public function withDevice(Device $device): EvaluationBuilder
    {
        $this->device = $device;
        return $this;
    }

    public function generateEvaluationTimes(int $quantity)
    {
        $builded = collect();
        foreach (range(1, $quantity) as $index) {
            $builded->push($this->build());
        }
        return $builded;
    }

    public function build(): Evaluation
    {
        $networkEntity               = $this->network ?? (new NetworkBuilder())->build();
        $deviceEntity                = $this->device ?? (new DeviceBuilder())->withNetwork($networkEntity)->build();
        $deviceId                    = $this->getDeviceNetworkId($deviceEntity->id, $networkEntity->id);
        $quiz                        = $this->quiz ?? factory(Quiz::class)->create();
        $evaluation                  = factory(Evaluation::class)->make();
        $evaluation->deviceNetworkId = $deviceId;
        $evaluation->quiz()->associate($quiz)->save();

        return $evaluation;
    }

    protected function getDeviceNetworkId(string $deviceId, string $networkId)
    {
        return DB::table('devices_network')->where(['networkId' => $networkId, 'deviceId' => $deviceId])->first()
            ->id;
    }
}
