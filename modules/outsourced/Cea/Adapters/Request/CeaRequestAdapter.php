<?php


namespace Outsourced\Cea\Adapters\Request;

use stdClass;

class CeaRequestAdapter
{
    protected $cardNumber;
    protected $chargeValue;
    protected $partnerIdent;
    protected $client;

    public function adapt()
    {
        $request                        = new stdClass();
        $request->NumeroCartao          = $this->cardNumber;
        $request->ValorCarga            = $this->chargeValue;
        $request->IdentificadorParceiro = $this->partnerIdent;
        $request->Cliente               = $this->client;

        return $request;
    }

    public function setCardNumber($cardNumber)
    {
        $this->cardNumber = $cardNumber;
        return $this;
    }

    public function setChargeValue($chargeValue)
    {
        $this->chargeValue = $chargeValue;
        return $this;
    }

    public function setPartnerIdentifier($partnerIdentifier)
    {
        $this->partnerIdent = $partnerIdentifier;
        return $this;
    }

    public function setClient(array $customer)
    {
        $cliente = new stdClass();

        $cliente->Identificacao                          = new stdClass();
        $cliente->Identificacao->PessoaFisica            = new stdClass();
        $cliente->Identificacao->PessoaFisica->CPF       = data_get($customer, 'cpf');
        $cliente->Identificacao->PessoaFisica->Nome      = data_get($customer, 'firstName');
        $cliente->Identificacao->PessoaFisica->Sobrenome = data_get($customer, 'lastName');

        $this->client = $cliente;

        return $this;
    }
}
