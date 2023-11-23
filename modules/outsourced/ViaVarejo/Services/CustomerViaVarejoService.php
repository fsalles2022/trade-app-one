<?php
declare(strict_types=1);

namespace Outsourced\ViaVarejo\Services;

use Illuminate\Support\Facades\Auth;
use Outsourced\Crafts\Customer\ValidatePartnerEmployeeInterface;
use Outsourced\ViaVarejo\Connections\ViaVarejoConnection;
use Outsourced\ViaVarejo\Exceptions\ViaVarejoExceptions;
use Illuminate\Http\Response as ResponseStatus;
use Throwable;
use TradeAppOne\Domain\Enumerators\NetworkEnum;
use TradeAppOne\Exceptions\BuildExceptions;

class CustomerViaVarejoService implements ValidatePartnerEmployeeInterface
{
    protected $viaVarejoConnection;

    public function __construct(ViaVarejoConnection $viaVarejoConnection)
    {
        $this->viaVarejoConnection = $viaVarejoConnection;
    }

    /**
     * @param string $cpf
     * @return string[]
     * @throws BuildExceptions
     * @throws Throwable
     */
    public function validatePartnerEmployee(string $cpf): array
    {
        $network = Auth::user()->pointsOfSale()->first()->network->slug;

        if ($network === NetworkEnum::VIA_VAREJO) {
            $response = $this->viaVarejoConnection->checkCpf($cpf);

            $existsClient = $response->get('cliente', false);

            throw_if(
                ! $existsClient &&
                $response->getStatus() === ResponseStatus::HTTP_OK,
                ViaVarejoExceptions::customerNotFound()
            );

            throw_if(
                ! $existsClient &&
                $response->getStatus() !== ResponseStatus::HTTP_OK,
                ViaVarejoExceptions::unavailable()
            );

            return ['message' => trans('via_varejo::messages.ViaVarejoCustomerFound')];
        }

        throw ViaVarejoExceptions::serviceNotAllowed();
    }
}
