<?php

namespace Banner\Repositories;

interface RepositoryInterface
{
    public function save($attributes);

    public function find($id);

    public function edit($id, array $attributes);

    public function destroy($id);
}
