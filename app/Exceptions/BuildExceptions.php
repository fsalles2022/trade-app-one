<?php

namespace TradeAppOne\Exceptions;

use Illuminate\Http\Response;

class BuildExceptions extends \Exception
{
    protected $shortMessage;
    protected $message;
    protected $code;
    protected $description;
    protected $help;
    protected $transportedMessage;
    protected $httpCode;
    protected $transportedData;

    public function __construct(array $exception)
    {
        $this->shortMessage       = array_get($exception, 'shortMessage', 'internalError');
        $this->message            = array_get($exception, 'message', trans('exceptions.internal_error'));
        $this->description        = array_get($exception, 'description', '');
        $this->help               = array_get($exception, 'help', '');
        $this->transportedMessage = array_get($exception, 'transportedMessage', '');
        $this->httpCode           = array_get($exception, 'httpCode', Response::HTTP_UNPROCESSABLE_ENTITY);
        $this->transportedData    = data_get($exception, 'transportedData', '');

        $this->code = $this->shortMessage;
        parent::__construct();
    }

    public function render()
    {
        return response($this->getError(), $this->httpCode);
    }

    public function getError()
    {
        return [
            'shortMessage'       => $this->shortMessage,
            'message'            => $this->message,
            'description'        => $this->description,
            'help'               => $this->help,
            'transportedMessage' => $this->transportedMessage,
            'transportedData'    => $this->transportedData
        ];
    }

    public function getShortMessage()
    {
        return $this->shortMessage;
    }
}
