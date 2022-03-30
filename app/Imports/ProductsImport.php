<?php

namespace App\Imports;

use App\Product;
use Maatwebsite\Excel\Concerns\ToModel;

class ProductsImport implements ToModel
{
    /**
    * @param array $row
    *
    * @return \Illuminate\Database\Eloquent\Model|null
    */
    public function model(array $row)
    {
        return new Product([
            'id_product' => $row['0'],
            'clabe_sat' => $row['1'],
            'unidad_medida' => $row['2'],
        ]);
    }
}
