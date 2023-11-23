<?php

namespace VivoBR\Adapters\Response;

use TradeAppOne\Domain\Adapters\ResponseAdapterAbstract;
use TradeAppOne\Domain\HttpClients\Responseable;

class VivoBRSaleResponse extends ResponseAdapterAbstract
{
    public const COD_SUCCESS = 0;
    private const REPROVED   = 'REPROVADO';

    public const COD_ERRORS = [
        30009 //Falha na análise de crédito.
    ];

    public static function make(Responseable $response): VivoBRSaleResponse
    {
        return new static($response);
    }

    public function usesBiometrics(): bool
    {
        return filled($this->originalResponse->get('biometria'));
    }

    public function isCreditAnalysis(): bool
    {
        return filled($this->originalResponse->get('analiseCredito'));
    }

    public function isCreditAnalysisSuccess(): bool
    {
        return $this->isCreditAnalysis()
            ? $this->originalResponse->get('analiseCredito.codigo') === self::COD_SUCCESS
            : true;
    }

    public function getIdentifiers(): array
    {
        $idVenda      = $this->originalResponse->get('idVenda');
        $idServico    = $this->originalResponse->get('servicos.0.id');
        $biometriaUrl = $this->originalResponse->get('biometria');

        return $idVenda && $idServico
            ? compact('idVenda', 'idServico', $biometriaUrl ? 'biometriaUrl' : '')
            : [];
    }

    public function isSuccess(): bool
    {
        $successCode        = $this->originalResponse->get('codigo') === self::COD_SUCCESS;
        $reprovedActivation = $this->originalResponse->get('statusAtivacao') === self::REPROVED;

        if ($reprovedActivation) {
            return false;
        }

        return $successCode && $this->isCreditAnalysisSuccess();
    }

    public function statusActivation()
    {
        return $this->originalResponse->get('statusAtivacaos');
    }

    public function getAdapted(): array
    {
        $this->isSuccess()
            ? $this->pushDataWithSuccess()
            : $this->pushDataWithErrors();

        return $this->adapted;
    }

    public function pushDataWithSuccess(): void
    {
        $this->pushAttributes([
            'pid'                => $this->originalResponse->get('idVenda'),
            'data'               => $this->getOriginal(),
            'transportedMessage' => $this->getMessage()
        ]);
    }

    public function pushDataWithErrors(): void
    {
        $this->status = $this->defaultErrorStatus;

        $this->pushAttributes([
            'data'    => $this->getOriginal(),
            'message' => $this->getMessage()
        ]);
    }

    public function getMessage(): ?string
    {
        return $this->originalResponse->get('detalhes.0')
            ?? $this->originalResponse->get('analiseCredito.mensagem')
            ?? $this->originalResponse->get('mensagem');
    }

    public function shouldIgnoreErrors(): bool
    {
        $code     = $this->originalResponse->get('codigo');
        $analysis = true;

        if ($this->isCreditAnalysis()) {
            $analysisCode = $this->originalResponse->get('analiseCredito.codigo');
            $analysis     = ! in_array($analysisCode, self::COD_ERRORS, true);
        }

        return ! in_array($code, self::COD_ERRORS, true) && $analysis;
    }
}
