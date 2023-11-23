<?php

namespace Banner\Models;

class Banner extends Model
{
    public $id;
    public $image_desktop;
    public $image_mobile;
    public $image_tablet;
    public $start_at;
    public $end_at;
    public $slug;
    public $key;
    public $href;
    public $order;
    public $image_desktop_placeholder;
    public $image_tablet_placeholder;
    public $image_mobile_placeholder;


    public function rules(): array
    {
        return ['key' => 'required'];
    }
}
