<?php

namespace Banner\Exceptions;

use Illuminate\Http\Response;

class BannerNotFound extends ApiException
{
    public function __construct()
    {
        $this->message = trans('banner::exceptions.BannerNotFound.message');
        $this->code    = 'BannerNotFound';
    }

    public function getHttpStatus()
    {
        return Response::HTTP_NOT_FOUND;
    }
}
