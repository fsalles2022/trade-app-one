<?php

declare(strict_types=1);

namespace SurfPernambucanas\Adapters;

abstract class PagtelActivationResponseAdapter extends PagtelResponseAdapter
{
    protected function getMessageResponse(): string
    {
        $errorsMessages = $this->getErrorMessage();

        if ($errorsMessages !== '') {
            return $errorsMessages;
        }

        $message = $this->originalResponse->get('message');

        return $message ?? parent::getMessageResponse();
    }

    public function isSuccess(): bool
    {
        return empty($this->originalResponse->get('error', [])) ? true : parent::isSuccess();
    }

    protected function getErrorMessage(): string
    {
        $errorsMessages = data_get($this->getErrors(), '*.message', []);

        return implode(', ', $errorsMessages);
    }

    /** @return array[] */
    protected function getErrors(): array
    {
        $errors = $this->originalResponse->get('error', []);

        return is_array($errors) ? $errors : [];
    }
}
