<?php

namespace Uol\Adapters\Request;

use TradeAppOne\Domain\Models\Collections\Service;

class CadastrarMatriculaRequestAdapter
{
    public static function adapt(Service $service)
    {
        $product      = $service->product;
        $customer     = $service->customer;
        $name         = $customer['firstName'] . ' ' . $customer['lastName'];
        $cpf          = $customer['cpf'];
        $local        = $customer['local'];
        $number       = $customer['number'];
        $complement   = $customer['complement'];
        $neighborhood = $customer['neighborhood'];
        $city         = $customer['city'];
        $state        = $customer['state'];
        $zipCode      = $customer['zipCode'];
        $mainPhone    = $customer['mainPhone'];
        $email        = $customer['email'];
        $password     = $customer['password'];
        return array(
            'versao' => 1,
            'xmlParametros' =>
                "<?xml version=\"1.0\"?>
            <matricula>
                <id_curso>$product</id_curso>
                <formato>1</formato>
                <aluno>
                    <nome>$name</nome>
                    <cpf>$cpf</cpf>
                    <logradouro>$local</logradouro>
                    <numero>$number</numero>
                    <complemento>$complement</complemento>
                    <bairro>$neighborhood</bairro>
                    <cidade>$city</cidade>
                    <uf>$state</uf>
                    <cep>$zipCode</cep>
                    <telefone>$mainPhone</telefone>
                    <email>$email</email>
                    <senha>$password</senha>
                </aluno>         
            </matricula>"
        );
    }
}
