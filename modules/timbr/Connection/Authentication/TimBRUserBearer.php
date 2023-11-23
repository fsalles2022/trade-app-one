<?php

namespace TimBR\Connection\Authentication;

use phpseclib\Crypt\RSA;
use phpseclib\Net\SSH2;
use TimBR\Components\Cookies;
use TimBR\Connection\TimBR;
use TimBR\Exceptions\TimBRAuthenticationFailed;
use TimBR\Exceptions\TimBRCodeGenerationFailed;

class TimBRUserBearer
{
    protected $suffix;
    /**
     * @var SSH2
     */
    private $ssh;

    private function configure(string $path = '')
    {
        $key = new RSA();
        if ($path) {
            $key->loadKey(file_get_contents($path));
        } else {
            $key->loadKey(config('integrations.timBR.tim-pem'));
        }


        $this->ssh = new SSH2('18.209.192.217', '2211');
        if (! $this->ssh->login('admin', $key)) {
            exit('Login Failed');
        }
    }

    public function requestBearer($network, $encryptedCpf, $redirectUri, $basic): string
    {
        $this->configure();
        // FIXME Usa os cookies e o code gerado para trocar pelo bearer do usuario
        try {
            $code = $this->requestCode($network, $encryptedCpf, $redirectUri);
            $this->ssh->write("cat {$network}_exchange_{$this->suffix}.txt\n");
            $cookies2   = $this->ssh->read('[prompt]');
            $formatted2 = Cookies::toCurlFormat($cookies2);
            $basic      = utf8_encode($basic);
            $this->ssh->write("curl -v -L -k -b '" . $formatted2 . "' " . TimBRCurlAuthRoutes::TOKENS() . " -H 'Authorization: " . $basic . "' " . TimBRCurlAuthRoutes::DEAFAULT_HEADERS . " -d 'grant_type=authorization_code&redirect_uri={$redirectUri}&code=" . $code . "' \n");
            $output = $this->ssh->read('[prompt]');
            $this->ssh->write("rm {$network}_{$this->suffix}.txt\n");
            $this->ssh->write("rm {$network}_exchange_{$this->suffix}.txt\n");
            $bearer = preg_split('["access_token":"|"\}\[]', $output, null, PREG_SPLIT_NO_EMPTY)[1];
            return 'Bearer ' . $bearer;
        } catch (\Exception $exception) {
            throw new TimBRAuthenticationFailed();
        }
    }

    private function requestCode($network, $encryptedCpf, $redirectUri): string
    {
        $this->suffix = rand(1, 1000000);
        $this->selectShellInPfSense();
        $firstCookies = $this->executeFirstInteraction($network, $redirectUri);
        $encryptedCpf = urlencode($encryptedCpf);
        try {
            $output = $this->executeSecondInteraction($network, $encryptedCpf, $firstCookies);
            return trim(preg_split('[\?code=|HTTP/1.1]', $output, null, PREG_SPLIT_NO_EMPTY)[9]);
        } catch (\ErrorException $exception) {
            throw new TimBRCodeGenerationFailed();
        }
    }

    private function selectShellInPfSense()
    {
        $this->ssh->write("8\n");
    }

    public function executeFirstInteraction(string $network, string $redirectUri): string
    {
        // FIXME Autoriza a autenticacao e retorna os cookies
        $curlCommand = "curl --cookie-jar {$network}_{$this->suffix}.txt -L -k '" . TimBRCurlAuthRoutes::AUTHORIZE() . "?response_type=code&client_id={$network}&redirect_uri={$redirectUri}&scope=" . TimBR::SCOPE_VENDA . "' " . TimBRCurlAuthRoutes::DEAFAULT_HEADERS . " \n";
        $this->ssh->write($curlCommand);
        $this->ssh->write("cat {$network}_{$this->suffix}.txt \n");
        $output       = $this->ssh->read('[prompt]');
        $cookies1     = Cookies::extractFromString($output);
        $arrayCookies = array_map(function ($cookie) {
            return trim($cookie['name']) . '=' . trim($cookie['value']);
        }, $cookies1);
        unset($arrayCookies[4]);
        $formatted = implode(';', $arrayCookies);
        return utf8_encode($formatted);
    }

    public function executeSecondInteraction($network, $encryptedCpf, $firstCookies)
    {
        // FIXME Troca os cookies pelo code e guarda os novos coookies
        $curlCommand = "curl -v -c {$network}_exchange_{$this->suffix}.txt -L -k -b '" . $firstCookies . "' " . TimBRCurlAuthRoutes::CRED_SUBMIT() . " " . TimBRCurlAuthRoutes::DEAFAULT_HEADERS . " -d 'content=" . $encryptedCpf . "&request_id=21222343666' \n";
        $this->ssh->write($curlCommand);
        return $this->ssh->read('[prompt]');
    }
}
