<?php

namespace AsperaPHP\API;

abstract class Model
{
    public function __construct($data = [])
    {
        foreach ($data as $key => $value) {
            $this->$key = $value;
        }
    }

    public static function fromObject($data): static
    {
        $model = new static();

        // Only map declared model properties from the response.
        foreach (get_class_vars(get_class($model)) as $key => $value) {
            // TODO: Maybe dynamically cast properties, ie package sender object
            if(isset($data->$key)) {
                $model->$key = $data->$key;
            }
        }

        return $model;
    }
}
